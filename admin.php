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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = (int) ($_POST['id'] ?? 0);

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
                                    <form method="post" onsubmit="return confirm('Opravdu smazat rezervaci?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= (int) $reservation['id'] ?>">
                                        <input type="hidden" name="date" value="<?= h($hasDateFilter ? $date : '') ?>">
                                        <button class="rounded bg-[#7B2D26] px-3 py-1.5 text-xs font-semibold hover:bg-[#9E382F]">
                                            Smazat
                                        </button>
                                    </form>
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
