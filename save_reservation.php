<?php

require __DIR__ . '/db.php';
require_once __DIR__ . '/integrations.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php#booking');
    exit;
}

$services = app_services();

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = app_normalize_phone((string) ($_POST['phone'] ?? ''));
$date = trim($_POST['date'] ?? '');
$time = trim($_POST['time'] ?? '');
$service = trim($_POST['service'] ?? '');
$note = trim($_POST['note'] ?? '');
$gdpr = isset($_POST['gdpr']);

$errors = [];

if (!app_verify_csrf_token($_POST['csrf_token'] ?? null)) {
    $errors[] = 'Formulář už vypršel nebo se nepodařilo ověřit odeslání. Otevři prosím rezervaci znovu.';
}

if (!app_validate_booking_honeypot($_POST['website'] ?? null)) {
    $errors[] = 'Žádost se nepodařilo ověřit. Zkus to prosím znovu.';
}

if (!app_validate_booking_form_timing($_POST['form_started_at'] ?? null)) {
    $errors[] = 'Formulář potřebujeme odeslat znovu. Vyber prosím termín ještě jednou.';
}

if ($name === '') {
    $errors[] = 'Doplň prosím jméno, ať víme, pro koho termín držíme.';
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'E-mail nevypadá správně. Mrkni prosím, jestli v něm nechybí znak @ nebo koncovka.';
}

if (!app_phone_is_valid($phone)) {
    $errors[] = 'Telefon nevypadá jako platné číslo. Může být třeba +420 777 123 456.';
}

$dateObject = DateTime::createFromFormat('!Y-m-d', $date);
if (!$dateObject || $dateObject->format('Y-m-d') !== $date) {
    $errors[] = 'Vyber prosím datum z nabídky.';
}

$duration = isset($services[$service]) ? (int) $services[$service]['duration'] : 30;
$availableTimes = app_time_slots_for_duration($date, $duration);
if (!in_array($time, $availableTimes, true)) {
    $errors[] = 'Vybraný čas už není dostupný. Zkus prosím jiný volný čas.';
}

if (!isset($services[$service])) {
    $errors[] = 'Vyber prosím službu, na kterou chceš přijít.';
}

if (!$gdpr) {
    $errors[] = 'Potřebujeme souhlas se zpracováním údajů, abychom mohli rezervaci vyřídit.';
}

if ($dateObject) {
    $today = new DateTime('today');
    $lastBookableDate = app_booking_last_date();
    $appointment = DateTime::createFromFormat('Y-m-d H:i', $date . ' ' . $time);

    if ($dateObject < $today) {
        $errors[] = 'Tohle datum už je za námi. Vyber prosím některý z dalších dnů.';
    } elseif ($dateObject > $lastBookableDate) {
        $errors[] = 'Online jde vybrat termín nejvýše 7 dní dopředu.';
    } elseif ($appointment && $appointment <= new DateTime()) {
        $errors[] = 'Tenhle čas už proběhl. Vyber prosím pozdější termín.';
    }
}

if (empty($errors)) {
    $stmt = $pdo->prepare('SELECT time, service, duration FROM reservations WHERE date = :date');
    $stmt->execute([':date' => $date]);
    $reservationsForDate = $stmt->fetchAll();

    if (app_reservations_overlap($reservationsForDate, $time, $duration)) {
        $errors[] = 'Tenhle termín je čerstvě obsazený. Vyber prosím jiný čas.';
    }
}

if (empty($errors)) {
    $googleCalendarCheck = app_google_calendar_overlaps($date, $time, $duration);

    if (!empty($googleCalendarCheck['errors'])) {
        $errors[] = 'Dostupnost termínu se teď nepodařilo ověřit. Zkus to prosím za chvíli znovu.';
    } elseif (!empty($googleCalendarCheck['overlaps'])) {
        $errors[] = 'Tenhle termín je už obsazený v kalendáři. Vyber prosím jiný čas.';
    }
}

if (empty($errors)) {
    $price = (int) $services[$service]['price'];

    try {
        $pdo->exec('BEGIN IMMEDIATE TRANSACTION');

        $lockingStmt = $pdo->prepare('SELECT time, service, duration FROM reservations WHERE date = :date');
        $lockingStmt->execute([':date' => $date]);
        $freshReservationsForDate = $lockingStmt->fetchAll();

        if (app_reservations_overlap($freshReservationsForDate, $time, $duration)) {
            $pdo->rollBack();
            $errors[] = 'Tenhle termín právě někdo obsadil. Vyber prosím jiný čas.';
        } else {
        $stmt = $pdo->prepare('
            INSERT INTO reservations (name, email, phone, date, time, service, price, duration, note, gdpr_accepted, status, created_at)
            VALUES (:name, :email, :phone, :date, :time, :service, :price, :duration, :note, :gdpr_accepted, :status, :created_at)
        ');

        $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':phone' => $phone,
            ':date' => $date,
            ':time' => $time,
            ':service' => $service,
            ':price' => $price,
            ':duration' => $duration,
            ':note' => $note === '' ? null : $note,
            ':gdpr_accepted' => 1,
            ':status' => 'pending',
            ':created_at' => (new DateTime())->format('Y-m-d H:i:s'),
        ]);

        $pdo->commit();

        $reservationId = (int) $pdo->lastInsertId();
        $reservation = [
            'id' => $reservationId,
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'date' => $date,
            'time' => $time,
            'service' => $service,
            'price' => $price,
            'duration' => $duration,
            'note' => $note,
        ];

        $integrationResult = app_run_reservation_request_integrations($reservation);
        $updateStmt = $pdo->prepare('
            UPDATE reservations
            SET owner_email_sent = :owner_email_sent,
                customer_email_sent = :customer_email_sent,
                calendar_event_id = :calendar_event_id,
                integration_errors = :integration_errors
            WHERE id = :id
        ');
        $updateStmt->execute([
            ':owner_email_sent' => $integrationResult['owner_email_sent'] ? 1 : 0,
            ':customer_email_sent' => $integrationResult['customer_email_sent'] ? 1 : 0,
            ':calendar_event_id' => $integrationResult['calendar_event_id'],
            ':integration_errors' => empty($integrationResult['errors'])
                ? null
                : implode("\n", $integrationResult['errors']),
            ':id' => $reservationId,
        ]);
        }
    } catch (PDOException $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        if ($e->getCode() === '23000') {
            $errors[] = 'Tenhle termín právě někdo obsadil. Vyber prosím jiný čas.';
        } else {
            $errors[] = 'Žádost se nepodařilo uložit. Zkus to prosím znovu, případně se ozvi telefonicky.';
        }
    }
}

function h(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

$displayDate = $dateObject ? $dateObject->format('d.m.Y') : $date;
$priceLabel = isset($services[$service]) ? app_price_label($service) : '';
$businessPhone = app_business_phone_display();
$businessEmail = app_business_email();
$businessAddress = app_business_full_address_inline();

unset($_SESSION['booking_form_started_at']);

?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<?= app_head_assets() ?>
    <title><?= empty($errors) ? 'Žádost o rezervaci přijata' : 'Rezervaci je potřeba upravit' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-[#F4EFE7] text-[#2B211C]">
    <main class="min-h-screen px-4 py-8 sm:py-12">
        <div class="mx-auto mb-6 flex max-w-4xl items-center justify-between gap-4">
            <a href="index.php" class="text-xl font-extrabold tracking-tight transition hover:opacity-85">
                <span>Hair By</span>
                <span class="text-[#C08A3E]">ReneNeme</span>
            </a>
            <a href="tel:+420608419610" class="hidden rounded-full border border-[#D8C8B0] px-4 py-2 text-sm font-semibold text-[#2B211C] transition hover:border-[#C08A3E] hover:text-[#94642C] sm:inline-flex">
                Zavolat
            </a>
        </div>

        <div class="mx-auto w-full max-w-4xl overflow-hidden rounded-3xl border border-[#D8C8B0] bg-[#F5EDE1] shadow-2xl shadow-[rgba(43,33,28,0.14)]">
        <?php if (!empty($errors)): ?>
            <div class="grid md:grid-cols-[0.85fr_1.15fr]">
                <div class="bg-[#2B211C] px-6 py-8 text-[#F5EDE1] sm:px-8 md:py-10">
                    <div class="mb-5 inline-flex h-14 w-14 items-center justify-center rounded-full border border-[#7B2D26] bg-[#3A211E] text-[#F4B8B0]">
                        <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M12 8v5M12 17h.01" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"/>
                            <path d="M10.3 4.2h3.4L21 18.5 19.3 21H4.7L3 18.5 10.3 4.2Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <p class="text-xs font-bold uppercase tracking-[0.24em] text-[#D6A85E]">Ještě to doladíme</p>
                    <h1 class="mt-3 text-3xl font-extrabold leading-tight sm:text-4xl">Rezervaci se nepodařilo odeslat</h1>
                    <p class="mt-4 text-sm leading-6 text-[#EDE8DD]">
                        Některý údaj nesedí nebo termín mezitím přestal být volný. Mrkni na přehled vedle a zkus to prosím znovu.
                    </p>
                </div>
                <div class="px-6 py-8 sm:px-8 md:py-10">
                    <p class="text-xs font-bold uppercase tracking-[0.22em] text-[#725E4C]">Co je potřeba upravit</p>
                    <ul class="mt-4 space-y-2 text-sm">
                        <?php foreach ($errors as $error): ?>
                            <li class="rounded-2xl border border-[#D8C8B0] bg-[#F9F5EF] px-4 py-3"><?= h($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                        <a href="index.php#booking" class="inline-flex justify-center rounded-xl bg-[#C08A3E] px-5 py-3 text-sm font-semibold text-[#F5EDE1] shadow-md transition hover:bg-[#94642C]">
                            Upravit rezervaci
                        </a>
                        <a href="tel:+420608419610" class="inline-flex justify-center rounded-xl border border-[#4A3A30] px-5 py-3 text-sm font-semibold text-[#2B211C] transition hover:bg-[#2B211C] hover:text-[#F5EDE1]">
                            Zavolat
                        </a>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="grid md:grid-cols-[0.9fr_1.1fr]">
                <div class="bg-[linear-gradient(145deg,#2B211C,#4A3A30)] px-6 py-8 text-[#F5EDE1] sm:px-8 md:py-10">
                    <div class="mb-5 inline-flex h-16 w-16 items-center justify-center rounded-full bg-[#C08A3E] text-[#F5EDE1] shadow-lg ring-8 ring-[rgba(192,138,62,0.16)]">
                        <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M5 12.5l4.2 4.2L19 7" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                    <p class="text-xs font-bold uppercase tracking-[0.24em] text-[#D6A85E]">Žádost přijata</p>
                    <h1 class="mt-3 text-3xl font-extrabold leading-tight sm:text-4xl">Díky, <?= h($name) ?>. Termín držíme v přehledu.</h1>
                    <p class="mt-4 text-sm leading-6 text-[#EDE8DD]">
                        Rezervace teď čeká na potvrzení. Jakmile ji přijmeme, dorazí ti e-mail se shrnutím a termín bude pevně zapsaný.
                    </p>
                    <div class="mt-6 rounded-2xl border border-[rgba(241,200,121,0.18)] bg-[rgba(255,255,255,0.06)] p-4">
                        <p class="text-xs font-bold uppercase tracking-[0.2em] text-[#F1C879]">Co bude dál</p>
                        <div class="mt-4 space-y-3 text-sm text-[#EDE8DD]">
                            <div class="flex gap-3">
                                <span class="mt-1 h-2.5 w-2.5 shrink-0 rounded-full bg-[#D6A85E]"></span>
                                <p>Rezervaci zkontrolujeme v administraci.</p>
                            </div>
                            <div class="flex gap-3">
                                <span class="mt-1 h-2.5 w-2.5 shrink-0 rounded-full bg-[#D6A85E]"></span>
                                <p>Po potvrzení ti přijde e-mail se všemi údaji.</p>
                            </div>
                            <div class="flex gap-3">
                                <span class="mt-1 h-2.5 w-2.5 shrink-0 rounded-full bg-[#D6A85E]"></span>
                                <p>Kdyby bylo potřeba něco doladit, ozveme se.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-6 py-8 sm:px-8 md:py-10">
                    <p class="text-xs font-bold uppercase tracking-[0.22em] text-[#725E4C]">Shrnutí rezervace</p>
                    <div class="mt-4 grid gap-3 sm:grid-cols-2">
                        <div class="rounded-2xl border border-[#D8C8B0] bg-[#F9F5EF] p-4">
                            <p class="text-xs font-bold uppercase tracking-[0.18em] text-[#725E4C]">Služba</p>
                            <p class="mt-1 font-bold"><?= h($service) ?></p>
                            <?php if ($priceLabel !== ''): ?>
                                <p class="mt-1 text-sm text-[#5E4E41]"><?= h($priceLabel) ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="rounded-2xl border border-[#D8C8B0] bg-[#F9F5EF] p-4">
                            <p class="text-xs font-bold uppercase tracking-[0.18em] text-[#725E4C]">Termín</p>
                            <p class="mt-1 font-bold"><?= h($displayDate) ?> v <?= h($time) ?></p>
                            <p class="mt-1 text-sm text-[#5E4E41]"><?= (int) $duration ?> minut</p>
                        </div>
                        <div class="rounded-2xl border border-[#D8C8B0] bg-[#F9F5EF] p-4">
                            <p class="text-xs font-bold uppercase tracking-[0.18em] text-[#725E4C]">Kontakt</p>
                            <p class="mt-1 font-bold"><?= h($phone) ?></p>
                            <p class="mt-1 break-words text-sm text-[#5E4E41]"><?= h($email) ?></p>
                        </div>
                        <div class="rounded-2xl border border-[#D8C8B0] bg-[#F9F5EF] p-4">
                            <p class="text-xs font-bold uppercase tracking-[0.18em] text-[#725E4C]">Místo</p>
                            <p class="mt-1 font-bold">Hair By ReneNeme</p>
                            <p class="mt-1 text-sm text-[#5E4E41]"><?= h($businessAddress) ?></p>
                        </div>
                    </div>
                    <?php if ($note !== ''): ?>
                        <div class="mt-3 rounded-2xl border border-[#D8C8B0] bg-[#F9F5EF] p-4">
                            <p class="text-xs font-bold uppercase tracking-[0.18em] text-[#725E4C]">Přání ke střihu</p>
                            <p class="mt-1 text-sm leading-6 text-[#5E4E41]"><?= nl2br(h($note)) ?></p>
                        </div>
                    <?php endif; ?>
                    <div class="mt-5 rounded-2xl border border-[#D8C8B0] bg-white/70 p-4 text-sm leading-6 text-[#5E4E41]">
                        <p class="font-semibold text-[#2B211C]">Potřebuješ něco změnit?</p>
                        <p>Zavolej na <?= h($businessPhone) ?> nebo napiš na <?= h($businessEmail) ?>.</p>
                    </div>
                    <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                        <a href="index.php" class="inline-flex justify-center rounded-xl bg-[#C08A3E] px-5 py-3 text-sm font-semibold text-[#F5EDE1] shadow-md transition hover:bg-[#94642C]">
                            Zpět na web
                        </a>
                        <a href="index.php#booking" class="inline-flex justify-center rounded-xl border border-[#4A3A30] px-5 py-3 text-sm font-semibold text-[#2B211C] transition hover:bg-[#2B211C] hover:text-[#F5EDE1]">
                            Vytvořit další rezervaci
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        </div>
    </main>
</body>
</html>
