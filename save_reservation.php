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
    $appointment = DateTime::createFromFormat('Y-m-d H:i', $date . ' ' . $time);

    if ($dateObject < $today) {
        $errors[] = 'Nemůžeš si rezervovat termín v minulosti.';
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

?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= empty($errors) ? 'Potvrzení rezervace' : 'Chyba rezervace' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#1F1B18] text-[#F5EDE1] min-h-screen flex items-center justify-center p-4">
    <div class="bg-[#3F332A] border border-[#6A654E] rounded-2xl px-8 py-6 max-w-md w-full shadow-xl">
        <?php if (!empty($errors)): ?>
            <h1 class="text-2xl font-bold mb-4">Rezervace se nepovedla</h1>
            <ul class="list-disc list-inside space-y-1 text-sm">
                <?php foreach ($errors as $error): ?>
                    <li><?= h($error) ?></li>
                <?php endforeach; ?>
            </ul>
            <a href="index.php#booking" class="inline-block mt-6 text-sm px-4 py-2 rounded-lg bg-[#C9BFA7] text-[#1F1B18] font-semibold hover:bg-[#E0D6BD]">
                Zpět na formulář
            </a>
        <?php else: ?>
            <h1 class="text-2xl font-bold mb-4">Rezervace odeslána</h1>
            <p class="mb-2">Díky, <strong><?= h($name) ?></strong>. Tvoje rezervace byla úspěšně zaznamenána.</p>

            <h2 class="text-lg font-semibold mt-4 mb-2">Shrnutí rezervace:</h2>
            <ul class="text-sm space-y-1">
                <li><strong>Služba:</strong> <?= h($service) ?> (<?= h(app_price_label($service)) ?>)</li>
                <li><strong>Datum:</strong> <?= h($date) ?></li>
                <li><strong>Čas:</strong> <?= h($time) ?></li>
                <li><strong>Telefon:</strong> <?= h($phone) ?></li>
                <li><strong>E-mail:</strong> <?= h($email) ?></li>
                <?php if ($note !== ''): ?>
                    <li><strong>Poznámka:</strong> <?= nl2br(h($note)) ?></li>
                <?php endif; ?>
            </ul>

            <a href="index.php" class="inline-block mt-6 text-sm px-4 py-2 rounded-lg border border-[#6A654E] hover:bg-[#2A231E]">
                Zpět na web
            </a>
        <?php endif; ?>
    </div>
</body>
</html>
