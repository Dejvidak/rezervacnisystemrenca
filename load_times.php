<?php

require __DIR__ . '/db.php';
require_once __DIR__ . '/integrations.php';

header('Content-Type: application/json; charset=UTF-8');

$date = trim($_GET['date'] ?? '');
$service = trim($_GET['service'] ?? '');
$dateObject = DateTime::createFromFormat('!Y-m-d', $date);

if (!$dateObject || $dateObject->format('Y-m-d') !== $date) {
    echo json_encode([
        'available' => [],
        'booked' => [],
        'closed' => true,
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$today = new DateTime('today');
$lastBookableDate = app_booking_last_date();
if ($dateObject < $today || $dateObject > $lastBookableDate) {
    echo json_encode([
        'available' => [],
        'booked' => [],
        'closed' => true,
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$services = app_services();
$duration = isset($services[$service])
    ? (int) $services[$service]['duration']
    : max(array_column($services, 'duration'));

$allTimes = app_time_slots_for_duration($date, $duration);
$stmt = $pdo->prepare('SELECT time, service, duration FROM reservations WHERE date = :date ORDER BY time ASC');
$stmt->execute([':date' => $date]);
$reservations = $stmt->fetchAll();
$googleBusy = app_google_calendar_busy_reservations_for_date($date);

if (!empty($googleBusy['errors'])) {
    echo json_encode([
        'available' => [],
        'booked' => [],
        'closed' => true,
        'calendar_error' => implode("\n", $googleBusy['errors']),
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$blockingReservations = array_merge($reservations, $googleBusy['reservations']);

$available = array_values(array_filter($allTimes, function (string $time) use ($blockingReservations, $duration): bool {
    return !app_reservations_overlap($blockingReservations, $time, $duration);
}));
$booked = array_values(array_map(function (array $reservation): string {
    return (string) $reservation['time'];
}, $blockingReservations));

$now = new DateTime();
if ($dateObject->format('Y-m-d') === $now->format('Y-m-d')) {
    $available = array_values(array_filter($available, function (string $time) use ($date): bool {
        $slot = DateTime::createFromFormat('Y-m-d H:i', $date . ' ' . $time);
        return $slot > new DateTime();
    }));
}

echo json_encode([
    'available' => $available,
    'booked' => array_values($booked),
    'closed' => empty($allTimes),
    'google_calendar_synced' => $googleBusy['configured'],
], JSON_UNESCAPED_UNICODE);
