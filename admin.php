<?php

require __DIR__ . '/db.php';
require_once __DIR__ . '/integrations.php';

require_admin_auth();

function require_admin_auth(): void
{
    $user = getenv('ADMIN_USER') ?: 'admin';
    $password = getenv('ADMIN_PASSWORD') ?: 'rezervace2026';

    $givenUser = $_SERVER['PHP_AUTH_USER'] ?? '';
    $givenPassword = $_SERVER['PHP_AUTH_PW'] ?? '';

    if (!hash_equals($user, $givenUser) || !hash_equals($password, $givenPassword)) {
        header('WWW-Authenticate: Basic realm="Rezervace"');
        header('HTTP/1.0 401 Unauthorized');
        echo 'Pro vstup do administrace je potreba prihlaseni.';
        exit;
    }
}

function h(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function admin_time_label(string $time, int $duration): string
{
    $start = DateTime::createFromFormat('H:i', $time);
    if (!$start) {
        return $time;
    }

    $end = clone $start;
    $end->modify('+' . max(1, $duration) . ' minutes');

    return $start->format('H:i') . '-' . $end->format('H:i');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = (int) ($_POST['id'] ?? 0);

    if ($action === 'accept' && $id > 0) {
        $reservationStmt = $pdo->prepare('SELECT * FROM reservations WHERE id = :id');
        $reservationStmt->execute([':id' => $id]);
        $reservation = $reservationStmt->fetch();

        if ($reservation) {
            $errors = [];
            $duration = (int) ($reservation['duration'] ?? app_service_duration((string) $reservation['service']));
            $googleCalendarCheck = app_google_calendar_overlaps((string) $reservation['date'], (string) $reservation['time'], $duration);

            if (!empty($googleCalendarCheck['errors'])) {
                $errors = array_merge($errors, $googleCalendarCheck['errors']);
            } elseif (!empty($googleCalendarCheck['overlaps']) && empty($reservation['calendar_event_id'])) {
                $errors[] = 'Termín se překrývá s událostí v Google Kalendáři. Rezervace nebyla přijata.';
            }

            if (empty($errors)) {
                $integrationResult = app_run_reservation_acceptance_integrations($reservation);
                $errors = array_merge($errors, $integrationResult['errors']);

                $accepted = !empty($integrationResult['calendar_event_id']) || trim((string) getenv('GOOGLE_CALENDAR_ID')) === '';
                $updateStmt = $pdo->prepare('
                    UPDATE reservations
                    SET status = :status,
                        accepted_at = :accepted_at,
                        customer_email_sent = :customer_email_sent,
                        calendar_event_id = :calendar_event_id,
                        integration_errors = :integration_errors
                    WHERE id = :id
                ');
                $updateStmt->execute([
                    ':status' => $accepted ? 'accepted' : (string) ($reservation['status'] ?? 'pending'),
                    ':accepted_at' => $accepted ? (new DateTime())->format('Y-m-d H:i:s') : ($reservation['accepted_at'] ?? null),
                    ':customer_email_sent' => !empty($integrationResult['customer_email_sent']) ? 1 : (int) ($reservation['customer_email_sent'] ?? 0),
                    ':calendar_event_id' => $integrationResult['calendar_event_id'] ?? $reservation['calendar_event_id'],
                    ':integration_errors' => empty($errors)
                        ? ($reservation['integration_errors'] ?? null)
                        : trim((string) ($reservation['integration_errors'] ?? '') . "\n" . implode("\n", $errors)),
                    ':id' => $id,
                ]);
            } else {
                $updateStmt = $pdo->prepare('
                    UPDATE reservations
                    SET integration_errors = :integration_errors
                    WHERE id = :id
                ');
                $updateStmt->execute([
                    ':integration_errors' => trim((string) ($reservation['integration_errors'] ?? '') . "\n" . implode("\n", $errors)),
                    ':id' => $id,
                ]);
            }
        }
    }

    if ($action === 'delete' && $id > 0) {
        $eventStmt = $pdo->prepare('SELECT calendar_event_id FROM reservations WHERE id = :id');
        $eventStmt->execute([':id' => $id]);
        $eventId = $eventStmt->fetchColumn();
        app_delete_google_calendar_event(is_string($eventId) ? $eventId : null);

        $stmt = $pdo->prepare('DELETE FROM reservations WHERE id = :id');
        $stmt->execute([':id' => $id]);
    }

    $redirect = 'admin.php';
    if (!empty($_POST['date'])) {
        $redirect .= '?date=' . urlencode((string) $_POST['date']);
    }

    header('Location: ' . $redirect);
    exit;
}

$date = trim($_GET['date'] ?? '');
$dateObject = DateTime::createFromFormat('!Y-m-d', $date);
$hasDateFilter = $dateObject && $dateObject->format('Y-m-d') === $date;

if ($hasDateFilter) {
    $stmt = $pdo->prepare('SELECT * FROM reservations WHERE date = :date ORDER BY time ASC');
    $stmt->execute([':date' => $date]);
} else {
    $today = (new DateTime('today'))->format('Y-m-d');
    $stmt = $pdo->prepare('SELECT * FROM reservations WHERE date >= :today ORDER BY date ASC, time ASC');
    $stmt->execute([':today' => $today]);
}

$reservations = $stmt->fetchAll();

$countStmt = $pdo->query('SELECT COUNT(*) FROM reservations');
$totalReservations = (int) $countStmt->fetchColumn();
$googleCalendarId = trim((string) getenv('GOOGLE_CALENDAR_ID'));
$googleCalendarOpenUrl = $googleCalendarId !== ''
    ? 'https://calendar.google.com/calendar/u/0/r?cid=' . rawurlencode($googleCalendarId)
    : '';
$calendarStart = $hasDateFilter ? clone $dateObject : new DateTime('today');
$calendarDays = [];
$calendarErrors = [];

for ($i = 0; $i < 7; $i++) {
    $day = clone $calendarStart;
    $day->modify('+' . $i . ' days');
    $dayDate = $day->format('Y-m-d');

    $dayStmt = $pdo->prepare('SELECT * FROM reservations WHERE date = :date ORDER BY time ASC');
    $dayStmt->execute([':date' => $dayDate]);
    $localReservations = $dayStmt->fetchAll();

    $entries = array_map(static function (array $reservation): array {
        $status = (string) ($reservation['status'] ?? 'accepted');

        return [
            'time' => (string) $reservation['time'],
            'duration' => (int) ($reservation['duration'] ?? app_service_duration((string) $reservation['service'])),
            'title' => (string) $reservation['service'],
            'detail' => ($status === 'pending' ? 'Čeká: ' : '') . (string) $reservation['name'],
            'source' => $status === 'pending' ? 'pending' : 'reservation',
        ];
    }, $localReservations);

    $googleBusy = app_google_calendar_busy_reservations_for_date($dayDate);
    if (!empty($googleBusy['errors'])) {
        $calendarErrors = array_merge($calendarErrors, $googleBusy['errors']);
    }

    foreach ($googleBusy['reservations'] as $busy) {
        if (app_reservations_overlap($localReservations, (string) $busy['time'], (int) $busy['duration'])) {
            continue;
        }

        $entries[] = [
            'time' => (string) $busy['time'],
            'duration' => (int) $busy['duration'],
            'title' => 'Obsazeno',
            'detail' => 'Google Kalendář',
            'source' => 'google',
        ];
    }

    usort($entries, static fn(array $a, array $b): int => strcmp($a['time'], $b['time']));

    $calendarDays[] = [
        'date' => $dayDate,
        'label' => $day->format('d.m.'),
        'weekday' => ['Po', 'Út', 'St', 'Čt', 'Pá', 'So', 'Ne'][(int) $day->format('N') - 1],
        'entries' => $entries,
    ];
}

?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Rezervace</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#1F1B18] text-[#F5EDE1] min-h-screen">
    <main class="max-w-6xl mx-auto py-10 px-4">
        <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-8">
            <div>
                <p class="text-xs uppercase tracking-[0.3em] text-[#C9BFA7] mb-2">Administrace</p>
                <h1 class="text-3xl font-bold">Rezervace</h1>
                <p class="text-sm text-[#D8C8B0] mt-2">Celkem uložených rezervací: <?= $totalReservations ?></p>
            </div>
            <a href="index.php" class="inline-flex items-center justify-center rounded-full border border-[#6A654E] px-4 py-2 text-sm hover:bg-[#2A231E]">
                Zpět na web
            </a>
        </div>

        <form method="get" class="mb-6 flex flex-col sm:flex-row gap-3 sm:items-center">
            <label for="date" class="text-sm">Vybrat datum:</label>
            <input
                type="date"
                id="date"
                name="date"
                value="<?= h($hasDateFilter ? $date : '') ?>"
                class="px-3 py-2 rounded bg-[#3F332A] border border-[#6A654E] text-[#F5EDE1]"
            >
            <button class="px-4 py-2 rounded bg-[#C9BFA7] text-[#1F1B18] font-semibold">Filtrovat</button>
            <a href="admin.php" class="text-sm underline">Zobrazit budoucí rezervace</a>
        </form>

        <section class="mb-6 overflow-hidden rounded-2xl border border-[#6A654E] bg-[#2A231E] shadow-2xl">
            <div class="flex flex-col gap-3 border-b border-[#3F332A] px-4 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-xs uppercase tracking-[0.24em] text-[#C9BFA7]">Kalendář</p>
                    <h2 class="mt-1 text-xl font-bold">Přehled příštích 7 dnů</h2>
                </div>
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                    <?php if ($googleCalendarId !== ''): ?>
                        <p class="rounded-full border border-[#6A654E] px-3 py-1 text-xs text-[#D8C8B0]">Synchronizace aktivní</p>
                        <a
                            href="<?= h($googleCalendarOpenUrl) ?>"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex items-center justify-center rounded-full bg-[#C9BFA7] px-4 py-2 text-sm font-semibold text-[#1F1B18] transition hover:bg-[#F5EDE1]"
                        >
                            Otevřít v Google Kalendáři
                        </a>
                    <?php else: ?>
                        <p class="rounded-full border border-[#7B2D26] px-3 py-1 text-xs text-[#F4B8B0]">Kalendář není nastavený</p>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (!empty($calendarErrors)): ?>
                <div class="border-b border-[#3F332A] bg-[#3A211E] px-4 py-3 text-sm text-[#F4B8B0]">
                    Google Kalendář se teď nepodařilo načíst: <?= h(implode(' ', array_unique($calendarErrors))) ?>
                </div>
            <?php endif; ?>

            <div class="grid gap-px bg-[#3F332A] md:grid-cols-7">
                <?php foreach ($calendarDays as $calendarDay): ?>
                    <article class="min-h-44 bg-[#241E1A] p-3">
                        <div class="mb-3 flex items-baseline justify-between gap-2">
                            <div>
                                <p class="text-xs font-bold uppercase tracking-[0.18em] text-[#C9BFA7]"><?= h($calendarDay['weekday']) ?></p>
                                <h3 class="mt-1 text-lg font-bold"><?= h($calendarDay['label']) ?></h3>
                            </div>
                            <a href="admin.php?date=<?= h($calendarDay['date']) ?>" class="text-xs underline text-[#D8C8B0]">detail</a>
                        </div>

                        <?php if (empty($calendarDay['entries'])): ?>
                            <p class="rounded-xl border border-[#3F332A] px-3 py-2 text-xs text-[#8F8373]">Volno</p>
                        <?php else: ?>
                            <div class="space-y-2">
                                <?php foreach ($calendarDay['entries'] as $entry): ?>
                                    <div class="rounded-xl border px-3 py-2 text-xs <?= $entry['source'] === 'google' ? 'border-[#735A31] bg-[#332A1F]' : ($entry['source'] === 'pending' ? 'border-[#8A6A2F] bg-[#3A2F20]' : 'border-[#6A654E] bg-[#2A231E]') ?>">
                                        <p class="font-bold text-[#F5EDE1]"><?= h(admin_time_label($entry['time'], $entry['duration'])) ?></p>
                                        <p class="mt-1 text-[#D8C8B0]"><?= h($entry['title']) ?></p>
                                        <p class="mt-0.5 text-[#9F927E]"><?= h($entry['detail']) ?></p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>

        <?php if (empty($reservations)): ?>
            <div class="rounded-xl border border-[#6A654E] bg-[#2A231E] p-5">
                <p>Žádné rezervace nenalezeny.</p>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto border border-[#6A654E] rounded-xl">
                <table class="min-w-full text-sm">
                    <thead class="bg-[#3F332A]">
                        <tr>
                            <th class="px-3 py-2 border-b border-[#6A654E] text-left">Datum</th>
                            <th class="px-3 py-2 border-b border-[#6A654E] text-left">Čas</th>
                            <th class="px-3 py-2 border-b border-[#6A654E] text-left">Jméno</th>
                            <th class="px-3 py-2 border-b border-[#6A654E] text-left">Kontakt</th>
                            <th class="px-3 py-2 border-b border-[#6A654E] text-left">Služba</th>
                            <th class="px-3 py-2 border-b border-[#6A654E] text-left">Stav</th>
                            <th class="px-3 py-2 border-b border-[#6A654E] text-left">Délka</th>
                            <th class="px-3 py-2 border-b border-[#6A654E] text-left">Cena</th>
                            <th class="px-3 py-2 border-b border-[#6A654E] text-left">Integrace</th>
                            <th class="px-3 py-2 border-b border-[#6A654E] text-left">Poznámka</th>
                            <th class="px-3 py-2 border-b border-[#6A654E] text-left">Akce</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reservations as $reservation): ?>
                            <tr class="odd:bg-[#2A231E] even:bg-[#241E1A] align-top">
                                <td class="px-3 py-2 border-b border-[#3F332A] whitespace-nowrap"><?= h($reservation['date']) ?></td>
                                <td class="px-3 py-2 border-b border-[#3F332A] whitespace-nowrap"><?= h($reservation['time']) ?></td>
                                <td class="px-3 py-2 border-b border-[#3F332A]"><?= h($reservation['name']) ?></td>
                                <td class="px-3 py-2 border-b border-[#3F332A]">
                                    <div><?= h($reservation['phone']) ?></div>
                                    <div class="text-xs text-[#C9BFA7]"><?= h($reservation['email']) ?></div>
                                </td>
                                <td class="px-3 py-2 border-b border-[#3F332A]"><?= h($reservation['service']) ?></td>
                                <td class="px-3 py-2 border-b border-[#3F332A] whitespace-nowrap">
                                    <?php if (($reservation['status'] ?? 'accepted') === 'pending'): ?>
                                        <span class="rounded-full border border-[#8A6A2F] bg-[#3A2F20] px-2 py-1 text-xs text-[#F1C879]">Čeká</span>
                                    <?php else: ?>
                                        <span class="rounded-full border border-[#496A45] bg-[#21351F] px-2 py-1 text-xs text-[#BFE3B5]">Přijato</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-3 py-2 border-b border-[#3F332A] whitespace-nowrap"><?= (int) ($reservation['duration'] ?? app_service_duration($reservation['service'])) ?> min</td>
                                <td class="px-3 py-2 border-b border-[#3F332A] whitespace-nowrap"><?= h(app_price_label($reservation['service']) ?: ((string) $reservation['price'] . ' Kč')) ?></td>
                                <td class="px-3 py-2 border-b border-[#3F332A] min-w-44">
                                    <div class="text-xs leading-5">
                                        <div>Majitel e-mail: <?= !empty($reservation['owner_email_sent']) ? 'ano' : 'ne' ?></div>
                                        <div>Zákazník e-mail: <?= !empty($reservation['customer_email_sent']) ? 'ano' : 'ne' ?></div>
                                        <div>Kalendář: <?= !empty($reservation['calendar_event_id']) ? 'ano' : 'ne' ?></div>
                                        <?php if (!empty($reservation['integration_errors'])): ?>
                                            <details class="mt-1 text-[#F4B8B0]">
                                                <summary class="cursor-pointer">Chyba</summary>
                                                <pre class="mt-1 whitespace-pre-wrap text-[11px]"><?= h($reservation['integration_errors']) ?></pre>
                                            </details>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="px-3 py-2 border-b border-[#3F332A]"><?= nl2br(h($reservation['note'] ?? '')) ?></td>
                                <td class="px-3 py-2 border-b border-[#3F332A]">
                                    <div class="flex flex-col gap-2">
                                    <?php if (($reservation['status'] ?? 'accepted') === 'pending'): ?>
                                        <form method="post" onsubmit="return confirm('Přijmout rezervaci a zapsat ji do Google Kalendáře?');">
                                            <input type="hidden" name="action" value="accept">
                                            <input type="hidden" name="id" value="<?= (int) $reservation['id'] ?>">
                                            <input type="hidden" name="date" value="<?= h($hasDateFilter ? $date : '') ?>">
                                            <button class="rounded bg-[#C9BFA7] px-3 py-1.5 text-xs font-semibold text-[#1F1B18] hover:bg-[#F5EDE1]">
                                                Přijmout
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                    <form method="post" onsubmit="return confirm('Opravdu smazat rezervaci? Pokud má událost v Google Kalendáři, smaže se také.');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= (int) $reservation['id'] ?>">
                                        <input type="hidden" name="date" value="<?= h($hasDateFilter ? $date : '') ?>">
                                        <button class="rounded bg-[#7B2D26] px-3 py-1.5 text-xs font-semibold hover:bg-[#9E382F]">
                                            Smazat
                                        </button>
                                    </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>
