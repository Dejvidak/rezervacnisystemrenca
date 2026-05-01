<?php

require_once __DIR__ . '/../config.php';

$failures = [];

function assert_true(bool $condition, string $message): void
{
    global $failures;

    if (!$condition) {
        $failures[] = $message;
    }
}

assert_true(app_phone_is_valid('+420 777 123 456'), 'Telefon s +420 ma projit validaci.');
assert_true(app_phone_is_valid('777123456'), 'Lokalni telefon ma projit validaci.');
assert_true(!app_phone_is_valid('abc123'), 'Textovy telefon nema projit validaci.');

$weekdaySlots = app_time_slots_for_duration('2026-05-04', 35);
assert_true($weekdaySlots[0] === '09:00', 'Prvni slot ve vsedni den ma zacinat v 09:00.');
assert_true(end($weekdaySlots) === '17:00', 'Posledni slot pro 35 minut ma skoncit startem v 17:00.');

$weekendSlots = app_time_slots_for_duration('2026-05-02', 30);
assert_true($weekendSlots === [], 'O vikendu nemaji vznikat zadne sloty.');

$overlappingReservations = [
    ['time' => '10:00', 'duration' => 45, 'service' => 'Kompletka 1'],
    ['time' => '12:00', 'duration' => 30, 'service' => 'Dětský střih'],
];
assert_true(app_reservations_overlap($overlappingReservations, '10:30', 30), 'Prekryvajici se termin ma byt rozpoznany.');
assert_true(!app_reservations_overlap($overlappingReservations, '11:00', 30), 'Neprekryvajici se termin nema byt oznacen jako kolize.');

if ($failures !== []) {
    fwrite(STDERR, "Reservation logic tests failed:\n");
    foreach ($failures as $failure) {
        fwrite(STDERR, '- ' . $failure . "\n");
    }
    exit(1);
}

fwrite(STDOUT, "Reservation logic tests passed.\n");
