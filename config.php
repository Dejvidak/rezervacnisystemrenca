<?php

if (is_file(__DIR__ . '/local_config.php')) {
    require_once __DIR__ . '/local_config.php';
}

date_default_timezone_set('Europe/Prague');

app_bootstrap_session();

function app_bootstrap_session(): void
{
    if (PHP_SAPI === 'cli' || session_status() === PHP_SESSION_ACTIVE || headers_sent()) {
        return;
    }

    session_set_cookie_params([
        'httponly' => true,
        'samesite' => 'Lax',
        'secure' => app_request_uses_https(),
    ]);

    session_start();
}

function app_request_uses_https(): bool
{
    $https = strtolower((string) ($_SERVER['HTTPS'] ?? ''));
    $forwardedProto = strtolower(trim((string) ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '')));

    return ($https !== '' && $https !== 'off') || $forwardedProto === 'https';
}

function app_csrf_token(): string
{
    if (!isset($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token']) || $_SESSION['csrf_token'] === '') {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function app_csrf_field(): string
{
    $token = htmlspecialchars(app_csrf_token(), ENT_QUOTES, 'UTF-8');
    return '<input type="hidden" name="csrf_token" value="' . $token . '">';
}

function app_verify_csrf_token(?string $token): bool
{
    $sessionToken = $_SESSION['csrf_token'] ?? null;
    return is_string($sessionToken) && is_string($token) && hash_equals($sessionToken, $token);
}

function app_booking_form_started_at(): int
{
    $startedAt = time();
    $_SESSION['booking_form_started_at'] = $startedAt;
    return $startedAt;
}

function app_validate_booking_honeypot(?string $value): bool
{
    return trim((string) $value) === '';
}

function app_validate_booking_form_timing($startedAt): bool
{
    $sessionStartedAt = $_SESSION['booking_form_started_at'] ?? null;
    if (!is_numeric($startedAt) || !is_numeric($sessionStartedAt)) {
        return false;
    }

    $startedAtInt = (int) $startedAt;
    $sessionStartedAtInt = (int) $sessionStartedAt;
    if ($startedAtInt !== $sessionStartedAtInt) {
        return false;
    }

    $elapsed = time() - $startedAtInt;
    return $elapsed >= 2 && $elapsed <= 7200;
}

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
    $serverHost = trim((string) ($_SERVER['HTTP_HOST'] ?? ''));
    if ($serverHost !== '') {
        $https = (string) ($_SERVER['HTTPS'] ?? '');
        $forwardedProto = trim((string) ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? ''));
        $scheme = ($https !== '' && strtolower($https) !== 'off') || strtolower($forwardedProto) === 'https'
            ? 'https'
            : 'http';

        return rtrim($scheme . '://' . $serverHost, '/');
    }

    return rtrim((string) (getenv('SITE_URL') ?: 'http://127.0.0.1:8000'), '/');
}

function app_booking_max_advance_days(): int
{
    return 7;
}

function app_business_name(): string
{
    return 'Hair By ReneNeme';
}

function app_business_phone(): string
{
    return '+420608419610';
}

function app_business_phone_display(): string
{
    return '+420 608 419 610';
}

function app_business_email(): string
{
    return 'renenemehair@seznam.cz';
}

function app_business_street_address(): string
{
    return 'Vackova 1064/39';
}

function app_business_postal_code(): string
{
    return '612 00';
}

function app_business_locality(): string
{
    return 'Brno-Královo Pole';
}

function app_business_locality_display(): string
{
    return 'Brno-Královo Pole';
}

function app_business_country(): string
{
    return 'CZ';
}

function app_business_ico(): string
{
    return '19671415';
}

function app_business_owner_name(): string
{
    return 'Renata Nemeskalova';
}

function app_business_instagram_url(): string
{
    return 'https://www.instagram.com/hairbyreneneme/';
}

function app_business_instagram_handle(): string
{
    return '@hairbyreneneme';
}

function app_business_map_url(): string
{
    return 'https://www.google.com/maps/search/?api=1&query=Vackova%201064%2F39%2C%20612%2000%20Brno-Kr%C3%A1lovo%20Pole';
}

function app_business_map_embed_url(): string
{
    return 'https://www.google.com/maps?q=Vackova%201064%2F39%2C%20612%2000%20Brno-Kr%C3%A1lovo%20Pole&output=embed';
}

function app_business_full_address_inline(): string
{
    return app_business_street_address() . ', ' . app_business_postal_code() . ' ' . app_business_locality_display();
}

function app_absolute_url(string $path = ''): string
{
    $base = app_site_url();
    if ($path === '') {
        return $base;
    }

    return $base . '/' . ltrim($path, '/');
}

function app_head_assets(): string
{
    $faviconIco = htmlspecialchars(app_absolute_url('favicon.ico?v=5'), ENT_QUOTES, 'UTF-8');
    $favicon = htmlspecialchars(app_absolute_url('assets/favicon.png?v=5'), ENT_QUOTES, 'UTF-8');
    $manifest = htmlspecialchars(app_absolute_url('site.webmanifest?v=5'), ENT_QUOTES, 'UTF-8');
    $modernCss = htmlspecialchars(app_absolute_url('assets/modern.css?v=33'), ENT_QUOTES, 'UTF-8');
    $modernJs = htmlspecialchars(app_absolute_url('assets/modern.js?v=27'), ENT_QUOTES, 'UTF-8');

    return <<<HTML
    <link rel="icon" href="{$faviconIco}" sizes="any">
    <link rel="icon" href="{$favicon}" type="image/png">
    <link rel="apple-touch-icon" href="{$favicon}">
    <link rel="manifest" href="{$manifest}">
    <meta name="theme-color" content="#080807">
    <link rel="stylesheet" href="{$modernCss}">
    <script src="{$modernJs}" defer></script>

HTML;
}

function app_html(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function app_site_footer(): string
{
    $businessName = app_html(app_business_name());
    $phone = app_html(app_business_phone());
    $phoneDisplay = app_html(app_business_phone_display());
    $email = app_html(app_business_email());
    $address = app_html(app_business_full_address_inline());
    $mapUrl = app_html(app_business_map_url());
    $instagramUrl = app_html(app_business_instagram_url());
    $instagramHandle = app_html(app_business_instagram_handle());
    $year = app_html((string) date('Y'));

    return <<<HTML
<footer id="footer" class="site-footer">
    <div class="site-footer__inner">
        <div class="site-footer__cta">
            <div class="site-footer__cta-copy">
                <p class="site-footer__eyebrow">Termín bez zbytečného čekání</p>
                <h2>Čistý střih, jasný ceník a rezervace na pár kliknutí.</h2>
                <p>Všechno důležité pro návštěvu Hair By ReneNeme najdeš rovnou tady: služby, adresa, otevíračka i rychlý kontakt.</p>
            </div>
            <div class="site-footer__actions" aria-label="Rychlé akce ve footeru">
                <a class="site-footer__button site-footer__button--primary" href="rezervace.php">Rezervovat termín</a>
                <a class="site-footer__button site-footer__button--ghost" href="cenik.php">Ceník služeb</a>
            </div>
        </div>

        <div class="site-footer__grid">
            <section class="site-footer__brand" aria-labelledby="site-footer-brand">
                <p id="site-footer-brand" class="site-footer__brand-title">
                    <span>Hair By</span>
                    <strong>ReneNeme</strong>
                </p>
                <p class="site-footer__brand-copy">
                    Pánské kadeřnictví v Brně pro střih, který drží tvar a návštěvu, která nepůsobí jako další povinnost v kalendáři.
                </p>
                <p class="site-footer__tagline">Pánské kadeřnictví · Brno-Královo Pole</p>
            </section>

            <section aria-labelledby="site-footer-visit">
                <h2 id="site-footer-visit" class="site-footer__heading">Návštěva</h2>
                <div class="site-footer__stack">
                    <p>
                        <span>Otevírací doba</span>
                        <strong>Po-Pá 9:00-18:00</strong>
                    </p>
                    <a href="{$mapUrl}" target="_blank" rel="noopener noreferrer">
                        <span>Adresa</span>
                        <strong>{$address}</strong>
                    </a>
                </div>
            </section>

            <section aria-labelledby="site-footer-contact">
                <h2 id="site-footer-contact" class="site-footer__heading">Kontakt</h2>
                <div class="site-footer__links">
                    <a href="tel:{$phone}">{$phoneDisplay}</a>
                    <a href="mailto:{$email}">{$email}</a>
                    <a href="{$instagramUrl}" target="_blank" rel="noopener noreferrer">Instagram {$instagramHandle}</a>
                    <a href="references.php">Reference střihů</a>
                </div>
            </section>
        </div>
    </div>
    <div class="site-footer__bottom">
        <p>© {$year} {$businessName}</p>
        <p>Web vytvořil Dejvidaak</p>
    </div>
</footer>

HTML;
}

function app_public_business_schema(string $pagePath = '', array $overrides = []): array
{
    $servicePrices = array_filter(
        array_map(static fn(array $service): int => (int) ($service['price'] ?? 0), app_services()),
        static fn(int $price): bool => $price > 0
    );
    $priceRange = !empty($servicePrices)
        ? min($servicePrices) . '-' . max($servicePrices) . ' CZK'
        : '310-620 CZK';

    $data = [
        '@context' => 'https://schema.org',
        '@type' => 'HairSalon',
        'name' => app_business_name(),
        'url' => app_absolute_url($pagePath),
        'image' => app_absolute_url('assets/barbershop-hero.png'),
        'telephone' => app_business_phone(),
        'email' => app_business_email(),
        'priceRange' => $priceRange,
        'address' => [
            '@type' => 'PostalAddress',
            'streetAddress' => app_business_street_address(),
            'postalCode' => app_business_postal_code(),
            'addressLocality' => app_business_locality_display(),
            'addressCountry' => app_business_country(),
        ],
        'sameAs' => [
            app_business_instagram_url(),
        ],
        'openingHoursSpecification' => [
            [
                '@type' => 'OpeningHoursSpecification',
                'dayOfWeek' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
                'opens' => '09:00',
                'closes' => '18:00',
            ],
        ],
        'areaServed' => [
            '@type' => 'City',
            'name' => 'Brno',
        ],
    ];

    foreach ($overrides as $key => $value) {
        $data[$key] = $value;
    }

    return $data;
}

function app_booking_last_date(): DateTime
{
    $lastDate = new DateTime('today');
    $lastDate->modify('+' . app_booking_max_advance_days() . ' days');
    return $lastDate;
}

function app_content_storage_path(): string
{
    return __DIR__ . '/storage/site_content.json';
}

function app_default_services(): array
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

function app_default_home_reference_cuts(): array
{
    return [
        [
            'title' => 'Delší přirozený střih',
            'description' => 'Zachovaná délka nahoře i vzadu, měkce upravený profil a přirozený objem bez tvrdého přechodu',
            'image' => 'assets/references/moderni-pansky-strih.jpg',
        ],
        [
            'title' => 'Mullet s vyholeným bokem',
            'description' => 'Výrazně vyčištěný bok kolem ucha, delší textura nahoře a ponechaná délka v zátylku',
            'image' => 'assets/references/home-reference-fade-test.png',
            'transparent_media' => true,
        ],
        [
            'title' => 'Dětský střih',
            'description' => 'Čistý dětský střih s jemným detailem a pohodovou návštěvou',
            'image' => 'assets/references/home-reference-child-test.png',
            'transparent_media' => true,
        ],
    ];
}

function app_default_reference_cuts(): array
{
    return [
        [
            'title' => 'Delší přirozený střih',
            'description' => 'Vzdušnější délka nahoře, lehce zkrácené boky a měkký tvar, který nepůsobí přestřiženě',
            'image' => 'assets/references/moderni-pansky-strih.jpg',
        ],
        [
            'title' => 'Moderní mullet fade',
            'description' => 'Krátce vyčištěný bok s přechodem kolem ucha, delší rozcuchaná textura nahoře a ponechaný zátylek',
            'image' => 'assets/references/kratky-fade.jpg',
        ],
        [
            'title' => 'Texturovaný mullet',
            'description' => 'Delší vršek a zadní partie, ostrý kontrast u spánku a volnější ofina směrem dopředu',
            'image' => 'assets/references/upraveny-strih.jpg',
        ],
        [
            'title' => 'Klasický taper s objemem',
            'description' => 'Čistě zkrácené boky a zátylek, delší uhlazený vršek a přirozený přechod bez tvrdé linie',
            'image' => 'assets/references/klasicky-styl.jpg',
        ],
        [
            'title' => 'Krátký crop fade',
            'description' => 'Nízký až střední fade do ztracena, krátký texturovaný vršek a čistý profil kolem vousů',
            'image' => 'assets/references/cisty-fade.jpg',
        ],
        [
            'title' => 'Zadní detail taper fade',
            'description' => 'Plynule vyčištěný zátylek, měkký přechod do delší textury a přirozeně zakončená zadní linie',
            'image' => 'assets/references/finalni-styling.jpg',
        ],
    ];
}

function app_default_site_content(): array
{
    return [
        'services' => app_default_services(),
        'home_references' => app_default_home_reference_cuts(),
        'references' => app_default_reference_cuts(),
    ];
}

function app_normalize_reference_cut(array $cut, array $fallback): array
{
    $title = trim((string) ($cut['title'] ?? ''));
    $description = trim((string) ($cut['description'] ?? ''));
    $image = trim((string) ($cut['image'] ?? ''));

    return [
        'title' => $title !== '' ? $title : (string) ($fallback['title'] ?? ''),
        'description' => $description !== '' ? $description : (string) ($fallback['description'] ?? ''),
        'image' => $image !== '' ? $image : (string) ($fallback['image'] ?? ''),
        'transparent_media' => !empty($cut['transparent_media']),
    ];
}

function app_normalize_reference_list($items, array $fallback): array
{
    if (!is_array($items)) {
        $items = [];
    }

    $normalized = [];
    foreach ($fallback as $index => $fallbackCut) {
        $cut = $items[$index] ?? [];
        $normalized[] = app_normalize_reference_cut(is_array($cut) ? $cut : [], $fallbackCut);
    }

    return $normalized;
}

function app_normalize_services($services, array $fallback): array
{
    if (!is_array($services)) {
        $services = [];
    }

    $normalized = [];
    foreach ($fallback as $serviceName => $fallbackService) {
        $service = $services[$serviceName] ?? [];
        if (!is_array($service)) {
            $service = [];
        }

        $price = (int) ($service['price'] ?? $fallbackService['price']);
        $duration = (int) ($service['duration'] ?? $fallbackService['duration']);

        $normalized[$serviceName] = [
            'price' => max(0, $price),
            'price_label' => trim((string) ($service['price_label'] ?? '')) !== '' ? trim((string) $service['price_label']) : (string) $fallbackService['price_label'],
            'duration' => max(5, $duration),
            'badge' => trim((string) ($service['badge'] ?? '')) !== '' ? trim((string) $service['badge']) : (string) ($fallbackService['badge'] ?? 'Služba'),
            'description' => trim((string) ($service['description'] ?? '')) !== '' ? trim((string) $service['description']) : (string) ($fallbackService['description'] ?? ''),
            'service_copy' => trim((string) ($service['service_copy'] ?? '')) !== '' ? trim((string) $service['service_copy']) : (string) ($fallbackService['service_copy'] ?? ''),
            'meta' => trim((string) ($service['meta'] ?? '')) !== '' ? trim((string) $service['meta']) : (string) ($fallbackService['meta'] ?? ''),
        ];

        if (!empty($fallbackService['service_title']) || !empty($service['service_title'])) {
            $normalized[$serviceName]['service_title'] = trim((string) ($service['service_title'] ?? '')) !== ''
                ? trim((string) $service['service_title'])
                : (string) ($fallbackService['service_title'] ?? '');
        }

        if (!empty($fallbackService['featured'])) {
            $normalized[$serviceName]['featured'] = true;
        }
    }

    return $normalized;
}

function app_normalize_site_content(array $content): array
{
    $fallback = app_default_site_content();

    return [
        'services' => app_normalize_services($content['services'] ?? [], $fallback['services']),
        'home_references' => app_normalize_reference_list($content['home_references'] ?? [], $fallback['home_references']),
        'references' => app_normalize_reference_list($content['references'] ?? [], $fallback['references']),
    ];
}

function app_site_content(bool $refresh = false): array
{
    static $content = null;

    if ($content !== null && !$refresh) {
        return $content;
    }

    $path = app_content_storage_path();
    if (!is_file($path)) {
        $content = app_default_site_content();
        return $content;
    }

    $decoded = json_decode((string) file_get_contents($path), true);
    $content = app_normalize_site_content(is_array($decoded) ? $decoded : []);

    return $content;
}

function app_save_site_content(array $content): bool
{
    $normalized = app_normalize_site_content($content);
    $dir = dirname(app_content_storage_path());
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }

    $json = json_encode($normalized, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if (!is_string($json)) {
        return false;
    }

    return file_put_contents(app_content_storage_path(), $json . "\n", LOCK_EX) !== false;
}

function app_services(): array
{
    return app_site_content()['services'];
}

function app_home_reference_cuts(): array
{
    return app_site_content()['home_references'];
}

function app_reference_cuts(): array
{
    return app_site_content()['references'];
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

function app_normalize_phone(string $phone): string
{
    $normalized = str_replace("\xc2\xa0", ' ', trim($phone));
    $normalized = preg_replace('/[().\/-]+/', ' ', $normalized);
    $normalized = preg_replace('/\s+/', ' ', $normalized);

    if (str_starts_with($normalized, '00')) {
        $normalized = '+' . substr($normalized, 2);
    }

    return $normalized;
}

function app_phone_is_valid(string $phone): bool
{
    if ($phone === '' || !preg_match('/^\+?[0-9 ]+$/', $phone)) {
        return false;
    }

    if (substr_count($phone, '+') > 1 || (str_contains($phone, '+') && !str_starts_with($phone, '+'))) {
        return false;
    }

    $digitsOnly = preg_replace('/\D+/', '', $phone);
    $digitCount = strlen($digitsOnly);

    return $digitCount >= 9 && $digitCount <= 15;
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
