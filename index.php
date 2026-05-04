<?php
require_once __DIR__ . '/config.php';

$services = app_services();
$serviceEntries = array_values(array_map(
    static fn(string $name, array $service): array => ['name' => $name, 'data' => $service],
    array_keys($services),
    $services
));
$featuredServiceEntry = null;
foreach ($serviceEntries as $entry) {
    if (!empty($entry['data']['featured'])) {
        $featuredServiceEntry = $entry;
        break;
    }
}
$teaserServices = [];
if ($featuredServiceEntry !== null) {
    $teaserServices[] = $featuredServiceEntry;
}
foreach ($serviceEntries as $entry) {
    if (count($teaserServices) >= 2) {
        break;
    }
    if (($featuredServiceEntry['name'] ?? null) === $entry['name']) {
        continue;
    }
    $teaserServices[] = $entry;
}
$servicePrices = array_map(static fn(array $service): int => (int) $service['price'], $services);
$serviceDurations = array_map(static fn(array $service): int => (int) $service['duration'], $services);
$instagramUrl = 'https://www.instagram.com/hairbyreneneme/';
$instagramHandle = '@hairbyreneneme';
$businessAddress = app_business_full_address_inline();
$businessMapUrl = app_business_map_url();
$businessMapEmbedUrl = app_business_map_embed_url();
$businessOpeningHoursLabel = 'Po-Pá 9:00-18:00';
$pageTitle = app_business_name() . ' - Pánské kadeřnictví & online rezervace';
$pageDescription = 'Pánské kadeřnictví Hair By ReneNeme v Brně. Online rezervace, čistý pánský střih, kompletka i dětský střih na adrese Vackova 1064/39, Brno-Královo Pole.';
$pageCanonical = app_absolute_url('/');
$pageImage = app_absolute_url('assets/renca-kaderko.jpg');
$pageSchema = app_public_business_schema('', [
    'description' => $pageDescription,
]);
$referenceCuts = [
    [
        'title' => 'Přirozený pánský střih',
        'description' => 'Lehce upravený tvar, čistší kontury a přirozený objem',
        'image' => 'assets/references/moderni-pansky-strih.jpg',
    ],
    [
        'title' => 'Krátký fade',
        'description' => 'Kratší boky, čistý přechod a upravený horní objem',
        'image' => 'assets/references/home-reference-fade-test.png',
        'transparent_media' => true,
    ],
    [
        'title' => 'Dětský střih',
        'description' => 'Čistý dětský střih s jemným detailem a pohodovou návštěvou',
        'image' => 'assets/references/home-reference-child-test.png',
        'transparent_media' => true,
    ],
    [
        'title' => 'Klasický styl',
        'description' => 'Nadčasový pánský střih s přirozenou délkou a měkkým tvarem',
        'image' => 'assets/references/klasicky-styl.jpg',
    ],
    [
        'title' => 'Čistý fade',
        'description' => 'Výraznější přechod, čistá linie kolem uší a svěží celkový vzhled',
        'image' => 'assets/references/cisty-fade.jpg',
    ],
    [
        'title' => 'Finální styling',
        'description' => 'Dokončený střih s lehkým stylingem pro upravený výsledný efekt',
        'image' => 'assets/references/finalni-styling.jpg',
    ],
];
?>
<!DOCTYPE html>
<html lang="cs" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<?= app_head_assets() ?>
    <meta name="description" content="<?= htmlspecialchars($pageDescription, ENT_QUOTES, 'UTF-8') ?>">
    <link rel="canonical" href="<?= htmlspecialchars($pageCanonical, ENT_QUOTES, 'UTF-8') ?>">
    <meta property="og:locale" content="cs_CZ">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="<?= htmlspecialchars(app_business_name(), ENT_QUOTES, 'UTF-8') ?>">
    <meta property="og:title" content="<?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?>">
    <meta property="og:description" content="<?= htmlspecialchars($pageDescription, ENT_QUOTES, 'UTF-8') ?>">
    <meta property="og:url" content="<?= htmlspecialchars($pageCanonical, ENT_QUOTES, 'UTF-8') ?>">
    <meta property="og:image" content="<?= htmlspecialchars($pageImage, ENT_QUOTES, 'UTF-8') ?>">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?>">
    <meta name="twitter:description" content="<?= htmlspecialchars($pageDescription, ENT_QUOTES, 'UTF-8') ?>">
    <meta name="twitter:image" content="<?= htmlspecialchars($pageImage, ENT_QUOTES, 'UTF-8') ?>">
    <script type="application/ld+json"><?= json_encode($pageSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            --page: #0D0D0B;
            --ink: #F7F3EA;
            --surface: #080807;
            --surface-soft: #24221E;
            --muted: #C8C1B4;
            --muted-strong: #D8CBB7;
            --line: #302D27;
            --line-soft: #3C3831;
            --cream: #F7F3EA;
            --cream-soft: #DCD3C2;
            --field: #171613;
            --field-border: #5B554B;
            --field-text: #F7F3EA;
            --accent: #C8AD63;
            --accent-dark: #A98A42;
            --gold: #D8BF7A;
            --gold-soft: #F0DFA9;
            --shadow-soft: 0 20px 44px rgba(0, 0, 0, 0.28);
            --shadow-strong: 0 30px 70px rgba(0, 0, 0, 0.46);
        }

        html,
        body {
            max-width: 100%;
            overflow-x: hidden;
        }

        body {
            padding-top: 4.35rem;
        }

        header,
        main,
        footer {
            max-width: 100vw;
        }

        .site-header {
            position: fixed;
            inset: 0 0 auto 0;
            z-index: 50;
            border-color: rgba(216, 191, 122, 0.18);
            background: linear-gradient(180deg, rgba(36, 34, 30, 0.96), rgba(20, 19, 17, 0.94));
            backdrop-filter: blur(14px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.22), inset 0 1px 0 rgba(255, 255, 255, 0.04);
            transition: background-color 220ms ease, border-color 220ms ease, box-shadow 220ms ease, backdrop-filter 220ms ease;
        }

        .site-header.is-scrolled {
            border-color: rgba(216, 191, 122, 0.28);
            background: linear-gradient(180deg, rgba(43, 40, 34, 0.9), rgba(13, 13, 11, 0.88));
            backdrop-filter: blur(18px);
            box-shadow: 0 16px 36px rgba(0, 0, 0, 0.3), inset 0 1px 0 rgba(216, 191, 122, 0.1);
        }

        @media (max-width: 1023px) {
            .site-header {
                position: fixed;
            }
        }

        .premium-surface {
            border: 1px solid var(--line);
            border-radius: 1.25rem;
            background: linear-gradient(180deg, rgba(31, 29, 25, 0.94), rgba(18, 17, 15, 0.92));
            box-shadow: var(--shadow-soft);
        }

        .premium-surface-dark {
            border: 1px solid rgba(214, 168, 94, 0.18);
            border-radius: 1.5rem;
            background: linear-gradient(145deg, rgba(43, 33, 28, 0.98), rgba(74, 58, 48, 0.94));
            box-shadow: var(--shadow-strong);
        }

        .ui-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.55rem;
            border-radius: 0.9rem;
            background: linear-gradient(180deg, var(--accent), var(--accent-dark));
            padding: 0.9rem 1.35rem;
            color: var(--cream);
            font-size: 0.95rem;
            font-weight: 700;
            box-shadow: 0 14px 28px rgba(200, 173, 99, 0.2);
            transition: transform 220ms ease, box-shadow 220ms ease, filter 220ms ease, background 220ms ease;
        }

        .ui-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 18px 34px rgba(200, 173, 99, 0.26);
            filter: saturate(1.04);
        }

        .ui-button-secondary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.55rem;
            border: 1px solid rgba(216, 191, 122, 0.28);
            border-radius: 0.9rem;
            background: rgba(31, 29, 25, 0.78);
            padding: 0.9rem 1.35rem;
            color: var(--cream);
            font-size: 0.95rem;
            font-weight: 700;
            box-shadow: 0 10px 24px rgba(0, 0, 0, 0.16);
            transition: transform 220ms ease, border-color 220ms ease, color 220ms ease, background 220ms ease, box-shadow 220ms ease;
        }

        .ui-button-secondary:hover {
            transform: translateY(-2px);
            border-color: rgba(216, 191, 122, 0.5);
            background: rgba(216, 191, 122, 0.12);
            color: var(--gold-soft);
            box-shadow: 0 16px 28px rgba(0, 0, 0, 0.2);
        }

        .price-stat {
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(216, 191, 122, 0.24);
            background: linear-gradient(180deg, rgba(31, 29, 25, 0.92), rgba(19, 18, 16, 0.92));
            box-shadow: 0 16px 30px rgba(0, 0, 0, 0.18);
        }

        .price-stat::before {
            position: absolute;
            inset: 0 auto 0 0;
            width: 4px;
            background: linear-gradient(180deg, var(--gold-soft), var(--accent));
            content: "";
        }

        .price-badge {
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(216, 191, 122, 0.26);
            background: linear-gradient(180deg, rgba(31, 29, 25, 0.94), rgba(19, 18, 16, 0.94));
            box-shadow: 0 14px 26px rgba(0, 0, 0, 0.18);
        }

        .price-badge::before {
            position: absolute;
            inset: 0 0 auto;
            height: 4px;
            background: linear-gradient(90deg, var(--gold-soft), var(--accent));
            content: "";
        }

        .price-badge--featured {
            border-color: rgba(216, 191, 122, 0.48);
            background: linear-gradient(180deg, rgba(216, 191, 122, 0.18), rgba(255, 255, 255, 0.06));
            box-shadow: 0 16px 28px rgba(0, 0, 0, 0.18);
        }

        .ui-button-ghost-dark {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.55rem;
            border: 1px solid rgba(218, 218, 213, 0.18);
            border-radius: 0.9rem;
            background: rgba(255, 255, 255, 0.04);
            padding: 0.9rem 1.35rem;
            color: var(--cream);
            font-size: 0.95rem;
            font-weight: 700;
            transition: transform 220ms ease, border-color 220ms ease, color 220ms ease, background 220ms ease;
        }

        .ui-button-ghost-dark:hover {
            transform: translateY(-2px);
            border-color: rgba(218, 218, 213, 0.34);
            background: rgba(255, 255, 255, 0.08);
            color: var(--gold-soft);
        }

        .lift-card {
            transition: transform 240ms ease, box-shadow 240ms ease, border-color 240ms ease;
        }

        .lift-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 22px 36px rgba(0, 0, 0, 0.14);
        }

        .reference-showcase {
            position: relative;
            overflow: hidden;
            border-radius: 1.5rem;
            background: linear-gradient(180deg, rgba(24, 23, 20, 0.78), rgba(13, 13, 11, 0.6));
        }

        .reference-showcase::before {
            display: none;
        }

        .reference-intro {
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(216, 200, 176, 0.9);
            border-radius: 1.25rem;
            background: linear-gradient(145deg, rgba(31, 29, 25, 0.94), rgba(18, 17, 15, 0.9));
            box-shadow: 0 18px 34px rgba(43, 33, 28, 0.07);
        }

        .reference-intro::before {
            position: absolute;
            inset: 0 auto auto 0;
            height: 100%;
            width: 5px;
            background: linear-gradient(180deg, var(--gold-soft), var(--accent));
            content: "";
        }

        .reference-gallery-grid {
            display: grid;
            gap: 1rem;
        }

        .reference-card {
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(74, 58, 48, 0.14);
            border-radius: 1.2rem;
            background: rgba(31, 29, 25, 0.92);
            box-shadow: 0 18px 34px rgba(43, 33, 28, 0.1);
            color: var(--ink);
        }

        .reference-card::before {
            display: none;
        }

        .reference-card::after {
            display: none;
        }

        .reference-card__media {
            position: relative;
            display: block;
            aspect-ratio: 4 / 5;
            overflow: hidden;
            background: var(--cream);
        }

        .reference-card__media--transparent {
            background: linear-gradient(145deg, rgba(31, 29, 25, 0.95), rgba(13, 13, 11, 0.96));
        }

        .reference-card__media--transparent::after {
            position: absolute;
            inset: 0;
            background:
                linear-gradient(180deg, rgba(8, 8, 7, 0.05), rgba(8, 8, 7, 0.44)),
                radial-gradient(circle at 78% 18%, rgba(216, 191, 122, 0.18), transparent 36%);
            content: "";
            pointer-events: none;
        }

        .reference-card__media img,
        .reference-card__media picture {
            display: block;
            height: 100%;
            width: 100%;
        }

        .reference-card__image--natural {
            object-position: 52% 18%;
        }

        .reference-card__image--fade {
            object-position: 50% 16%;
        }

        .reference-card__image--clean {
            object-position: 50% 16%;
        }

        .reference-card__image--transparent {
            opacity: 0.78;
            filter: saturate(0.9) contrast(1.06);
            mix-blend-mode: screen;
        }

        .reference-card__content {
            padding: 1rem;
        }

        .reference-card__eyebrow {
            display: inline-flex;
            border-radius: 999px;
            border: 1px solid rgba(216, 191, 122, 0.22);
            background: rgba(216, 191, 122, 0.1);
            padding: 0.3rem 0.62rem;
            font-size: 0.62rem;
            font-weight: 800;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            color: var(--accent-dark);
        }

        .reference-card__panel {
            margin-top: 0.85rem;
        }

        .reference-card__cta {
            display: inline-flex;
            align-items: center;
            gap: 0.55rem;
            color: var(--accent-dark);
            transition: gap 220ms ease, color 220ms ease, transform 220ms ease;
        }

        .reference-card__cta-icon {
            display: inline-flex;
            height: 1.9rem;
            width: 1.9rem;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            border: 1px solid rgba(216, 191, 122, 0.28);
            background: rgba(216, 191, 122, 0.1);
            color: var(--accent-dark);
            transition: transform 220ms ease, background 220ms ease, border-color 220ms ease;
        }

        .reference-card__hint {
            color: var(--muted);
        }

        .reference-card:hover .reference-card__panel,
        .reference-card:focus-visible .reference-card__panel {
            transform: none;
            background: transparent;
            box-shadow: none;
        }

        .reference-card:hover .reference-card__cta,
        .reference-card:focus-visible .reference-card__cta {
            gap: 0.8rem;
            transform: translateX(2px);
        }

        .reference-card:hover .reference-card__cta-icon,
        .reference-card:focus-visible .reference-card__cta-icon {
            transform: translateX(2px);
            background: rgba(192, 138, 62, 0.14);
            border-color: rgba(192, 138, 62, 0.4);
        }

        .reference-card:hover .reference-card__hint,
        .reference-card:focus-visible .reference-card__hint {
            opacity: 1;
        }

        .reference-card--featured {
            min-height: 0;
        }

        .reference-card--compact {
            min-height: 0;
        }

        @media (min-width: 1024px) {
            .reference-gallery-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }

            .reference-card--featured {
                grid-row: auto;
            }

            .reference-card--compact {
                min-height: 0;
            }
        }

        .accent-link {
            color: var(--accent);
            font-weight: 700;
            text-decoration: underline;
            text-decoration-color: var(--accent);
            text-underline-offset: 0.26rem;
            transition: color 220ms ease, text-decoration-color 220ms ease;
        }

        .accent-link:hover {
            color: var(--accent-dark);
            text-decoration-color: var(--accent-dark);
        }

        .trust-grid-card {
            position: relative;
            overflow: hidden;
        }

        .trust-grid-card::before {
            position: absolute;
            inset: 0 auto auto 0;
            height: 3px;
            width: 100%;
            background: linear-gradient(90deg, var(--gold), rgba(241, 200, 121, 0));
            content: "";
        }

        .hero-stage {
            position: relative;
            isolation: isolate;
        }

        .homepage-hero {
            position: relative;
            isolation: isolate;
            min-height: calc(100svh - 4.35rem);
            width: 100vw;
            margin-left: calc(50% - 50vw);
            overflow: hidden;
            padding: clamp(2.8rem, 7vh, 5.4rem) clamp(1.25rem, 4vw, 4rem) clamp(2rem, 5vh, 4rem);
            background: #050504;
        }

        .homepage-hero__media {
            position: absolute;
            inset: -4% 0 -10%;
            z-index: 0;
            transform: translate3d(0, var(--hero-media-y, 0px), 0);
            will-change: transform;
        }

        .homepage-hero__media::before {
            display: none;
        }

        .homepage-hero__media img {
            display: block;
            height: 100%;
            width: 100%;
            object-fit: cover;
            object-position: 58% 50%;
            filter: grayscale(0.92) saturate(0.76) contrast(1.08) brightness(0.52);
            transform: scale(1.045);
            transform-origin: 58% 50%;
            animation: heroImageBreath 26s ease-in-out infinite;
            will-change: transform;
        }

        @keyframes heroImageBreath {
            0%,
            100% {
                transform: scale(1.045);
            }

            50% {
                transform: scale(1.115);
            }
        }

        .homepage-hero::before {
            position: absolute;
            inset: 0;
            z-index: 1;
            background:
                linear-gradient(90deg, rgba(5, 5, 4, 0.82) 0%, rgba(5, 5, 4, 0.52) 38%, rgba(5, 5, 4, 0.14) 68%, rgba(5, 5, 4, 0.72) 100%),
                linear-gradient(180deg, rgba(5, 5, 4, 0.08) 0%, rgba(5, 5, 4, 0.04) 54%, rgba(5, 5, 4, 0.72) 100%);
            content: "";
            pointer-events: none;
        }

        .homepage-hero__inner {
            position: relative;
            z-index: 2;
            display: grid;
            min-height: calc(100svh - 7.35rem);
            align-items: center;
            gap: 2rem;
        }

        .homepage-hero__copy {
            opacity: 0;
            transform: translate3d(0, -86px, 0);
            animation: heroCopyDrop 1250ms cubic-bezier(0.16, 1, 0.3, 1) 160ms forwards;
            will-change: opacity, transform;
        }

        .homepage-hero__eyebrow {
            color: var(--gold-soft);
        }

        .homepage-hero__title {
            max-width: 6.4em;
            color: var(--cream);
            font-family: Impact, "Arial Black", "Helvetica Neue", Arial, sans-serif;
            font-size: clamp(3.7rem, 7.9vw, 7.2rem);
            font-weight: 950;
            letter-spacing: 0;
            line-height: 0.94;
            text-transform: uppercase;
            text-wrap: balance;
        }

        .homepage-hero__title-accent {
            color: var(--gold);
        }

        .homepage-hero__intro {
            max-width: 42rem;
            color: rgba(247, 243, 234, 0.86);
        }

        .hero-info-grid {
            display: grid;
            gap: 0.75rem;
            max-width: 42rem;
        }

        .hero-info-pill {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            border: 1px solid rgba(216, 191, 122, 0.22);
            border-radius: 0.9rem;
            background: rgba(8, 8, 7, 0.5);
            padding: 0.8rem 0.9rem;
            color: var(--cream-soft);
            box-shadow: 0 16px 34px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
        }

        .hero-info-pill__icon {
            display: inline-flex;
            flex: 0 0 auto;
            align-items: center;
            justify-content: center;
            width: 2rem;
            height: 2rem;
            border-radius: 0.7rem;
            background: rgba(216, 191, 122, 0.13);
            color: var(--gold-soft);
        }

        .booking-map-card {
            overflow: hidden;
            border: 1px solid rgba(216, 191, 122, 0.2);
            border-radius: 1.25rem;
            background: linear-gradient(180deg, rgba(31, 29, 25, 0.94), rgba(18, 17, 15, 0.92));
            box-shadow: 0 20px 44px rgba(0, 0, 0, 0.22);
        }

        .booking-map-card iframe {
            display: block;
            width: 100%;
            height: 17rem;
            border: 0;
            filter: grayscale(0.32) contrast(1.08) brightness(0.86);
        }

        .map-action-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.65rem;
            width: 100%;
            border: 1px solid rgba(216, 191, 122, 0.28);
            border-radius: 0.85rem;
            background: rgba(216, 191, 122, 0.1);
            padding: 0.9rem 1rem;
            color: var(--cream);
            font-size: 0.94rem;
            font-weight: 750;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.04);
            transition: transform 220ms ease, border-color 220ms ease, background 220ms ease, color 220ms ease, box-shadow 220ms ease;
        }

        .map-action-button:hover,
        .map-action-button:focus-visible {
            transform: translateY(-1px);
            border-color: rgba(216, 191, 122, 0.52);
            background: rgba(216, 191, 122, 0.18);
            color: var(--gold-soft);
            box-shadow: 0 14px 28px rgba(0, 0, 0, 0.18);
        }

        .map-action-button__icon {
            display: inline-flex;
            flex: 0 0 auto;
            align-items: center;
            justify-content: center;
            width: 2rem;
            height: 2rem;
            border-radius: 0.65rem;
            background: rgba(216, 191, 122, 0.14);
            color: var(--gold-soft);
        }

        .map-action-button__text {
            min-width: 0;
            overflow-wrap: anywhere;
        }

        @media (min-width: 640px) {
            .hero-info-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .map-action-button {
                width: auto;
                min-width: 13rem;
                padding-right: 1.15rem;
            }
        }

        .homepage-hero__card {
            align-self: end;
            justify-self: end;
            width: min(100%, 22rem);
            margin-bottom: clamp(4rem, 10vh, 6.5rem);
            border: 1px solid rgba(247, 243, 234, 0.18);
            border-radius: 1rem;
            background: rgba(8, 8, 7, 0.56);
            box-shadow: 0 18px 44px rgba(0, 0, 0, 0.24);
            backdrop-filter: blur(16px);
            opacity: 0;
            transform: translate3d(0, -54px, 0);
            animation: heroCopyDrop 1050ms cubic-bezier(0.16, 1, 0.3, 1) 420ms forwards;
        }

        .homepage-hero__card summary {
            cursor: pointer;
            list-style: none;
        }

        .homepage-hero__card summary::-webkit-details-marker {
            display: none;
        }

        .homepage-hero__card-toggle {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .homepage-hero__card-icon {
            display: inline-flex;
            flex: 0 0 auto;
            align-items: center;
            justify-content: center;
            width: 2.1rem;
            height: 2.1rem;
            border-radius: 0.7rem;
            background: rgba(216, 191, 122, 0.12);
            color: var(--gold-soft);
            transition: transform 220ms ease, background 220ms ease;
        }

        .homepage-hero__card[open] .homepage-hero__card-icon {
            transform: rotate(45deg);
            background: rgba(216, 191, 122, 0.2);
        }

        .homepage-hero__card-body {
            margin-top: 0.85rem;
            border-top: 1px solid rgba(247, 243, 234, 0.12);
            padding-top: 0.85rem;
        }

        .homepage-hero__steps {
            display: grid;
            gap: 0.55rem;
            color: var(--cream-soft);
            font-size: 0.86rem;
            line-height: 1.55;
        }

        .homepage-hero__steps li {
            display: flex;
            gap: 0.55rem;
        }

        .homepage-hero__steps li::before {
            flex: 0 0 auto;
            margin-top: 0.55em;
            width: 0.35rem;
            height: 0.35rem;
            border-radius: 999px;
            background: var(--gold);
            content: "";
        }

        @keyframes heroCopyDrop {
            to {
                opacity: 1;
                transform: translate3d(0, 0, 0);
            }
        }

        .selected-service-card {
            display: none;
            align-items: stretch;
            justify-content: space-between;
            gap: 1rem;
            border: 1px solid rgba(216, 191, 122, 0.38);
            border-radius: 1rem;
            background: linear-gradient(135deg, rgba(216, 191, 122, 0.12), rgba(31, 29, 25, 0.94));
            padding: 1rem;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.04), 0 18px 34px rgba(0, 0, 0, 0.16);
        }

        .selected-service-card.is-visible {
            display: flex;
        }

        .selected-service-card__price {
            display: flex;
            min-width: 6.4rem;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(216, 191, 122, 0.28);
            border-radius: 0.85rem;
            background: rgba(13, 13, 11, 0.42);
            padding: 0.85rem 0.75rem;
            text-align: center;
        }

        @media (min-width: 768px) {
            .homepage-hero__inner {
                grid-template-columns: minmax(0, 0.9fr) minmax(18rem, 0.62fr);
            }
        }

        @media (max-width: 767px) {
            .homepage-hero {
                min-height: calc(100svh - 4.35rem);
                padding-top: 2.2rem;
                padding-bottom: 2rem;
            }

            .homepage-hero__inner {
                min-height: calc(100svh - 8.55rem);
                align-items: end;
            }

            .homepage-hero__media img {
                object-fit: cover;
                object-position: 62% 50%;
            }

            .homepage-hero::before {
                background:
                    linear-gradient(90deg, rgba(5, 5, 4, 0.9) 0%, rgba(5, 5, 4, 0.48) 70%, rgba(5, 5, 4, 0.76) 100%),
                    linear-gradient(180deg, rgba(5, 5, 4, 0.16) 0%, rgba(5, 5, 4, 0.12) 45%, rgba(5, 5, 4, 0.84) 100%);
            }

            .homepage-hero__card {
                justify-self: start;
                width: min(100%, 20rem);
                margin-bottom: 0;
            }

            .homepage-hero__title {
                max-width: 6.6em;
                font-size: clamp(2.85rem, 13.8vw, 4.75rem);
                line-height: 0.96;
            }

            .selected-service-card {
                flex-direction: column;
            }

            .selected-service-card__price {
                min-width: 0;
                align-items: flex-start;
                text-align: left;
            }
        }

        .hero-stage::before,
        .hero-stage::after {
            position: absolute;
            z-index: -1;
            border-radius: 999px;
            content: "";
            filter: blur(12px);
            opacity: 0.72;
        }

        .hero-stage::before {
            top: 0;
            left: -3.5rem;
            height: 11rem;
            width: 11rem;
            background: radial-gradient(circle, rgba(17, 17, 17, 0.22), rgba(17, 17, 17, 0));
        }

        .hero-stage::after {
            right: -1.5rem;
            bottom: 1rem;
            height: 13rem;
            width: 13rem;
            background: radial-gradient(circle, rgba(35, 35, 35, 0.14), rgba(35, 35, 35, 0));
        }

        .hero-media-frame {
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(17, 17, 17, 0.18);
            border-radius: 1.75rem;
            background: linear-gradient(145deg, rgba(0, 0, 0, 0.98), rgba(35, 35, 35, 0.94));
            box-shadow: var(--shadow-strong);
        }

        .hero-media-frame::before {
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(0, 0, 0, 0.08), rgba(0, 0, 0, 0.34));
            content: "";
            pointer-events: none;
        }

        .hero-media-frame::after {
            position: absolute;
            inset: 0.85rem;
            border: 1px solid rgba(245, 237, 225, 0.14);
            border-radius: 1.15rem;
            content: "";
            pointer-events: none;
        }

        .hero-floating-card {
            backdrop-filter: blur(14px);
            background: rgba(0, 0, 0, 0.84);
            box-shadow: 0 18px 34px rgba(0, 0, 0, 0.22);
        }

        .booking-form {
            position: relative;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.18);
            transform: translate3d(0, 0, 0);
            will-change: transform;
        }

        .booking-form input,
        .booking-form select,
        .booking-form textarea {
            box-sizing: border-box;
            max-width: 100%;
            min-width: 0;
        }

        .booking-slot-grid {
            gap: 0.75rem;
        }

        .booking-control {
            appearance: none;
            -webkit-appearance: none;
            display: block;
            width: 100%;
            height: 2.75rem;
            border: 1px solid var(--field-border);
            border-radius: 0.5rem;
            background-color: var(--field);
            color: var(--field-text);
            font-size: 1rem;
            line-height: 1.25;
            transition: border-color 180ms ease, box-shadow 180ms ease, background-color 180ms ease;
        }

        .booking-control:focus {
            outline: none;
            border-color: var(--gold);
            box-shadow: 0 0 0 3px rgba(216, 191, 122, 0.22);
        }

        .booking-control:disabled {
            cursor: not-allowed;
            border-color: rgba(91, 85, 75, 0.58);
            color: rgba(247, 243, 234, 0.62);
            opacity: 1;
        }

        .booking-control--date {
            padding: 0 0.9rem;
            line-height: 2.75rem;
        }

        .booking-date-shell {
            position: relative;
        }

        .booking-date-shell .booking-control--date {
            color: transparent;
            -webkit-text-fill-color: transparent;
            caret-color: transparent;
        }

        .booking-date-display {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            padding: 0 2.6rem 0 0.9rem;
            color: var(--gold-soft);
            font-size: 1rem;
            line-height: 1;
            pointer-events: none;
        }

        .booking-control--select {
            padding: 0 2.35rem 0 0.9rem;
            background-image:
                linear-gradient(45deg, transparent 50%, var(--gold-soft) 50%),
                linear-gradient(135deg, var(--gold-soft) 50%, transparent 50%);
            background-position:
                calc(100% - 1rem) 50%,
                calc(100% - 0.68rem) 50%;
            background-repeat: no-repeat;
            background-size: 0.32rem 0.32rem, 0.32rem 0.32rem;
        }

        .booking-control--select::-ms-expand {
            display: none;
        }

        .booking-form input[type="date"] {
            appearance: none;
            -webkit-appearance: none;
            display: block;
            width: 100%;
            max-width: 100%;
            min-height: 2.75rem;
            line-height: 1.35;
        }

        .booking-form input[type="date"]::-webkit-date-and-time-value {
            display: flex;
            align-items: center;
            min-height: 100%;
            text-align: left;
        }

        .booking-form input[type="date"]::-webkit-calendar-picker-indicator {
            opacity: 0.7;
            padding: 0.35rem;
            margin-right: -0.15rem;
            cursor: pointer;
            filter: invert(0.92) sepia(0.2) saturate(1.3);
        }

        @media (max-width: 639px) {
            .booking-slot-grid {
                gap: 0.85rem;
            }

            .booking-control {
                height: 3rem;
                font-size: 1rem;
            }
        }

        @media (min-width: 640px) {
            .booking-control {
                height: 2.35rem;
                border-radius: 0.5rem;
                font-size: 0.875rem;
            }

            .booking-form input[type="date"] {
                min-height: 2.35rem;
            }

            .booking-control--date {
                padding: 0 0.75rem;
                line-height: 2.35rem;
            }

            .booking-date-display {
                padding: 0 2.2rem 0 0.75rem;
                font-size: 0.875rem;
            }

            .booking-control--select {
                padding: 0 2rem 0 0.75rem;
                background-position:
                    calc(100% - 0.92rem) 50%,
                    calc(100% - 0.62rem) 50%;
                background-size: 0.28rem 0.28rem, 0.28rem 0.28rem;
            }
        }

        .booking-form::after {
            position: absolute;
            inset: -5px;
            z-index: -1;
            border: 2px solid rgba(17, 17, 17, 0.58);
            border-radius: inherit;
            content: "";
            opacity: 0;
            transform: translateZ(0) scale(0.985);
            will-change: opacity, transform;
        }

        .booking-attention {
            animation: bookingLift 820ms cubic-bezier(0.2, 0.8, 0.2, 1);
        }

        .booking-attention::after {
            animation: bookingRing 820ms ease-out;
        }

        .booking-submit__spinner {
            animation: spin 780ms linear infinite;
        }

        .booking-loading-overlay {
            opacity: 0;
            pointer-events: none;
            transition: opacity 260ms ease;
        }

        .booking-loading-overlay.is-visible {
            opacity: 1;
            pointer-events: auto;
        }

        .booking-loading-card {
            opacity: 0;
            transform: translate3d(0, 18px, 0) scale(0.96);
            transition: opacity 320ms ease, transform 460ms cubic-bezier(0.16, 1, 0.3, 1);
        }

        .booking-loading-overlay.is-visible .booking-loading-card {
            opacity: 1;
            transform: translate3d(0, 0, 0) scale(1);
        }

        .booking-loading-pulse {
            animation: bookingPulse 1200ms ease-in-out infinite;
        }

        .booking-loading-progress {
            animation: bookingProgress 2100ms cubic-bezier(0.2, 0.8, 0.2, 1) infinite;
            transform-origin: left center;
        }

        .booking-loading-overlay.is-slow .booking-loading-fallback {
            display: block;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        @keyframes bookingPulse {
            0%, 100% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(17, 17, 17, 0.35);
            }
            50% {
                transform: scale(1.06);
                box-shadow: 0 0 0 12px rgba(17, 17, 17, 0);
            }
        }

        @keyframes bookingProgress {
            0% {
                transform: scaleX(0.12);
            }
            45% {
                transform: scaleX(0.72);
            }
            100% {
                transform: scaleX(1);
            }
        }

        @keyframes bookingLift {
            0% {
                transform: translate3d(0, 0, 0);
            }
            42% {
                transform: translate3d(0, -7px, 0);
            }
            72% {
                transform: translate3d(0, -1px, 0);
            }
            100% {
                transform: translate3d(0, 0, 0);
            }
        }

        @keyframes bookingRing {
            0% {
                opacity: 0;
                transform: translateZ(0) scale(0.985);
            }
            25% {
                opacity: 1;
                transform: translateZ(0) scale(1);
            }
            100% {
                opacity: 0;
                transform: translateZ(0) scale(1.025);
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .booking-attention {
                animation: none;
            }

            .booking-attention::after {
                animation: none;
                opacity: 1;
            }

            .booking-submit__spinner {
                animation: none;
            }

            .booking-loading-overlay,
            .booking-loading-card,
            .booking-loading-pulse,
            .booking-loading-progress {
                animation: none;
                transition: none;
            }

            .homepage-hero__media,
            .homepage-hero__media img,
            .homepage-hero__copy,
            .homepage-hero__card {
                animation: none;
                opacity: 1;
                transform: none;
                transition: none;
            }
        }

        .about-card__button {
            cursor: pointer;
        }

        .about-card__icon {
            transition: transform 260ms ease;
        }

        .about-card:hover .about-card__icon {
            transform: translateY(-2px);
        }

        .about-popover {
            opacity: 0;
            pointer-events: none;
            transition: opacity 260ms ease;
        }

        .about-popover.is-open {
            opacity: 1;
            pointer-events: auto;
        }

        .about-popover.is-closing {
            opacity: 0;
            pointer-events: none;
        }

        .about-popover__panel {
            opacity: 0;
            transform: translate3d(var(--about-start-x, 0), var(--about-start-y, 26px), 0) scale(var(--about-start-scale, 0.82));
            transform-origin: center center;
            transition: opacity 260ms ease, transform 560ms cubic-bezier(0.16, 1, 0.3, 1);
            will-change: opacity, transform;
        }

        .about-popover.is-open .about-popover__panel.is-expanded {
            opacity: 1;
            transform: translate3d(0, 0, 0) scale(1);
        }

        .gallery-lightbox {
            opacity: 0;
            pointer-events: none;
            transition: opacity 220ms ease;
        }

        .gallery-lightbox.is-open {
            opacity: 1;
            pointer-events: auto;
        }

        .gallery-lightbox.is-closing {
            opacity: 0;
            pointer-events: none;
        }

        .gallery-lightbox__panel {
            opacity: 0;
            transform: translate3d(var(--gallery-start-x, 0), var(--gallery-start-y, 28px), 0) scale(var(--gallery-start-scale, 0.86));
            transform-origin: center center;
            transition: opacity 260ms ease, transform 680ms cubic-bezier(0.16, 1, 0.3, 1);
            will-change: opacity, transform;
        }

        .gallery-lightbox.is-open .gallery-lightbox__panel.is-expanded {
            opacity: 1;
            transform: translate3d(0, 0, 0) scale(1);
        }

        /* section-reveal styles are now in assets/modern.css */

        .scroll-top-button {
            opacity: 0;
            pointer-events: none;
            transform: translate3d(0, 12px, 0);
            border-color: rgba(17, 17, 17, 0.18);
            background: rgba(0, 0, 0, 0.94);
            color: var(--gold-soft);
            box-shadow: 0 18px 34px rgba(0, 0, 0, 0.22);
            transition: opacity 220ms ease, transform 220ms ease, background-color 220ms ease, color 220ms ease, box-shadow 220ms ease, border-color 220ms ease, filter 220ms ease;
        }

        .scroll-top-button.is-visible {
            opacity: 1;
            pointer-events: auto;
            transform: translate3d(0, 0, 0);
        }

        .scroll-top-button.is-visible:hover {
            background: var(--accent-dark);
            color: var(--cream);
            border-color: rgba(218, 218, 213, 0.48);
            box-shadow: 0 22px 38px rgba(0, 0, 0, 0.28);
            filter: brightness(1.06) saturate(1.04);
            transform: translate3d(0, -2px, 0);
        }

        .scroll-top-button.is-footer-contrast {
            background: rgba(245, 237, 225, 0.96);
            color: var(--accent-dark);
            border-color: rgba(17, 17, 17, 0.34);
            box-shadow: 0 18px 34px rgba(0, 0, 0, 0.14);
        }

        .scroll-top-button.is-visible.is-footer-contrast:hover {
            background: rgba(218, 218, 213, 0.98);
            color: var(--surface);
            border-color: rgba(17, 17, 17, 0.52);
            box-shadow: 0 22px 38px rgba(0, 0, 0, 0.22);
            filter: brightness(1.02) saturate(1.02);
        }

        @media (prefers-reduced-motion: reduce) {
            .ui-button,
            .ui-button-secondary,
            .ui-button-ghost-dark,
            .lift-card,
            .about-card__icon,
            .about-popover,
            .about-popover__panel,
            .gallery-lightbox,
            .gallery-lightbox__panel,
            .scroll-top-button {
                transition: none;
            }
        }
    </style>
</head>
<body class="overflow-x-hidden bg-[var(--page)] text-[color:var(--cream)] antialiased">

<!-- Scroll progress bar -->
<div id="scrollProgress" aria-hidden="true"></div>

<!-- NAV / HEADER -->
<header class="site-header bg-[var(--surface)] border-b border-[var(--surface-soft)] shadow-lg">
    <div class="max-w-6xl mx-auto flex items-center justify-between px-4 py-3">
        <a href="#top" class="whitespace-nowrap text-xl font-extrabold tracking-tight transition hover:opacity-90 sm:text-2xl md:text-[1.65rem]" aria-label="Hair By ReneNeme">
            <span class="text-[color:var(--cream)]">Hair By</span>
            <span class="text-[color:var(--gold)]">ReneNeme</span>
        </a>
        <nav class="hidden items-center gap-2 text-xs text-[color:var(--cream-soft)] lg:flex lg:gap-5 lg:text-sm">
            <a href="#about" class="nav-link whitespace-nowrap transition hover:text-[color:var(--gold)]">O nás</a>
            <a href="#services" class="nav-link whitespace-nowrap transition hover:text-[color:var(--gold)]">Služby</a>
            <a href="references.php" class="nav-link whitespace-nowrap transition hover:text-[color:var(--gold)]">Reference</a>
            <a href="cenik.php" class="nav-link whitespace-nowrap transition hover:text-[color:var(--gold)]">Ceník</a>
            <a href="contact.php" class="nav-link whitespace-nowrap transition hover:text-[color:var(--gold)]">Kontakt</a>
            <a
                href="<?= htmlspecialchars($instagramUrl, ENT_QUOTES, 'UTF-8') ?>"
                target="_blank"
                rel="noopener"
                class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-[var(--surface-soft)] text-[color:var(--cream)] transition hover:-translate-y-0.5 hover:border-[var(--gold)] hover:text-[color:var(--gold)] focus:outline-none focus:ring-2 focus:ring-[var(--gold)]"
                aria-label="Instagram Hair By ReneNeme"
            >
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <rect x="4" y="4" width="16" height="16" rx="5" stroke="currentColor" stroke-width="2" />
                    <circle cx="12" cy="12" r="3.5" stroke="currentColor" stroke-width="2" />
                    <path d="M17 7.2h.01" stroke="currentColor" stroke-width="3" stroke-linecap="round" />
                </svg>
            </a>
            <a href="#booking" class="inline-flex whitespace-nowrap rounded-lg bg-[var(--accent)] px-4 py-2 font-semibold text-[color:var(--cream)] shadow-sm transition hover:-translate-y-0.5 hover:bg-[var(--accent-dark)] hover:shadow-md focus:outline-none focus:ring-2 focus:ring-[var(--gold)]">Rezervace</a>
        </nav>
        <button
            type="button"
            id="mobileMenuButton"
            class="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-[var(--field-border)] text-[color:var(--cream)] transition hover:bg-[var(--surface-soft)] focus:outline-none focus:ring-2 focus:ring-[var(--gold)] lg:hidden"
            aria-controls="mobileMenu"
            aria-expanded="false"
            aria-label="Otevřít menu"
        >
            <svg id="menuIconOpen" class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M4 7h16M4 12h16M4 17h16" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
            </svg>
            <svg id="menuIconClose" class="hidden h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
            </svg>
        </button>
    </div>
    <nav id="mobileMenu" class="hidden max-h-[calc(100vh-4.25rem)] overflow-y-auto border-t border-[rgba(216,191,122,0.18)] bg-[#1F1D19] px-4 pb-4 pt-2 text-sm text-[color:var(--cream-soft)] shadow-lg lg:hidden">
        <a href="#about" class="block rounded-lg px-3 py-3 hover:bg-[var(--surface-soft)] hover:text-[color:var(--gold)]">O nás</a>
        <a href="#services" class="block rounded-lg px-3 py-3 hover:bg-[var(--surface-soft)] hover:text-[color:var(--gold)]">Služby</a>
        <a href="references.php" class="block rounded-lg px-3 py-3 hover:bg-[var(--surface-soft)] hover:text-[color:var(--gold)]">Reference</a>
        <a href="cenik.php" class="block rounded-lg px-3 py-3 hover:bg-[var(--surface-soft)] hover:text-[color:var(--gold)]">Ceník</a>
        <a href="contact.php" class="block rounded-lg px-3 py-3 hover:bg-[var(--surface-soft)] hover:text-[color:var(--gold)]">Kontakt</a>
        <a href="#booking" class="mt-2 inline-flex w-full items-center justify-center rounded-lg bg-[var(--accent)] px-4 py-3 font-semibold text-[color:var(--cream)] shadow-sm transition hover:bg-[var(--accent-dark)] focus:outline-none focus:ring-2 focus:ring-[var(--gold)]">Rezervovat termín</a>
        <a href="<?= htmlspecialchars($instagramUrl, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener" class="flex items-center gap-2 rounded-lg px-3 py-3 hover:bg-[var(--surface-soft)] hover:text-[color:var(--gold)]">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <rect x="4" y="4" width="16" height="16" rx="5" stroke="currentColor" stroke-width="2" />
                <circle cx="12" cy="12" r="3.5" stroke="currentColor" stroke-width="2" />
                <path d="M17 7.2h.01" stroke="currentColor" stroke-width="3" stroke-linecap="round" />
            </svg>
            Instagram
        </a>
    </nav>
</header>

<main id="top" class="max-w-6xl mx-auto px-4 pb-14 sm:px-6 md:pb-16">

    <!-- HERO sekce-->
    <section class="homepage-hero">
        <div class="homepage-hero__media" aria-hidden="true">
            <img src="assets/homepage-hero-wide.png" alt="">
        </div>
        <div class="homepage-hero__inner">
            <div class="homepage-hero__copy">
                <p class="homepage-hero__eyebrow mb-4 text-[11px] font-bold uppercase tracking-[0.24em] sm:text-xs">
                    <span class="inline-flex items-center gap-2">
                        <span class="hero-badge-pulse inline-block h-1.5 w-1.5 rounded-full bg-[var(--gold)]"></span>
                        Pánské kadeřnictví · Brno
                    </span>
                </p>
                <h1 class="homepage-hero__title">
                    Pánské<br>
                    kadeřnictví<br>
                    v Brně<br>
                    <span class="homepage-hero__title-accent hero-title-shimmer">By ReneNeme</span>
                </h1>
                <p class="homepage-hero__intro mt-6 text-base font-semibold leading-7 sm:text-lg">
                    Pánské střihy s čistým tvarem, pohodovou domluvou a rezervací na pár kliknutí.
                </p>
                <div class="hero-info-grid mt-4">
                    <div class="hero-info-pill">
                        <span class="hero-info-pill__icon" aria-hidden="true">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none">
                                <path d="M12 6v6l4 2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" stroke="currentColor" stroke-width="2" />
                            </svg>
                        </span>
                        <span>
                            <span class="block text-[11px] font-bold uppercase tracking-[0.2em] text-[color:var(--muted-strong)]">Otevírací doba</span>
                            <strong class="mt-1 block text-sm text-[color:var(--cream)]"><?= htmlspecialchars($businessOpeningHoursLabel, ENT_QUOTES, 'UTF-8') ?></strong>
                        </span>
                    </div>
                    <a href="<?= htmlspecialchars($businessMapUrl, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer" class="hero-info-pill transition hover:-translate-y-0.5 hover:border-[rgba(216,191,122,0.42)] hover:text-[color:var(--gold-soft)]">
                        <span class="hero-info-pill__icon" aria-hidden="true">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none">
                                <path d="M12 21s7-5.1 7-11a7 7 0 1 0-14 0c0 5.9 7 11 7 11Z" stroke="currentColor" stroke-width="2" />
                                <path d="M12 12.2a2.2 2.2 0 1 0 0-4.4 2.2 2.2 0 0 0 0 4.4Z" stroke="currentColor" stroke-width="2" />
                            </svg>
                        </span>
                        <span>
                            <span class="block text-[11px] font-bold uppercase tracking-[0.2em] text-[color:var(--muted-strong)]">Adresa</span>
                            <strong class="mt-1 block text-sm text-[color:var(--cream)]"><?= htmlspecialchars($businessAddress, ENT_QUOTES, 'UTF-8') ?></strong>
                        </span>
                    </a>
                </div>
                <div class="mt-5 flex flex-col gap-3 sm:flex-row sm:flex-wrap">
                    <a href="#booking" class="ui-button ripple-btn w-full focus:outline-none focus:ring-2 focus:ring-[var(--accent)] sm:w-auto">
                        Rezervovat termín
                    </a>
                    <a href="cenik.php" class="ui-button-ghost-dark ripple-btn w-full focus:outline-none focus:ring-2 focus:ring-[var(--gold)] sm:w-auto">
                        Ceník služeb
                    </a>
                </div>
            </div>
            <details class="homepage-hero__card p-4 text-[color:var(--cream)] sm:p-5">
                <summary class="homepage-hero__card-toggle">
                    <span>
                        <span class="block text-[10px] font-bold uppercase tracking-[0.24em] text-[color:var(--muted-strong)] sm:text-xs">Před návštěvou</span>
                        <span class="mt-2 block text-base font-extrabold sm:text-lg">Jak probíhá rezervace</span>
                    </span>
                    <span class="homepage-hero__card-icon" aria-hidden="true">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none">
                            <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                        </svg>
                    </span>
                </summary>
                <div class="homepage-hero__card-body">
                    <ul class="homepage-hero__steps">
                        <li>Vybereš službu, datum a čas.</li>
                        <li>Po odeslání žádost zkontrolujeme.</li>
                        <li>Po potvrzení dorazí stručné shrnutí e-mailem.</li>
                    </ul>
                </div>
            </details>
        </div>
    </section>

    <!-- O NÁS -->
    <section id="about" class="scroll-mt-28 border-t border-[var(--line)] py-8 md:scroll-mt-32 md:py-10">
        <div class="premium-surface section-reveal p-5 sm:p-6 lg:p-8">
            <div class="grid gap-7 lg:grid-cols-[1.05fr_0.95fr] lg:items-start">
                <div class="max-w-2xl">
                    <p class="text-xs font-bold uppercase tracking-[0.24em] text-[color:var(--muted-strong)]">Proč právě sem</p>
                    <h2 class="mt-2 text-2xl font-bold sm:text-3xl">Poctivý střih, klidná návštěva a rezervace bez zmatku</h2>
                    <p class="mt-4 text-sm leading-7 text-[color:var(--muted)] sm:text-[15px]">
                        <strong>Hair By ReneNeme</strong> je pánské kadeřnictví v Brně, kde se ladí hlavně výsledek,
                        který bude fungovat i doma. Bez zbytečné omáčky, ale s pečlivostí a normální domluvou.
                    </p>
                    <div class="mt-6 grid gap-3 sm:grid-cols-3 stagger-reveal">
                        <div class="glow-card rounded-2xl border border-[var(--line)] bg-[rgba(31,29,25,0.82)] px-4 py-4 shadow-sm">
                            <p class="text-sm font-semibold text-[color:var(--cream)]">1. Domluva</p>
                            <p class="mt-2 text-sm leading-6 text-[color:var(--muted)]">Krátce si řekneme styl i praktické očekávání.</p>
                        </div>
                        <div class="glow-card rounded-2xl border border-[var(--line)] bg-[rgba(31,29,25,0.82)] px-4 py-4 shadow-sm">
                            <p class="text-sm font-semibold text-[color:var(--cream)]">2. Střih</p>
                            <p class="mt-2 text-sm leading-6 text-[color:var(--muted)]">Pečlivě, bez spěchu a podle toho, co ti sedí.</p>
                        </div>
                        <div class="glow-card rounded-2xl border border-[var(--line)] bg-[rgba(31,29,25,0.82)] px-4 py-4 shadow-sm">
                            <p class="text-sm font-semibold text-[color:var(--cream)]">3. Hotovo</p>
                            <p class="mt-2 text-sm leading-6 text-[color:var(--muted)]">Upravený výsledek, který drží i další dny.</p>
                        </div>
                    </div>
                </div>
                <div class="grid gap-3 text-sm">
            <article class="about-card premium-surface lift-card p-4">
                <button
                    type="button"
                    class="about-card__button flex w-full items-start justify-between gap-4 text-left"
                    data-about-title="Přátelský přístup"
                    data-about-summary="Klidná návštěva bez zbytečného spěchu. Sedneš si, domluvíme styl a jde se na věc"
                    data-about-detail="Renata se ptá na styl, zvyky i to, jak moc chceš účes ráno řešit. Cílem je střih, který bude vypadat dobře nejen po odchodu z křesla, ale i další dny doma."
                >
                    <span class="max-w-md">
                        <span class="mb-1 block font-semibold">Přátelský přístup</span>
                        <span class="block leading-6 text-[color:var(--muted)]">Klidná návštěva bez zbytečného spěchu. Sedneš si, domluvíme styl a jde se na věc.</span>
                    </span>
                    <span class="about-card__icon mt-1 inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[var(--cream)] text-[color:var(--accent)]" aria-hidden="true">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none">
                            <path d="M7 17L17 7M9 7h8v8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </span>
                </button>
            </article>
            <article class="about-card premium-surface lift-card p-4">
                <button
                    type="button"
                    class="about-card__button flex w-full items-start justify-between gap-4 text-left"
                    data-about-title="Moderní střihy"
                    data-about-summary="Od klasiky po výraznější fade. Vždy tak, aby střih seděl k vlasům i běžnému nošení"
                    data-about-detail="Střih může být čistý, výrazný nebo úplně přirozený. Klidně dones fotku inspirace, výsledek se ale vždy upraví podle tvaru hlavy, hustoty vlasů a toho, co ti bude prakticky fungovat."
                >
                    <span class="max-w-md">
                        <span class="mb-1 block font-semibold">Moderní střihy</span>
                        <span class="block leading-6 text-[color:var(--muted)]">Od klasiky po výraznější fade. Vždy tak, aby střih seděl k vlasům i běžnému nošení.</span>
                    </span>
                    <span class="about-card__icon mt-1 inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[var(--cream)] text-[color:var(--accent)]" aria-hidden="true">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none">
                            <path d="M7 17L17 7M9 7h8v8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </span>
                </button>
            </article>
            <article class="about-card premium-surface lift-card p-4">
                <button
                    type="button"
                    class="about-card__button flex w-full items-start justify-between gap-4 text-left"
                    data-about-title="Přehledná rezervace"
                    data-about-summary="Systém hlídá délku služby i dostupné časy, takže výběr termínu je rychlý a jasný"
                    data-about-detail="Při výběru termínu se počítá s délkou služby i aktuální dostupností. Po odeslání žádost zkontrolujeme a po potvrzení dorazí shrnutí e-mailem."
                >
                    <span class="max-w-md">
                        <span class="mb-1 block font-semibold">Přehledná rezervace</span>
                        <span class="block leading-6 text-[color:var(--muted)]">Systém hlídá délku služby i dostupné časy, takže výběr termínu je rychlý a jasný.</span>
                    </span>
                    <span class="about-card__icon mt-1 inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[var(--cream)] text-[color:var(--accent)]" aria-hidden="true">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none">
                            <path d="M7 17L17 7M9 7h8v8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </span>
                </button>
            </article>
                </div>
            </div>
        </div>
    </section>

    <!-- REFERENCE STŘIHŮ -->
    <section class="border-t border-[var(--line)] py-8 md:py-10">
        <div class="reference-showcase section-reveal--left section-reveal p-4 sm:p-5 lg:p-6">
            <div class="grid gap-5 lg:grid-cols-[0.68fr_1.32fr] lg:items-start">
                <div class="reference-intro p-5 sm:p-6 lg:p-7">
                    <span class="inline-flex rounded-full border border-[rgba(17,17,17,0.22)] bg-[rgba(31,29,25,0.78)] px-3 py-1 text-[11px] font-bold uppercase tracking-[0.22em] text-[color:var(--muted-strong)]">Inspirace před rezervací</span>
                    <h2 class="mt-4 max-w-md text-2xl font-bold leading-tight sm:text-[2.2rem]">Vyber si střih, který ti sedí</h2>
                    <p class="mt-4 max-w-md text-sm leading-7 text-[color:var(--muted)] sm:text-[15px]">
                        Otevři reference a projdi si styl, který chceš.
                    </p>

                    <div class="mt-5 inline-flex items-center gap-2 rounded-full border border-[rgba(183,154,85,0.18)] bg-[rgba(31,29,25,0.7)] px-3 py-2 text-sm font-medium text-[color:var(--muted)] shadow-sm">
                        <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-[rgba(183,154,85,0.12)] text-[color:var(--accent)]" aria-hidden="true">
                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none">
                                <path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </span>
                        Klikni na ukázku
                    </div>

                    <div class="mt-6 flex flex-col items-start gap-3 sm:flex-row sm:flex-wrap">
                        <a href="references.php" class="ui-button focus:outline-none focus:ring-2 focus:ring-[var(--gold)]">
                            Otevřít reference
                        </a>
                        <a href="<?= htmlspecialchars($instagramUrl, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener" class="ui-button-secondary focus:outline-none focus:ring-2 focus:ring-[var(--gold)]">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <rect x="4" y="4" width="16" height="16" rx="5" stroke="currentColor" stroke-width="2" />
                                <circle cx="12" cy="12" r="3.5" stroke="currentColor" stroke-width="2" />
                                <path d="M17 7.2h.01" stroke="currentColor" stroke-width="3" stroke-linecap="round" />
                            </svg>
                            Instagram
                        </a>
                    </div>
                </div>
                <div class="reference-gallery-grid">
                <?php foreach (array_slice($referenceCuts, 0, 3) as $index => $cut): ?>
                <?php /* reveal-item added for stagger animation */ ?>
                    <?php
                    $hasImage = is_file(__DIR__ . '/' . $cut['image']);
                    $webpImage = preg_replace('/\.jpe?g$/i', '.webp', $cut['image']);
                    $hasWebpImage = is_string($webpImage) && is_file(__DIR__ . '/' . $webpImage);
                    $cardClass = $index === 0 ? 'reference-card--featured' : 'reference-card--compact';
                    $imageClass = ['reference-card__image--natural', 'reference-card__image--fade', 'reference-card__image--clean'][$index] ?? 'reference-card__image--natural';
                    $usesTransparentMedia = !empty($cut['transparent_media']);
                    $mediaClass = $usesTransparentMedia ? ' reference-card__media--transparent' : '';
                    $transparentImageClass = $usesTransparentMedia ? ' reference-card__image--transparent' : '';
                    ?>
                    <a href="references.php" class="reference-card lift-card reveal-item group <?= $cardClass ?>">
                        <?php if ($hasImage): ?>
                            <picture class="reference-card__media<?= $mediaClass ?>">
                                <?php if ($hasWebpImage): ?>
                                    <source srcset="<?= htmlspecialchars($webpImage, ENT_QUOTES, 'UTF-8') ?>" type="image/webp">
                                <?php endif; ?>
                                <img
                                    src="<?= htmlspecialchars($cut['image'], ENT_QUOTES, 'UTF-8') ?>"
                                    alt="<?= htmlspecialchars($cut['title'], ENT_QUOTES, 'UTF-8') ?>"
                                    width="1012"
                                    height="1800"
                                    class="<?= $imageClass ?><?= $transparentImageClass ?> h-full w-full object-cover transition duration-500 group-hover:scale-[1.03]"
                                    loading="lazy"
                                    decoding="async"
                                >
                            </picture>
                        <?php else: ?>
                            <div class="reference-card__media flex items-center justify-center bg-[linear-gradient(145deg,rgba(0,0,0,0.96),rgba(35,35,35,0.94))] px-5 text-center text-[color:var(--cream)]">
                                <p class="text-sm font-semibold"><?= htmlspecialchars($cut['title'], ENT_QUOTES, 'UTF-8') ?></p>
                            </div>
                        <?php endif; ?>
                        <div class="reference-card__content">
                            <span class="reference-card__eyebrow"><?= $index === 0 ? 'Nejoblíbenější styl' : 'Ukázka střihu' ?></span>
                            <div class="reference-card__panel">
                                <p class="text-lg font-semibold leading-tight sm:text-xl"><?= htmlspecialchars($cut['title'], ENT_QUOTES, 'UTF-8') ?></p>
                                <p class="reference-card__hint mt-2 text-sm leading-6"><?= htmlspecialchars($cut['description'], ENT_QUOTES, 'UTF-8') ?></p>
                                <p class="reference-card__cta mt-3 text-xs font-semibold uppercase tracking-[0.18em]">
                                    <span>Otevřít galerii</span>
                                    <span class="reference-card__cta-icon" aria-hidden="true">
                                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none">
                                            <path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </span>
                                </p>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>
    <!-- SLUŽBY -->
    <section id="services" class="scroll-mt-28 border-t border-[var(--line)] py-8 md:scroll-mt-32 md:py-10">
        <div class="section-reveal flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-2xl">
                <p class="text-xs font-bold uppercase tracking-[0.24em] text-[color:var(--muted-strong)]">Služby</p>
                <h2 class="mt-2 text-2xl font-bold sm:text-[2rem]">Vyber si, co ti sedí</h2>
                <p class="mt-4 text-sm leading-7 text-[color:var(--muted)] sm:text-[15px]">
                    Tady najdeš nejčastější volby. Pokud chceš detailnější porovnání, ceny i délky návštěvy,
                    mrkni do kompletního ceníku.
                </p>
            </div>
            <div>
                <a href="cenik.php" class="ui-button focus:outline-none focus:ring-2 focus:ring-[var(--gold)]">
                    Zobrazit celý ceník
                </a>
            </div>
        </div>
        <div class="mt-6 grid gap-4 md:grid-cols-2 stagger-reveal">
            <?php foreach ($serviceEntries as $entry): ?>
                <?php
                $serviceName = $entry['name'];
                $service = $entry['data'];
                $isFeatured = !empty($service['featured']);
                ?>
                <article class="service-card-hover group relative overflow-hidden rounded-2xl border <?= $isFeatured ? 'border-[var(--accent)] bg-[linear-gradient(135deg,rgba(0,0,0,0.98),rgba(35,35,35,0.94))] text-[color:var(--cream)] shadow-xl' : 'border-[var(--line)] bg-[rgba(31,29,25,0.82)] text-[color:var(--cream)] shadow-sm' ?>">
                    <div class="absolute inset-x-0 top-0 h-1 <?= $isFeatured ? 'bg-[linear-gradient(90deg,var(--gold),var(--gold-soft))]' : 'bg-[linear-gradient(90deg,var(--accent),rgba(17,17,17,0.12))]' ?>"></div>
                    <div class="p-5 sm:p-6">
                        <div class="flex flex-col items-start gap-4">
                            <div class="w-full">
                                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                    <div>
                                        <span class="inline-flex rounded-full border px-3 py-1 text-[11px] font-bold uppercase tracking-[0.2em] <?= $isFeatured ? 'border-[rgba(218,218,213,0.32)] bg-[rgba(218,218,213,0.12)] text-[color:var(--gold-soft)]' : 'border-[var(--line)] bg-[var(--field)] text-[color:var(--muted-strong)]' ?>">
                                            <?= htmlspecialchars((string) ($service['badge'] ?? 'Služba'), ENT_QUOTES, 'UTF-8') ?>
                                        </span>
                                        <h3 class="mt-4 text-xl font-bold"><?= htmlspecialchars($serviceName, ENT_QUOTES, 'UTF-8') ?></h3>
                                    </div>
                                    <div class="rounded-2xl border px-4 py-3 text-left <?= $isFeatured ? 'border-[rgba(218,218,213,0.18)] bg-[rgba(255,255,255,0.06)] text-[color:var(--cream)]' : 'border-[var(--line)] bg-[rgba(31,29,25,0.82)] text-[color:var(--cream)]' ?>">
                                        <p class="text-[11px] font-bold uppercase tracking-[0.18em] <?= $isFeatured ? 'text-[color:var(--gold-soft)]' : 'text-[color:var(--muted-strong)]' ?>">Od</p>
                                        <p class="mt-1 text-xl font-bold <?= $isFeatured ? 'text-[color:var(--gold-soft)]' : 'text-[color:var(--gold)]' ?>"><?= htmlspecialchars((string) ($service['price_label'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
                                    </div>
                                </div>
                                <p class="mt-2 max-w-md text-sm leading-6 <?= $isFeatured ? 'text-[color:var(--cream-soft)]' : 'text-[color:var(--muted)]' ?>">
                                    <?= htmlspecialchars((string) ($service['description'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                                </p>
                                <p class="mt-3 max-w-md text-sm leading-6 <?= $isFeatured ? 'text-[color:var(--cream-soft)]' : 'text-[color:var(--muted)]' ?>">
                                    <?= htmlspecialchars((string) ($service['service_copy'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                                </p>
                                <?php if (!empty($service['meta'])): ?>
                                    <p class="mt-3 inline-flex rounded-full border px-3 py-1.5 text-xs font-semibold <?= $isFeatured ? 'border-[rgba(218,218,213,0.18)] bg-[rgba(255,255,255,0.05)] text-[color:var(--cream-soft)]' : 'border-[var(--line)] bg-[var(--field)] text-[color:var(--muted)]' ?>">
                                        <?= htmlspecialchars((string) $service['meta'], ENT_QUOTES, 'UTF-8') ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="mt-5 flex flex-col items-start gap-3 border-t pt-4 sm:flex-row sm:flex-wrap sm:items-center sm:justify-between <?= $isFeatured ? 'border-[rgba(218,218,213,0.18)]' : 'border-[var(--line-soft)]' ?>">
                            <p class="text-sm font-semibold <?= $isFeatured ? 'text-[color:var(--gold-soft)]' : 'text-[color:var(--gold)]' ?>">
                                cca <?= htmlspecialchars((string) $service['duration'], ENT_QUOTES, 'UTF-8') ?> minut
                            </p>
                            <a
                                href="#booking"
                                data-book-service="<?= htmlspecialchars($serviceName, ENT_QUOTES, 'UTF-8') ?>"
                                class="inline-flex items-center justify-center rounded-xl border px-4 py-2.5 text-sm font-semibold transition focus:outline-none focus:ring-2 focus:ring-[var(--gold)] <?= $isFeatured ? 'border-[rgba(216,191,122,0.28)] bg-[rgba(255,255,255,0.08)] text-[color:var(--gold-soft)] hover:-translate-y-0.5 hover:border-[rgba(216,191,122,0.5)] hover:bg-[rgba(216,191,122,0.18)] hover:text-[color:var(--cream)]' : 'border-[rgba(216,191,122,0.28)] bg-[rgba(216,191,122,0.16)] text-[color:var(--gold-soft)] shadow-sm hover:-translate-y-0.5 hover:border-[rgba(216,191,122,0.5)] hover:bg-[rgba(216,191,122,0.24)] hover:text-[color:var(--cream)]' ?>"
                            >
                                Vybrat a rezervovat
                            </a>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
        <div class="mt-5 section-reveal glow-card rounded-2xl border border-[var(--line)] bg-[rgba(31,29,25,0.82)] p-5 shadow-sm">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-[color:var(--muted-strong)]">Dárkové poukazy</p>
                    <h3 class="mt-2 text-xl font-bold">Poukaz na střih jako dárek</h3>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-[color:var(--muted)]">
                        Poukaz připravíme podle domluvy. Ozvi se telefonicky nebo e-mailem a doladíme částku i předání.
                    </p>
                </div>
                <div class="flex flex-col gap-3 sm:flex-row">
                    <a href="tel:+420608419610" class="inline-flex items-center justify-center rounded-lg bg-[var(--accent)] px-4 py-2.5 text-sm font-semibold text-[color:var(--cream)] transition hover:bg-[var(--accent-dark)] focus:outline-none focus:ring-2 focus:ring-[var(--gold)]">
                        Zavolat
                    </a>
                    <a href="mailto:renenemehair@seznam.cz?subject=D%C3%A1rkov%C3%BD%20poukaz%20Hair%20By%20ReneNeme" class="inline-flex items-center justify-center rounded-lg border border-[var(--line)] px-4 py-2.5 text-sm font-semibold text-[color:var(--cream)] transition hover:border-[var(--accent)] hover:text-[color:var(--gold-soft)] focus:outline-none focus:ring-2 focus:ring-[var(--gold)]">
                        Napsat e-mail
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- REZERVAČNÍ FORMULÁŘ -->
    <section id="booking" class="mt-2 scroll-mt-24 border-t border-[var(--line)] py-8 md:mt-4 md:scroll-mt-28 md:py-10">
        <div class="grid gap-6 md:grid-cols-[0.8fr_1.2fr] md:items-start md:gap-8">
            <div class="order-1 section-reveal section-reveal--left space-y-5">
                <p class="text-xs uppercase tracking-[0.24em] text-[color:var(--muted-strong)] font-bold">Rezervace</p>
                <h2 class="mt-1 text-2xl font-bold mb-3">Vyber si termín online</h2>
                <p class="max-w-lg text-sm leading-6 text-[color:var(--muted)]">
                    Vyplň pár údajů, zvol službu a vyber si volný čas. O zbytek se postará systém
                    a po potvrzení dorazí shrnutí e-mailem.
                </p>

                <div class="rounded-3xl border border-[var(--surface-soft)] bg-[linear-gradient(145deg,rgba(0,0,0,0.98),rgba(35,35,35,0.94))] p-5 text-[color:var(--cream)] shadow-xl sm:p-6">
                    <p class="text-[11px] font-bold uppercase tracking-[0.22em] text-[color:var(--gold)]">Jak to funguje</p>
                    <h3 class="mt-3 text-xl font-bold">Jednoduše, rychle a bez čekání</h3>
                    <div class="mt-5 space-y-3 text-sm text-[color:var(--cream-soft)] stagger-reveal">
                        <div class="flex items-start gap-3">
                            <span class="step-dot mt-1 h-2.5 w-2.5 shrink-0 rounded-full bg-[var(--gold)]"></span>
                            <p>Vybereš službu a systém rovnou nabídne reálně volné časy</p>
                        </div>
                        <div class="flex items-start gap-3">
                            <span class="step-dot mt-1 h-2.5 w-2.5 shrink-0 rounded-full bg-[var(--gold)]"></span>
                            <p>Doplníš kontakt a odešleš žádost během chvilky</p>
                        </div>
                        <div class="flex items-start gap-3">
                            <span class="step-dot mt-1 h-2.5 w-2.5 shrink-0 rounded-full bg-[var(--gold)]"></span>
                            <p>Po schválení rezervace ti přijde potvrzení e-mailem</p>
                        </div>
                    </div>
                    <div class="mt-5 border-t border-[rgba(218,218,213,0.16)] pt-4 text-sm text-[color:var(--cream-soft)]">
                        <p><strong class="text-[color:var(--cream)]"><?= htmlspecialchars($businessOpeningHoursLabel, ENT_QUOTES, 'UTF-8') ?></strong> · <?= htmlspecialchars($businessAddress, ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                </div>

                <div class="booking-map-card section-reveal section-reveal--left">
                    <div class="p-4 sm:p-5">
                        <p class="text-[11px] font-bold uppercase tracking-[0.22em] text-[color:var(--gold)]">Kde nás najdeš</p>
                        <h3 class="mt-2 text-lg font-bold">Vackova ulice, Brno-Královo Pole</h3>
                        <p class="mt-2 text-sm leading-6 text-[color:var(--muted)]">
                            Mapa je přímo tady, kdyby sis chtěl před rezervací rychle ověřit cestu.
                        </p>
                    </div>
                    <iframe
                        title="Mapa Hair By ReneNeme"
                        src="<?= htmlspecialchars($businessMapEmbedUrl, ENT_QUOTES, 'UTF-8') ?>"
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                    ></iframe>
                    <div class="border-t border-[rgba(216,191,122,0.16)] p-4 sm:p-5">
                        <a
                            href="<?= htmlspecialchars($businessMapUrl, ENT_QUOTES, 'UTF-8') ?>"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="map-action-button focus:outline-none focus:ring-2 focus:ring-[var(--gold)]"
                        >
                            <span class="map-action-button__icon" aria-hidden="true">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none">
                                    <path d="M12 21s7-5.1 7-11a7 7 0 1 0-14 0c0 5.9 7 11 7 11Z" stroke="currentColor" stroke-width="2" />
                                    <path d="M12 12.2a2.2 2.2 0 1 0 0-4.4 2.2 2.2 0 0 0 0 4.4Z" stroke="currentColor" stroke-width="2" />
                                </svg>
                            </span>
                            <span class="map-action-button__text">Otevřít v Google Maps</span>
                        </a>
                    </div>
                </div>
            </div>

            <div class="order-2 section-reveal section-reveal--right space-y-4">
                <form id="bookingForm" action="save_reservation.php" method="POST" class="booking-form scroll-mt-24 space-y-4 rounded-lg border border-[var(--line)] bg-[linear-gradient(180deg,rgba(31,29,25,0.96),rgba(14,13,11,0.96))] p-4 text-[color:var(--cream)] sm:p-5 md:scroll-mt-28 md:p-6">
                    <?= app_csrf_field() ?>
                    <input type="hidden" name="form_started_at" value="<?= app_booking_form_started_at() ?>">
                    <div class="hidden" aria-hidden="true">
                        <label for="website">Web</label>
                        <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
                    </div>

                    <!-- Jméno -->
                    <div>
                        <label for="name" class="block text-sm font-medium mb-1">Jak se jmenuješ *</label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            required
                            class="w-full rounded-lg bg-[var(--field)] border border-[var(--field-border)] px-3 py-2 text-base text-[color:var(--field-text)] sm:text-sm focus:outline-none focus:ring-2 focus:ring-[var(--gold)]"
                            placeholder="Např. Jan Novák"
                        >
                    </div>

                    <!-- E-mail -->
                    <div>
                        <label for="email" class="block text-sm font-medium mb-1">E-mail *</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            required
                            class="w-full rounded-lg bg-[var(--field)] border border-[var(--field-border)] px-3 py-2 text-base text-[color:var(--field-text)] sm:text-sm focus:outline-none focus:ring-2 focus:ring-[var(--gold)]"
                            placeholder="např. jan.novak@email.cz"
                        >
                    </div>

                    <!-- Telefon -->
                    <div>
                        <label for="phone" class="block text-sm font-medium mb-1">Telefon pro rychlou domluvu *</label>
                        <input
                            type="tel"
                            id="phone"
                            name="phone"
                            required
                            inputmode="tel"
                            autocomplete="tel"
                            pattern="^[+0-9 ().\/-]{9,25}$"
                            title="Zadej telefonní číslo, například +420 777 123 456"
                            class="w-full rounded-lg bg-[var(--field)] border border-[var(--field-border)] px-3 py-2 text-base text-[color:var(--field-text)] sm:text-sm focus:outline-none focus:ring-2 focus:ring-[var(--gold)]"
                            placeholder="např. +420 777 123 456"
                        >
                    </div>

                    <!-- Datum + čas -->
                    <div class="booking-slot-grid grid grid-cols-1 sm:grid-cols-2">
                        <div class="min-w-0">
                            <label for="date" class="block text-sm font-medium mb-1">Kdy se ti to hodí *</label>
                            <div class="booking-date-shell">
                                <input
                                    type="date"
                                    id="date"
                                    name="date"
                                    required
                                    class="booking-control booking-control--date"
                                >
                                <span id="dateDisplay" class="booking-date-display" aria-hidden="true">Vyber datum</span>
                            </div>
                        </div>
                        <div class="min-w-0">
                            <label for="time" class="block text-sm font-medium mb-1">Volný čas *</label>
                            <select
                                id="time"
                                name="time"
                                required
                                class="booking-control booking-control--select"
                            >
                                <option value="">Nejprve vyber datum</option>
                            </select>
                        </div>
                    </div>

                    <!-- Typ služby -->
                    <div>
                        <label for="service" class="block text-sm font-medium mb-1">Co budeme stříhat *</label>
                        <select
                            id="service"
                            name="service"
                            required
                            class="booking-control booking-control--select"
                        >
                            <option value="">Vyber službu...</option>
                            <?php foreach ($services as $serviceName => $service): ?>
                                <option value="<?= htmlspecialchars($serviceName, ENT_QUOTES, 'UTF-8') ?>">
                                    <?= htmlspecialchars($serviceName . ' (' . (string) ($service['price_label'] ?? '') . ')', ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <p id="priceInfo" class="mt-1 text-xs text-[color:var(--cream-soft)] hidden">
                            Vybraná služba: <span id="priceValue"></span>
                        </p>
                        <div id="selectedServiceCard" class="selected-service-card mt-3" aria-live="polite">
                            <div class="min-w-0">
                                <p class="text-[10px] font-black uppercase tracking-[0.28em] text-[color:var(--gold-soft)]">Vybraná služba</p>
                                <h3 id="selectedServiceName" class="mt-2 text-lg font-extrabold text-[color:var(--cream)]"></h3>
                                <p id="selectedServiceCopy" class="mt-3 text-sm leading-6 text-[color:var(--cream-soft)]"></p>
                            </div>
                            <div class="selected-service-card__price shrink-0">
                                <p id="selectedServicePrice" class="text-xl font-black text-[color:var(--gold-soft)]"></p>
                                <p id="selectedServiceDuration" class="mt-2 text-xs font-bold text-[color:var(--cream)]"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Poznámka -->
                    <div>
                        <label for="note" class="block text-sm font-medium mb-3">Přání ke střihu (nepovinné)</label>
                        <textarea
                            id="note"
                            name="note"
                            rows="3"
                            class="w-full rounded-lg bg-[var(--field)] border border-[var(--field-border)] px-3 py-2 text-base text-[color:var(--field-text)] sm:text-sm focus:outline-none focus:ring-2 focus:ring-[var(--gold)]"
                            placeholder="Např. delší vlasy, speciální přání..."
                        ></textarea>
                    </div>

                    <!-- Souhlas -->
                    <div class="flex gap-2 text-xs text-[color:var(--cream-soft)]">
                        <input
                            type="checkbox"
                            id="gdpr"
                            name="gdpr"
                            required
                            class="mt-1 rounded border-[var(--field-border)] bg-[var(--cream)] accent-[var(--accent)]"
                        >
                        <label for="gdpr">
                            Souhlasím se zpracováním osobních údajů pro účely rezervace. Údaje slouží jen k domluvě a potvrzení termínu.
                        </label>
                    </div>

                    <!-- Chyba z JS -->
                    <p id="errorMsg" class="text-xs text-red-300 hidden"></p>
                    <p id="bookingLoadingMsg" class="hidden rounded-lg border border-[var(--surface-soft)] bg-[rgba(255,255,255,0.06)] px-3 py-2 text-xs text-[color:var(--cream-soft)]">
                        Žádost právě odesíláme. Chvilku vydrž, kontrolujeme termín a připravujeme shrnutí.
                    </p>

                    <!-- Tlačítko -->
                    <button
                        type="submit"
                        id="bookingSubmitButton"
                        class="ripple-btn inline-flex w-full items-center justify-center gap-2 rounded-lg bg-[var(--accent)] py-3 text-sm font-semibold shadow-lg transition hover:-translate-y-0.5 hover:bg-[var(--accent-dark)] hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-[var(--gold)] disabled:cursor-wait disabled:opacity-80"
                    >
                        <span id="bookingSubmitText">Požádat o termín</span>
                        <span id="bookingSubmitLoading" class="hidden items-center gap-2">
                            <span class="booking-submit__spinner h-4 w-4 rounded-full border-2 border-[rgba(245,237,225,0.42)] border-t-[var(--cream)]"></span>
                            Odesíláme žádost...
                        </span>
                    </button>
                </form>

                <div class="glow-card rounded-2xl border border-[var(--line)] bg-[rgba(31,29,25,0.82)] p-4 shadow-sm">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-[color:var(--muted-strong)]">Praktické info</p>
                            <p class="mt-2 text-sm leading-6 text-[color:var(--muted)]">
                                Pokud máš speciální přání ke střihu, napiš ho do poznámky už při rezervaci.
                            </p>
                        </div>
                        <div class="flex flex-col gap-3 sm:flex-row">
                            <a
                                href="<?= htmlspecialchars($businessMapUrl, ENT_QUOTES, 'UTF-8') ?>"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="inline-flex items-center justify-center rounded-lg border border-[var(--line)] px-4 py-2.5 text-sm font-semibold text-[color:var(--cream)] transition hover:-translate-y-0.5 hover:border-[var(--accent)] hover:text-[color:var(--gold-soft)] focus:outline-none focus:ring-2 focus:ring-[var(--gold)]"
                            >
                                Otevřít mapu
                            </a>
                            <a
                                href="tel:+420608419610"
                                class="inline-flex items-center justify-center rounded-lg border border-[var(--line)] px-4 py-2.5 text-sm font-semibold text-[color:var(--cream)] transition hover:-translate-y-0.5 hover:border-[var(--accent)] hover:text-[color:var(--gold-soft)] focus:outline-none focus:ring-2 focus:ring-[var(--gold)]"
                            >
                                Zavolat
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<div
    id="bookingLoadingOverlay"
    class="booking-loading-overlay fixed inset-0 z-[120] flex items-center justify-center bg-[rgba(0,0,0,0.76)] p-4 backdrop-blur-md"
    aria-hidden="true"
>
    <div class="booking-loading-card max-h-[calc(100vh-2rem)] w-full max-w-md overflow-y-auto rounded-lg border border-[var(--surface-soft)] bg-[var(--surface)] p-5 text-center shadow-2xl sm:p-6">
        <div class="booking-loading-pulse mx-auto mb-5 inline-flex h-16 w-16 items-center justify-center rounded-full bg-[var(--accent)] text-[color:var(--cream)] shadow-lg">
            <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M5 12.5l4.2 4.2L19 7" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </div>
        <p class="text-xs font-bold uppercase tracking-[0.24em] text-[color:var(--muted-strong)]">Rezervace se odesílá</p>
        <h2 class="mt-2 text-2xl font-extrabold text-[color:var(--cream)]">Držíme ti termín</h2>
        <p id="bookingLoadingStep" class="mt-3 text-sm leading-6 text-[color:var(--muted)]">
            Kontrolujeme dostupnost vybraného času...
        </p>
        <div class="mt-5 h-2 overflow-hidden rounded-full bg-[var(--line-soft)]">
            <div class="booking-loading-progress h-full w-full rounded-full bg-[var(--accent)]"></div>
        </div>
        <div class="mt-5 grid grid-cols-3 gap-2 text-[11px] font-semibold text-[color:var(--muted-strong)]">
            <span>Kontrola</span>
            <span>Uložení</span>
            <span>Potvrzení</span>
        </div>
        <p class="booking-loading-fallback mt-5 hidden text-xs leading-5 text-[color:var(--muted)]">
            Pokud to trvá déle, nech stránku ještě chvíli otevřenou. Rezervace se zpracovává na serveru.
        </p>
    </div>
</div>

<button
    type="button"
    id="scrollTopButton"
    class="scroll-top-button fixed bottom-5 right-4 z-[80] inline-flex h-12 w-12 items-center justify-center rounded-full border border-[rgba(17,17,17,0.18)] bg-[var(--surface)] text-[color:var(--gold-soft)] shadow-xl focus:outline-none focus:ring-2 focus:ring-[var(--gold)] sm:bottom-6 sm:right-6"
    aria-label="Zpět nahoru"
>
    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
        <path d="M12 18V6M12 6l-5 5M12 6l5 5" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" />
    </svg>
</button>

<div
    id="aboutPopover"
    class="about-popover fixed inset-0 z-[90] flex items-center justify-center bg-black/45 p-4 backdrop-blur-md"
    aria-hidden="true"
>
    <button
        type="button"
        id="aboutBackdrop"
        class="absolute inset-0 cursor-default"
        aria-label="Zavřít detail"
    ></button>
    <section class="about-popover__panel relative z-10 max-h-[calc(100vh-2rem)] w-full max-w-2xl overflow-y-auto rounded-[1.75rem] border border-[var(--line)] bg-[var(--surface)] shadow-2xl">
        <button
            type="button"
            id="aboutClose"
            class="absolute right-3 top-3 z-20 inline-flex h-10 w-10 items-center justify-center rounded-full bg-[rgba(0,0,0,0.78)] text-[color:var(--cream)] shadow-lg transition hover:bg-[var(--surface-soft)] focus:outline-none focus:ring-2 focus:ring-[var(--gold)]"
            aria-label="Zavřít detail"
        >
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
            </svg>
        </button>
        <div class="bg-[var(--surface)] px-5 py-5 pr-16 text-[color:var(--cream)] sm:px-7 sm:py-6">
            <p class="text-xs font-bold uppercase tracking-[0.22em] text-[color:var(--gold)]">Detail</p>
            <h3 id="aboutPopoverTitle" class="mt-2 text-2xl font-bold sm:text-3xl"></h3>
        </div>
        <div class="px-5 py-5 sm:px-7 sm:py-6">
            <p id="aboutPopoverSummary" class="text-base font-semibold text-[color:var(--cream)]"></p>
            <p id="aboutPopoverDetail" class="mt-4 leading-7 text-[color:var(--muted)]"></p>
            <a href="#booking" class="mt-6 inline-flex rounded-2xl bg-[var(--accent)] px-5 py-3 text-sm font-semibold text-[color:var(--cream)] shadow-md transition hover:-translate-y-0.5 hover:bg-[var(--accent-dark)] hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-[var(--gold)]">
                Rezervovat termín
            </a>
        </div>
    </section>
</div>

<div
    id="galleryLightbox"
    class="gallery-lightbox fixed inset-0 z-[100] flex items-center justify-center bg-black/60 p-4 backdrop-blur-md"
    aria-hidden="true"
>
    <button
        type="button"
        id="galleryBackdrop"
        class="absolute inset-0 cursor-default"
        aria-label="Zavřít fotografii"
    ></button>
    <div class="gallery-lightbox__panel relative z-10 max-h-[calc(100vh-2rem)] w-full max-w-4xl overflow-y-auto rounded-[1.75rem]">
        <button
            type="button"
            id="galleryClose"
            class="absolute right-3 top-3 z-20 inline-flex h-10 w-10 items-center justify-center rounded-full bg-[rgba(0,0,0,0.78)] text-[color:var(--cream)] shadow-lg transition hover:bg-[var(--surface-soft)] focus:outline-none focus:ring-2 focus:ring-[var(--gold)]"
            aria-label="Zavřít fotografii"
        >
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
            </svg>
        </button>
        <figure class="overflow-hidden rounded-[1.75rem] bg-[var(--surface)] shadow-2xl">
            <img
                id="galleryImage"
                src=""
                alt=""
                class="max-h-[68vh] w-full object-contain bg-[var(--surface)] sm:max-h-[78vh]"
            >
            <figcaption class="border-t border-[var(--surface-soft)] px-4 py-3 text-[color:var(--cream)] sm:px-5">
                <p id="galleryTitle" class="font-semibold"></p>
                <p id="galleryDescription" class="mt-1 text-sm text-[color:var(--cream-soft)]"></p>
            </figcaption>
        </figure>
    </div>
</div>

<!-- FOOTER -->
<footer class="border-t border-[var(--surface-soft)] bg-[var(--surface)] text-[color:var(--cream-soft)]">
    <div class="mx-auto grid max-w-6xl gap-6 px-4 py-8 sm:px-6 md:grid-cols-[1.2fr_0.8fr_0.8fr] stagger-reveal">
        <div>
            <p class="text-xl font-extrabold tracking-tight">
                <span class="text-[color:var(--cream)]">Hair By</span>
                <span class="text-[color:var(--gold)]">ReneNeme</span>
            </p>
            <p class="mt-3 max-w-sm text-sm leading-6 text-[color:var(--cream-soft)]">
                Pánské kadeřnictví v Brně pro čistý střih, pohodovou návštěvu a rezervaci bez zbytečného čekání.
            </p>
        </div>
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.22em] text-[color:var(--gold)]">Kontakt</p>
            <div class="mt-3 space-y-2 text-sm">
                <a href="tel:+420608419610" class="block hover:text-[color:var(--gold)]">+420 608 419 610</a>
                <a href="mailto:renenemehair@seznam.cz" class="block break-words hover:text-[color:var(--gold)]">renenemehair@seznam.cz</a>
                <a
                    href="<?= htmlspecialchars($businessMapUrl, ENT_QUOTES, 'UTF-8') ?>"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="block hover:text-[color:var(--gold)]"
                >
                    Vackova 1064/39, Brno
                </a>
            </div>
        </div>
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.22em] text-[color:var(--gold)]">Rychle</p>
            <div class="mt-3 space-y-2 text-sm">
                <a href="#booking" class="block hover:text-[color:var(--gold)]">Rezervace</a>
                <a href="cenik.php" class="block hover:text-[color:var(--gold)]">Ceník</a>
                <a href="<?= htmlspecialchars($instagramUrl, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener" class="block hover:text-[color:var(--gold)]">Instagram</a>
                <a href="contact.php" class="block hover:text-[color:var(--gold)]">Kontakt</a>
            </div>
        </div>
    </div>
    <div class="border-t border-[var(--surface-soft)] px-4 py-4 text-center text-xs text-[color:var(--cream-soft)]">
        <p>© <?php echo date('Y'); ?> Hair By ReneNeme · Web vytvořil Dejvidaak</p>
    </div>
</footer>


<!-- JavaScript – datum/čas + cena -->
<script>
    const serviceSelect = document.getElementById('service');
    const dateInput = document.getElementById('date');
    const dateDisplay = document.getElementById('dateDisplay');
    const timeInput = document.getElementById('time');
    const priceInfo = document.getElementById('priceInfo');
    const priceValue = document.getElementById('priceValue');
    const selectedServiceCard = document.getElementById('selectedServiceCard');
    const selectedServiceName = document.getElementById('selectedServiceName');
    const selectedServiceCopy = document.getElementById('selectedServiceCopy');
    const selectedServicePrice = document.getElementById('selectedServicePrice');
    const selectedServiceDuration = document.getElementById('selectedServiceDuration');
    const errorMsg = document.getElementById('errorMsg');
    const bookingFormElement = document.querySelector('#booking form');
    const bookingSubmitButton = document.getElementById('bookingSubmitButton');
    const bookingSubmitText = document.getElementById('bookingSubmitText');
    const bookingSubmitLoading = document.getElementById('bookingSubmitLoading');
    const bookingLoadingMsg = document.getElementById('bookingLoadingMsg');
    const bookingLoadingOverlay = document.getElementById('bookingLoadingOverlay');
    const bookingLoadingStep = document.getElementById('bookingLoadingStep');
    const bookingPrefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    const services = <?= json_encode($services, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;
    let isBookingSubmitting = false;
    let bookingLoadingStepTimer = null;
    let bookingSlowTimer = null;

    // Minimální dnešní datum
    const today = new Date();
    const todayDateStr = formatLocalDate(today);
    const maxBookingDate = new Date(today);
    maxBookingDate.setDate(maxBookingDate.getDate() + <?= (int) app_booking_max_advance_days() ?>);
    const maxBookingDateStr = formatLocalDate(maxBookingDate);
    dateInput.min = todayDateStr;
    dateInput.max = maxBookingDateStr;

    function formatDisplayDate(value) {
        const parts = value.split('-');
        if (parts.length !== 3) return 'Vyber datum';
        return `${parts[2]}.${parts[1]}.${parts[0]}`;
    }

    function updateDateDisplay() {
        if (!dateDisplay || !dateInput) return;
        dateDisplay.textContent = dateInput.value ? formatDisplayDate(dateInput.value) : 'Vyber datum';
    }

    dateInput.addEventListener('input', updateDateDisplay);
    dateInput.addEventListener('change', updateDateDisplay);
    window.addEventListener('pageshow', updateDateDisplay);
    updateDateDisplay();

    function updateSelectedServiceInfo() {
        const selected = serviceSelect.value;
        if (services[selected]) {
            priceValue.textContent = `${services[selected].price_label} · cca ${services[selected].duration} min`;
            priceInfo.classList.remove('hidden');
            if (selectedServiceName) selectedServiceName.textContent = services[selected].service_title || selected;
            if (selectedServiceCopy) selectedServiceCopy.textContent = services[selected].service_copy || services[selected].description || '';
            if (selectedServicePrice) selectedServicePrice.textContent = services[selected].price_label || '';
            if (selectedServiceDuration) selectedServiceDuration.textContent = `cca ${services[selected].duration} minut`;
            selectedServiceCard?.classList.add('is-visible');
        } else {
            priceInfo.classList.add('hidden');
            selectedServiceCard?.classList.remove('is-visible');
        }
    }

    // Zobrazení ceny pod selectem místo alertu
    serviceSelect.addEventListener('change', updateSelectedServiceInfo);
    updateSelectedServiceInfo();

    bookingFormElement?.querySelectorAll('input, select, textarea').forEach(field => {
        field.addEventListener('invalid', () => {
            errorMsg.textContent = field instanceof HTMLInputElement && field.id === 'phone'
                ? 'Mrkni prosím na telefon. Může být třeba +420 777 123 456.'
                : 'Ještě prosím doplň zvýrazněné pole.';
            errorMsg.classList.remove('hidden');
        });
    });

    // Kontrola, že nejde rezervovat minulost
    bookingFormElement?.addEventListener('submit', function(e) {
        if (isBookingSubmitting) {
            e.preventDefault();
            return;
        }

        errorMsg.classList.add('hidden');
        errorMsg.textContent = '';
        bookingLoadingMsg?.classList.add('hidden');

        const chosenDate = dateInput.value;
        const chosenTime = timeInput.value;

        if (!chosenDate || !chosenTime) return;

        const now = new Date();
        const todayStr = formatLocalDate(now);
        const nowTimeStr = now.toTimeString().slice(0, 5);

        if (chosenDate < todayStr) {
            e.preventDefault();
            errorMsg.textContent = "Tohle datum už je za námi. Vyber prosím některý z dalších dnů.";
            errorMsg.classList.remove('hidden');
            return;
        }

        if (chosenDate > maxBookingDateStr) {
            e.preventDefault();
            errorMsg.textContent = "Online jde vybrat termín nejvýše 7 dní dopředu.";
            errorMsg.classList.remove('hidden');
            return;
        }

        if (chosenDate === todayStr && chosenTime < nowTimeStr) {
            e.preventDefault();
            errorMsg.textContent = "Tenhle čas už dnes proběhl. Vyber prosím pozdější termín.";
            errorMsg.classList.remove('hidden');
            return;
        }

        isBookingSubmitting = true;
        bookingSubmitButton.disabled = true;
        bookingSubmitText?.classList.add('hidden');
        bookingSubmitLoading?.classList.remove('hidden');
        bookingSubmitLoading?.classList.add('inline-flex');
        bookingLoadingMsg?.classList.remove('hidden');
        bookingLoadingOverlay?.classList.add('is-visible');
        bookingLoadingOverlay?.classList.remove('is-slow');
        bookingLoadingOverlay?.setAttribute('aria-hidden', 'false');
        document.body.classList.add('overflow-hidden');

        const loadingSteps = [
            'Kontrolujeme, jestli je čas volný...',
            'Ukládáme tvoji žádost...',
            'Připravujeme shrnutí rezervace...'
        ];
        let loadingStepIndex = 0;
        if (bookingLoadingStep) {
            bookingLoadingStep.textContent = loadingSteps[loadingStepIndex];
        }
        window.clearInterval(bookingLoadingStepTimer);
        bookingLoadingStepTimer = window.setInterval(() => {
            loadingStepIndex = Math.min(loadingStepIndex + 1, loadingSteps.length - 1);
            if (bookingLoadingStep) {
                bookingLoadingStep.textContent = loadingSteps[loadingStepIndex];
            }
        }, 700);

        bookingSlowTimer = window.setTimeout(() => {
            bookingLoadingOverlay?.classList.add('is-slow');
        }, 6500);

        e.preventDefault();
        window.requestAnimationFrame(() => {
            window.setTimeout(() => {
                HTMLFormElement.prototype.submit.call(bookingFormElement);
            }, 320);
        });

    });

    window.addEventListener('pageshow', () => {
        window.clearTimeout(bookingSlowTimer);
        window.clearInterval(bookingLoadingStepTimer);
        isBookingSubmitting = false;
        bookingSubmitButton.disabled = false;
        bookingSubmitText?.classList.remove('hidden');
        bookingSubmitLoading?.classList.add('hidden');
        bookingSubmitLoading?.classList.remove('inline-flex');
        bookingLoadingMsg?.classList.add('hidden');
        bookingLoadingOverlay?.classList.remove('is-visible', 'is-slow');
        bookingLoadingOverlay?.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('overflow-hidden');
    });

    function formatLocalDate(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const siteHeader = document.querySelector('.site-header');
    const homepageHero = document.querySelector('.homepage-hero');
    const homepageHeroMedia = document.querySelector('.homepage-hero__media');
    const mobileMenuButton = document.getElementById('mobileMenuButton');
    const mobileMenu = document.getElementById('mobileMenu');
    const menuIconOpen = document.getElementById('menuIconOpen');
    const menuIconClose = document.getElementById('menuIconClose');
    const bookingSection = document.getElementById('booking');
    const bookingForm = document.getElementById('bookingForm');
    const nameInput = document.getElementById('name');
    const aboutPopover = document.getElementById('aboutPopover');
    const aboutPanel = document.querySelector('.about-popover__panel');
    const aboutTitle = document.getElementById('aboutPopoverTitle');
    const aboutSummary = document.getElementById('aboutPopoverSummary');
    const aboutDetail = document.getElementById('aboutPopoverDetail');
    const aboutClose = document.getElementById('aboutClose');
    const aboutBackdrop = document.getElementById('aboutBackdrop');
    const galleryLightbox = document.getElementById('galleryLightbox');
    const galleryImage = document.getElementById('galleryImage');
    const galleryTitle = document.getElementById('galleryTitle');
    const galleryDescription = document.getElementById('galleryDescription');
    const galleryClose = document.getElementById('galleryClose');
    const galleryBackdrop = document.getElementById('galleryBackdrop');
    const galleryPanel = document.querySelector('.gallery-lightbox__panel');
    const scrollTopButton = document.getElementById('scrollTopButton');
    const pageFooter = document.querySelector('footer');
    const dateInput = document.getElementById('date');
    const timeSelect = document.getElementById('time');
    const serviceSelect = document.getElementById('service');
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    const mobileViewport = window.matchMedia('(max-width: 767px)');
    let previouslyFocusedElement = null;
    let aboutPreviouslyFocusedElement = null;
    let activeAboutTrigger = null;
    let aboutCloseTimer = null;
    let activeGalleryTrigger = null;
    let galleryCloseTimer = null;

    function updateSiteHeader() {
        siteHeader?.classList.toggle('is-scrolled', window.scrollY > 16);
    }

    function updateHeroParallax() {
        if (!homepageHero || !homepageHeroMedia || prefersReducedMotion.matches) return;

        const rect = homepageHero.getBoundingClientRect();
        const progress = Math.min(1, Math.max(0, -rect.top / Math.max(1, rect.height)));
        homepageHeroMedia.style.setProperty('--hero-media-y', `${progress * 86}px`);
    }

    function getBookingScrollTarget() {
        return mobileViewport.matches && bookingForm ? bookingForm : bookingSection;
    }

    function smoothScrollToSection(target, updateHash = true, hashId = '') {
        if (!target) return;
        target.scrollIntoView({
            behavior: prefersReducedMotion.matches ? 'auto' : 'smooth',
            block: 'start',
        });

        const targetHash = hashId || target.id;
        if (updateHash && targetHash) {
            window.history.pushState(null, '', `#${targetHash}`);
        }
    }

    function setAboutStartFromTrigger(trigger) {
        if (!aboutPanel || !trigger) return;

        const triggerRect = trigger.getBoundingClientRect();
        const viewportCenterX = window.innerWidth / 2;
        const viewportCenterY = window.innerHeight / 2;
        const triggerCenterX = triggerRect.left + triggerRect.width / 2;
        const triggerCenterY = triggerRect.top + triggerRect.height / 2;
        const scale = Math.min(0.82, Math.max(0.34, triggerRect.width / Math.min(window.innerWidth * 0.9, 672)));

        aboutPanel.style.setProperty('--about-start-x', `${triggerCenterX - viewportCenterX}px`);
        aboutPanel.style.setProperty('--about-start-y', `${triggerCenterY - viewportCenterY}px`);
        aboutPanel.style.setProperty('--about-start-scale', String(scale));
    }

    function openAboutPopover(trigger) {
        if (!aboutPopover || !aboutPanel || !aboutTitle || !aboutSummary || !aboutDetail) return;

        window.clearTimeout(aboutCloseTimer);
        aboutPreviouslyFocusedElement = document.activeElement;
        activeAboutTrigger = trigger;

        aboutPanel.classList.remove('is-expanded');
        aboutPopover.classList.remove('is-closing');
        setAboutStartFromTrigger(trigger);
        aboutTitle.textContent = trigger.dataset.aboutTitle || '';
        aboutSummary.textContent = trigger.dataset.aboutSummary || '';
        aboutDetail.textContent = trigger.dataset.aboutDetail || '';
        aboutPopover.classList.add('is-open');
        aboutPopover.setAttribute('aria-hidden', 'false');
        document.body.classList.add('overflow-hidden');

        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                aboutPanel.classList.add('is-expanded');
            });
        });

        window.setTimeout(() => {
            aboutClose?.focus({ preventScroll: true });
        }, 260);
    }

    function closeAboutPopover() {
        if (!aboutPopover || !aboutPanel) return;

        setAboutStartFromTrigger(activeAboutTrigger);
        aboutPopover.classList.add('is-closing');
        aboutPanel.classList.remove('is-expanded');
        aboutClose?.blur();

        aboutCloseTimer = window.setTimeout(() => {
            aboutPopover.classList.remove('is-open', 'is-closing');
            aboutPopover.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('overflow-hidden');
            activeAboutTrigger = null;

            if (aboutPreviouslyFocusedElement instanceof HTMLElement) {
                aboutPreviouslyFocusedElement.focus({ preventScroll: true });
            }
        }, 560);
    }

    document.querySelectorAll('.about-card__button').forEach(trigger => {
        trigger.addEventListener('click', () => {
            openAboutPopover(trigger);
        });
    });

    aboutClose?.addEventListener('click', closeAboutPopover);
    aboutBackdrop?.addEventListener('click', closeAboutPopover);
    aboutPopover?.querySelector('a[href="#booking"]')?.addEventListener('click', closeAboutPopover);

    if (mobileMenuButton && mobileMenu && menuIconOpen && menuIconClose) {
        const closeMobileMenu = () => {
            mobileMenu.classList.add('hidden');
            menuIconOpen.classList.remove('hidden');
            menuIconClose.classList.add('hidden');
            mobileMenuButton.setAttribute('aria-expanded', 'false');
            mobileMenuButton.setAttribute('aria-label', 'Otevřít menu');
        };

        mobileMenuButton.addEventListener('click', () => {
            const isOpen = !mobileMenu.classList.contains('hidden');
            mobileMenu.classList.toggle('hidden', isOpen);
            menuIconOpen.classList.toggle('hidden', !isOpen);
            menuIconClose.classList.toggle('hidden', isOpen);
            mobileMenuButton.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
            mobileMenuButton.setAttribute('aria-label', isOpen ? 'Otevřít menu' : 'Zavřít menu');
        });

        mobileMenu.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', closeMobileMenu);
        });
    }

    function focusBookingForm() {
        if (!bookingForm) return;

        bookingForm.classList.remove('booking-attention');
        void bookingForm.offsetWidth;
        bookingForm.classList.add('booking-attention');

        window.setTimeout(() => {
            nameInput?.focus({ preventScroll: true });
        }, 260);

        window.setTimeout(() => {
            bookingForm.classList.remove('booking-attention');
        }, 900);
    }

    function preselectBookingService(serviceName) {
        if (!serviceName || !serviceSelect) return;

        const hasMatchingOption = Array.from(serviceSelect.options).some(option => option.value === serviceName);
        if (!hasMatchingOption) return;

        serviceSelect.value = serviceName;
        serviceSelect.dispatchEvent(new Event('change', { bubbles: true }));
    }

    function animateBookingArrivalFromHash() {
        if (!bookingSection || window.location.hash !== '#booking') return;

        window.setTimeout(() => {
            smoothScrollToSection(getBookingScrollTarget(), false);
            window.setTimeout(focusBookingForm, prefersReducedMotion.matches ? 120 : 520);
        }, 120);
    }

    document.querySelectorAll('a[href="#booking"]').forEach(link => {
        link.addEventListener('click', event => {
            if (!bookingSection) return;

            event.preventDefault();
            preselectBookingService(link.dataset.bookService || '');
            if (mobileMenu && !mobileMenu.classList.contains('hidden')) {
                mobileMenu.classList.add('hidden');
                menuIconOpen?.classList.remove('hidden');
                menuIconClose?.classList.add('hidden');
                mobileMenuButton?.setAttribute('aria-expanded', 'false');
                mobileMenuButton?.setAttribute('aria-label', 'Otevřít menu');
            }

            smoothScrollToSection(getBookingScrollTarget(), true, 'booking');
            window.setTimeout(focusBookingForm, 520);
        });
    });

    document.querySelectorAll('a[href^="#"]').forEach(link => {
        const href = link.getAttribute('href') || '';
        if (href === '#booking' || href === '#') return;

        link.addEventListener('click', event => {
            const target = document.querySelector(href);
            if (!(target instanceof HTMLElement)) return;

            event.preventDefault();

            if (mobileMenu && !mobileMenu.classList.contains('hidden')) {
                mobileMenu.classList.add('hidden');
                menuIconOpen?.classList.remove('hidden');
                menuIconClose?.classList.add('hidden');
                mobileMenuButton?.setAttribute('aria-expanded', 'false');
                mobileMenuButton?.setAttribute('aria-label', 'Otevřít menu');
            }

            smoothScrollToSection(target);
        });
    });

    animateBookingArrivalFromHash();
    window.addEventListener('hashchange', animateBookingArrivalFromHash);

    function updateScrollTopButton() {
        if (!scrollTopButton) return;
        scrollTopButton.classList.toggle('is-visible', window.scrollY > 520);

        if (pageFooter) {
            const footerRect = pageFooter.getBoundingClientRect();
            const footerInView = footerRect.top < window.innerHeight - 32;
            scrollTopButton.classList.toggle('is-footer-contrast', footerInView);
        }
    }

    scrollTopButton?.addEventListener('click', () => {
        window.scrollTo({
            top: 0,
            behavior: prefersReducedMotion.matches ? 'auto' : 'smooth',
        });
    });

    window.addEventListener('scroll', updateScrollTopButton, { passive: true });
    window.addEventListener('scroll', updateSiteHeader, { passive: true });
    window.addEventListener('scroll', updateHeroParallax, { passive: true });
    updateScrollTopButton();
    updateSiteHeader();
    updateHeroParallax();

    function setGalleryStartFromTrigger(trigger) {
        if (!galleryPanel || !trigger) return;

        const triggerRect = trigger.getBoundingClientRect();
        const viewportCenterX = window.innerWidth / 2;
        const viewportCenterY = window.innerHeight / 2;
        const triggerCenterX = triggerRect.left + triggerRect.width / 2;
        const triggerCenterY = triggerRect.top + triggerRect.height / 2;
        const scale = Math.min(0.72, Math.max(0.28, triggerRect.width / Math.min(window.innerWidth * 0.9, 896)));

        galleryPanel.style.setProperty('--gallery-start-x', `${triggerCenterX - viewportCenterX}px`);
        galleryPanel.style.setProperty('--gallery-start-y', `${triggerCenterY - viewportCenterY}px`);
        galleryPanel.style.setProperty('--gallery-start-scale', String(scale));
    }

    function openGalleryLightbox(trigger) {
        if (!galleryLightbox || !galleryPanel || !galleryImage || !galleryTitle || !galleryDescription) return;

        window.clearTimeout(galleryCloseTimer);
        previouslyFocusedElement = document.activeElement;
        activeGalleryTrigger = trigger;

        galleryPanel.classList.remove('is-expanded');
        galleryLightbox.classList.remove('is-closing');
        setGalleryStartFromTrigger(trigger);
        galleryImage.src = trigger.dataset.galleryImage || trigger.href;
        galleryImage.alt = trigger.dataset.galleryTitle || '';
        galleryTitle.textContent = trigger.dataset.galleryTitle || '';
        galleryDescription.textContent = trigger.dataset.galleryDescription || '';
        galleryLightbox.classList.add('is-open');
        galleryLightbox.setAttribute('aria-hidden', 'false');
        document.body.classList.add('overflow-hidden');

        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                galleryPanel.classList.add('is-expanded');
            });
        });

        window.setTimeout(() => {
            galleryClose?.focus({ preventScroll: true });
        }, 260);
    }

    function closeGalleryLightbox() {
        if (!galleryLightbox || !galleryPanel || !galleryImage) return;

        setGalleryStartFromTrigger(activeGalleryTrigger);
        galleryLightbox.classList.add('is-closing');
        galleryPanel.classList.remove('is-expanded');
        galleryClose?.blur();

        galleryCloseTimer = window.setTimeout(() => {
            galleryLightbox.classList.remove('is-open', 'is-closing');
            galleryLightbox.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('overflow-hidden');
            galleryImage.src = '';
            activeGalleryTrigger = null;

            if (previouslyFocusedElement instanceof HTMLElement) {
                previouslyFocusedElement.focus({ preventScroll: true });
            }
        }, 680);
    }

    document.querySelectorAll('[data-gallery-image]').forEach(trigger => {
        trigger.addEventListener('click', event => {
            event.preventDefault();
            openGalleryLightbox(trigger);
        });
    });

    galleryClose?.addEventListener('click', closeGalleryLightbox);
    galleryBackdrop?.addEventListener('click', closeGalleryLightbox);

    document.addEventListener('keydown', event => {
        if (event.key === 'Escape' && aboutPopover?.classList.contains('is-open')) {
            closeAboutPopover();
        }

        if (event.key === 'Escape' && galleryLightbox?.classList.contains('is-open')) {
            closeGalleryLightbox();
        }
    });

    // Section reveal is now handled by assets/modern.js

    if (!dateInput || !timeSelect || !serviceSelect) return;

    async function loadAvailableTimes() {
        const date = dateInput.value;
        const previouslySelectedTime = timeSelect.value;

        // nic nevybráno → disable select
        if (!date) {
            timeSelect.innerHTML = "";
            const opt = document.createElement('option');
            opt.value = "";
            opt.textContent = "Nejprve vyber datum";
            opt.disabled = true;
            opt.selected = true;
            timeSelect.appendChild(opt);
            timeSelect.disabled = true;
            return;
        }

        try {
            const params = new URLSearchParams({ date });
            if (serviceSelect.value) {
                params.set('service', serviceSelect.value);
            }

            const res = await fetch('load_times.php?' + params.toString());
            const data = await res.json();

            // Server vrací už jen skutečně volné časy.
            const freeTimes = data.available || [];

            timeSelect.innerHTML = "";

            if (data.calendar_error) {
                const opt = document.createElement('option');
                opt.value = "";
                opt.textContent = "Kalendář teď nejde ověřit";
                opt.disabled = true;
                opt.selected = true;
                timeSelect.appendChild(opt);
                timeSelect.disabled = true;
                return;
            }

            if (data.closed) {
                const opt = document.createElement('option');
                opt.value = "";
                opt.textContent = "V tento den je zavřeno";
                opt.disabled = true;
                opt.selected = true;
                timeSelect.appendChild(opt);
                timeSelect.disabled = true;
                return;
            }

            if (freeTimes.length === 0) {
                const opt = document.createElement('option');
                opt.value = "";
                opt.textContent = "Žádné volné časy";
                opt.disabled = true;
                opt.selected = true;
                timeSelect.appendChild(opt);
                timeSelect.disabled = true;
                return;
            }

            timeSelect.disabled = false;

            const placeholder = document.createElement('option');
            placeholder.value = "";
            placeholder.textContent = "Vyber čas";
            placeholder.disabled = true;
            placeholder.selected = !freeTimes.includes(previouslySelectedTime);
            timeSelect.appendChild(placeholder);

            freeTimes.forEach(time => {
                const opt = document.createElement('option');
                opt.value = time;
                opt.textContent = time;
                opt.selected = time === previouslySelectedTime;
                timeSelect.appendChild(opt);
            });
        } catch (err) {
            console.error('Chyba při načítání časů:', err);
            timeSelect.innerHTML = "";
            const opt = document.createElement('option');
            opt.value = "";
            opt.textContent = "Časy se teď nepodařilo načíst";
            opt.disabled = true;
            opt.selected = true;
            timeSelect.appendChild(opt);
            timeSelect.disabled = true;
            errorMsg.textContent = 'Volné časy se teď nepodařilo načíst. Zkus to prosím znovu za chvilku.';
            errorMsg.classList.remove('hidden');
        }
    }

    dateInput.addEventListener('change', loadAvailableTimes);
    serviceSelect.addEventListener('change', function () {
        if (dateInput.value) {
            loadAvailableTimes();
        }
    });

    // inicializace – dokud není datum, zamkneme výběr času
    timeSelect.innerHTML = "";
    const opt = document.createElement('option');
    opt.value = "";
    opt.textContent = "Nejprve vyber datum";
    opt.disabled = true;
    opt.selected = true;
    timeSelect.appendChild(opt);
    timeSelect.disabled = true;
});
</script>



</body>
</html>
