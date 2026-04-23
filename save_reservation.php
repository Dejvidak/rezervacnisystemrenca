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
$phone = trim($_POST['phone'] ?? '');
$date = trim($_POST['date'] ?? '');
$time = trim($_POST['time'] ?? '');
$service = trim($_POST['service'] ?? '');
$note = trim($_POST['note'] ?? '');
$gdpr = isset($_POST['gdpr']);

$errors = [];

if ($name === '') {
    $errors[] = 'Jméno je povinné.';
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Zadej platný e-mail.';
}

if ($phone === '' || !preg_match('/^[0-9+()\/\s.-]{6,30}$/', $phone)) {
    $errors[] = 'Zadej platný telefon.';
}

$dateObject = DateTime::createFromFormat('!Y-m-d', $date);
if (!$dateObject || $dateObject->format('Y-m-d') !== $date) {
    $errors[] = 'Zadej platné datum.';
}

$duration = isset($services[$service]) ? (int) $services[$service]['duration'] : 30;
$availableTimes = app_time_slots_for_duration($date, $duration);
if (!in_array($time, $availableTimes, true)) {
    $errors[] = 'Vybraný čas není dostupný.';
}

if (!isset($services[$service])) {
    $errors[] = 'Vyber platnou službu.';
}

if (!$gdpr) {
    $errors[] = 'Musíš souhlasit se zpracováním osobních údajů.';
}

if ($dateObject) {
    $today = new DateTime('today');
    $lastBookableDate = app_booking_last_date();
    $appointment = DateTime::createFromFormat('Y-m-d H:i', $date . ' ' . $time);

    if ($dateObject < $today) {
        $errors[] = 'Nemůžeš si rezervovat termín v minulosti.';
    } elseif ($dateObject > $lastBookableDate) {
        $errors[] = 'Rezervaci je možné vytvořit nejvýše 7 dní dopředu.';
    } elseif ($appointment && $appointment <= new DateTime()) {
        $errors[] = 'Nemůžeš si rezervovat čas, který už proběhl.';
    }
}

if (empty($errors)) {
    $stmt = $pdo->prepare('SELECT time, service, duration FROM reservations WHERE date = :date');
    $stmt->execute([':date' => $date]);
    $reservationsForDate = $stmt->fetchAll();

    if (app_reservations_overlap($reservationsForDate, $time, $duration)) {
        $errors[] = 'Tenhle termín je už obsazený. Vyber prosím jiný čas.';
    }
}

if (empty($errors)) {
    $price = (int) $services[$service]['price'];

    try {
        $stmt = $pdo->prepare('
            INSERT INTO reservations (name, email, phone, date, time, service, price, duration, note, gdpr_accepted, created_at)
            VALUES (:name, :email, :phone, :date, :time, :service, :price, :duration, :note, :gdpr_accepted, :created_at)
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
            ':created_at' => (new DateTime())->format('Y-m-d H:i:s'),
        ]);

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

        $integrationResult = app_run_reservation_integrations($reservation);
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
    } catch (PDOException $e) {
        if ($e->getCode() === '23000') {
            $errors[] = 'Tenhle termín je už obsazený. Vyber prosím jiný čas.';
        } else {
            $errors[] = 'Rezervaci se nepodařilo uložit. Zkus to prosím znovu.';
        }
    }
}

function h(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

$displayDate = $dateObject ? $dateObject->format('d.m.Y') : $date;
$priceLabel = isset($services[$service]) ? app_price_label($service) : '';

?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= empty($errors) ? 'Potvrzení rezervace' : 'Chyba rezervace' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-[#F4EFE7] text-[#2B211C]">
    <main class="flex min-h-screen items-center justify-center px-4 py-10">
        <div class="w-full max-w-2xl overflow-hidden rounded-lg border border-[#D8C8B0] bg-[#F5EDE1] shadow-2xl shadow-[rgba(43,33,28,0.14)]">
        <?php if (!empty($errors)): ?>
            <div class="bg-[#2B211C] px-5 py-6 text-[#F5EDE1] sm:px-8">
                <p class="text-xs font-bold uppercase tracking-[0.24em] text-[#D6A85E]">Rezervace nebyla uložena</p>
                <h1 class="mt-2 text-3xl font-extrabold">Ještě to chce upravit</h1>
                <p class="mt-3 max-w-xl text-sm leading-6 text-[#EDE8DD]">
                    Něco ve formuláři nesedí. Mrkni na chyby níže a zkus rezervaci odeslat znovu.
                </p>
            </div>
            <div class="px-5 py-6 sm:px-8">
                <ul class="space-y-2 text-sm">
                    <?php foreach ($errors as $error): ?>
                        <li class="rounded-lg border border-[#D8C8B0] bg-[#F9F5EF] px-4 py-3"><?= h($error) ?></li>
                    <?php endforeach; ?>
                </ul>
                <a href="index.php#booking" class="mt-6 inline-flex rounded-lg bg-[#C08A3E] px-5 py-3 text-sm font-semibold text-[#F5EDE1] shadow-md transition hover:bg-[#94642C]">
                    Zpět na formulář
                </a>
            </div>
        <?php else: ?>
            <div class="bg-[#2B211C] px-5 py-7 text-[#F5EDE1] sm:px-8">
                <div class="mb-5 inline-flex h-12 w-12 items-center justify-center rounded-full bg-[#C08A3E] text-[#F5EDE1] shadow-lg">
                    <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M5 12.5l4.2 4.2L19 7" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </div>
                <p class="text-xs font-bold uppercase tracking-[0.24em] text-[#D6A85E]">Rezervace potvrzena</p>
                <h1 class="mt-2 text-3xl font-extrabold">Díky, <?= h($name) ?></h1>
                <p class="mt-3 max-w-xl text-sm leading-6 text-[#EDE8DD]">
                    Termín máme uložený. Shrnutí rezervace jsme poslali na e-mail, takže ho budeš mít po ruce.
                </p>
            </div>
            <div class="px-5 py-6 sm:px-8">
                <div class="grid gap-3 sm:grid-cols-2">
                    <div class="rounded-lg border border-[#D8C8B0] bg-[#F9F5EF] p-4">
                        <p class="text-xs font-bold uppercase tracking-[0.18em] text-[#725E4C]">Služba</p>
                        <p class="mt-1 font-bold"><?= h($service) ?></p>
                        <?php if ($priceLabel !== ''): ?>
                            <p class="mt-1 text-sm text-[#5E4E41]"><?= h($priceLabel) ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="rounded-lg border border-[#D8C8B0] bg-[#F9F5EF] p-4">
                        <p class="text-xs font-bold uppercase tracking-[0.18em] text-[#725E4C]">Termín</p>
                        <p class="mt-1 font-bold"><?= h($displayDate) ?> v <?= h($time) ?></p>
                        <p class="mt-1 text-sm text-[#5E4E41]"><?= (int) $duration ?> minut</p>
                    </div>
                    <div class="rounded-lg border border-[#D8C8B0] bg-[#F9F5EF] p-4">
                        <p class="text-xs font-bold uppercase tracking-[0.18em] text-[#725E4C]">Kontakt</p>
                        <p class="mt-1 font-bold"><?= h($phone) ?></p>
                        <p class="mt-1 break-words text-sm text-[#5E4E41]"><?= h($email) ?></p>
                    </div>
                    <div class="rounded-lg border border-[#D8C8B0] bg-[#F9F5EF] p-4">
                        <p class="text-xs font-bold uppercase tracking-[0.18em] text-[#725E4C]">Místo</p>
                        <p class="mt-1 font-bold">Hair By ReneNeme</p>
                        <p class="mt-1 text-sm text-[#5E4E41]">Vackova 1064/39, Brno</p>
                    </div>
                </div>
                <?php if ($note !== ''): ?>
                    <div class="mt-3 rounded-lg border border-[#D8C8B0] bg-[#F9F5EF] p-4">
                        <p class="text-xs font-bold uppercase tracking-[0.18em] text-[#725E4C]">Poznámka</p>
                        <p class="mt-1 text-sm leading-6 text-[#5E4E41]"><?= nl2br(h($note)) ?></p>
                    </div>
                <?php endif; ?>
                <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                    <a href="index.php" class="inline-flex justify-center rounded-lg bg-[#C08A3E] px-5 py-3 text-sm font-semibold text-[#F5EDE1] shadow-md transition hover:bg-[#94642C]">
                        Zpět na web
                    </a>
                    <a href="index.php#booking" class="inline-flex justify-center rounded-lg border border-[#4A3A30] px-5 py-3 text-sm font-semibold text-[#2B211C] transition hover:bg-[#2B211C] hover:text-[#F5EDE1]">
                        Vytvořit další rezervaci
                    </a>
                </div>
            </div>
        <?php endif; ?>
        </div>
    </main>
</body>
</html>
