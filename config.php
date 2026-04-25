<?php

if (is_file(__DIR__ . '/local_config.php')) {
    require_once __DIR__ . '/local_config.php';
}

date_default_timezone_set('Europe/Prague');

function app_timezone(): string
{
    return 'Europe/Prague';
}

function app_owner_email(): ?string
{
    $email = trim((string) getenv('BOOKING_NOTIFY_EMAIL'));
    return filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : null;
}

function app_email_enabled(): bool
{
    return app_owner_email() !== null;
}

function app_from_email(): string
{
    $email = trim((string) getenv('BOOKING_FROM_EMAIL'));
    return filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : 'rezervace@example.com';
}

function app_site_url(): string
{
    return rtrim((string) (getenv('SITE_URL') ?: 'http://127.0.0.1:8000'), '/');
}

function app_booking_max_advance_days(): int
{
    return 7;
}

function app_booking_last_date(): DateTime
{
    $lastDate = new DateTime('today');
    $lastDate->modify('+' . app_booking_max_advance_days() . ' days');
    return $lastDate;
}

function app_services(): array
{
    return [
        'Pánský střih' => [
            'price' => 420,
            'price_label' => '420 Kč',
            'duration' => 35,
            'badge' => 'Nejčastější volba',
            'description' => 'Čistý střih pro svěží každodenní look bez zbytečného zdržování',
            'service_copy' => 'Rychlý, čistý střih na sucho bez mytí a úpravy vousů',
            'meta' => 'Ideální pro pravidelnou údržbu a rychlý refresh',
        ],
        'Kompletka 1' => [
            'price' => 520,
            'price_label' => '520 Kč',
            'duration' => 45,
            'badge' => 'Více času na detail',
            'description' => 'Delší slot pro pečlivější doladění střihu a pohodovější návštěvu',
            'service_copy' => 'Střih s mytím, případně úpravou vousů podle domluvy',
            'service_title' => 'Kompletka 1 (zahrnuje střih a mytí)',
            'meta' => 'Skvělá volba, když chceš jít o kus víc do detailu',
        ],
        'Kompletka 2' => [
            'price' => 620,
            'price_label' => '620 Kč',
            'duration' => 50,
            'badge' => 'Premium čas',
            'description' => 'Nejdelší varianta pro maximální prostor na tvar, detail a finální úpravu',
            'service_copy' => 'Střih vlasů, úprava vousů a finální styling v jednom termínu',
            'meta' => 'Pro chvíle, kdy chceš rezervaci bez kompromisů',
            'featured' => true,
        ],
        'Dětský střih' => [
            'price' => 310,
            'price_label' => '310-420 Kč',
            'duration' => 30,
            'badge' => 'Pro mladší klienty',
            'description' => 'Krátká a svižná návštěva s ohledem na věk, délku vlasů i náročnost střihu',
            'service_copy' => 'Chlapecký i holčičí střih. Cena se liší podle náročnosti, například výrazně vyholené boky mohou být za 420 Kč',
            'meta' => 'Cena se odvíjí podle délky a náročnosti střihu',
        ],
    ];
}

function app_time_slots_for_date(string $date): array
{
    $day = DateTime::createFromFormat('!Y-m-d', $date);
    if (!$day || $day->format('Y-m-d') !== $date) {
        return [];
    }

    $openingHours = app_opening_hours_for_date($date);
    if ($openingHours === null) {
        return [];
    }

    return app_time_slots_for_duration($date, 30);
}

function app_time_slots_for_duration(string $date, int $durationMinutes): array
{
    $openingHours = app_opening_hours_for_date($date);
    if ($openingHours === null) {
        return [];
    }

    $start = DateTime::createFromFormat('Y-m-d H:i', $date . ' ' . $openingHours['start']);
    $lastStart = DateTime::createFromFormat('Y-m-d H:i', $date . ' ' . $openingHours['end']);
    $lastStart->modify('-' . max(1, $durationMinutes) . ' minutes');

    if ($lastStart < $start) {
        return [];
    }

    return app_build_time_slots($start->format('H:i'), $lastStart->format('H:i'));
}

function app_opening_hours_for_date(string $date): ?array
{
    $day = DateTime::createFromFormat('!Y-m-d', $date);
    if (!$day || $day->format('Y-m-d') !== $date) {
        return null;
    }

    $weekday = (int) $day->format('N');
    if ($weekday >= 1 && $weekday <= 5) {
        return ['start' => '09:00', 'end' => '18:00'];
    }

    return null;
}

function app_build_time_slots(string $start, string $last): array
{
    $slots = [];
    $current = DateTime::createFromFormat('H:i', $start);
    $end = DateTime::createFromFormat('H:i', $last);

    while ($current <= $end) {
        $slots[] = $current->format('H:i');
        $current->modify('+30 minutes');
    }

    return $slots;
}

function app_price_label(string $service): string
{
    $services = app_services();
    return $services[$service]['price_label'] ?? '';
}

function app_service_duration(string $service): int
{
    $services = app_services();
    return (int) ($services[$service]['duration'] ?? 30);
}

function app_reservations_overlap(array $reservations, string $time, int $durationMinutes): bool
{
    $start = app_time_to_minutes($time);
    $end = $start + $durationMinutes;

    foreach ($reservations as $reservation) {
        $existingStart = app_time_to_minutes((string) $reservation['time']);
        $existingDuration = (int) ($reservation['duration'] ?? 0);
        if ($existingDuration <= 0) {
            $existingDuration = app_service_duration((string) $reservation['service']);
        }

        $existingEnd = $existingStart + $existingDuration;
        if ($start < $existingEnd && $existingStart < $end) {
            return true;
        }
    }

    return false;
}

function app_time_to_minutes(string $time): int
{
    [$hours, $minutes] = array_map('intval', explode(':', $time));
    return $hours * 60 + $minutes;
}
