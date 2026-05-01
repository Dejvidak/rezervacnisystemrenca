<?php
require_once __DIR__ . '/config.php';

$instagramUrl = app_business_instagram_url();
$instagramHandle = app_business_instagram_handle();
$pageTitle = 'Reference střihů - ' . app_business_name();
$pageDescription = 'Vybrané reference střihů Hair By ReneNeme v Brně. Projdi si galerii, inspiruj se tvarem i délkou a pak přejdi rovnou k rezervaci termínu.';
$pageCanonical = app_absolute_url('references.php');
$pageImage = app_absolute_url('assets/references/moderni-pansky-strih.jpg');
$pageSchema = app_public_business_schema('references.php', [
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
        'image' => 'assets/references/kratky-fade.jpg',
    ],
    [
        'title' => 'Upravený střih',
        'description' => 'Vyčištěné boky, uhlazený profil a střih připravený na běžné nošení',
        'image' => 'assets/references/upraveny-strih.jpg',
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
<html lang="cs">
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
            --page: #F4EFE7;
            --ink: #2B211C;
            --surface: #2B211C;
            --surface-soft: #4A3A30;
            --muted: #5E4E41;
            --muted-strong: #725E4C;
            --line: #D8C8B0;
            --cream: #F5EDE1;
            --cream-soft: #EDE8DD;
            --accent: #C08A3E;
            --accent-dark: #94642C;
            --gold: #D6A85E;
            --gold-soft: #F1C879;
        }

        .premium-surface {
            border: 1px solid var(--line);
            border-radius: 1.25rem;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.84), rgba(255, 255, 255, 0.72));
            box-shadow: 0 20px 40px rgba(43, 33, 28, 0.1);
        }

        .site-header {
            transition: background-color 220ms ease, border-color 220ms ease, box-shadow 220ms ease, backdrop-filter 220ms ease;
        }

        .site-header.is-scrolled {
            border-color: rgba(74, 58, 48, 0.54);
            background: rgba(43, 33, 28, 0.88);
            backdrop-filter: blur(16px);
            box-shadow: 0 14px 32px rgba(43, 33, 28, 0.18);
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

        .reference-pill {
            display: inline-flex;
            align-items: center;
            border: 1px solid rgba(192, 138, 62, 0.24);
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.62);
            box-shadow: 0 10px 24px rgba(43, 33, 28, 0.06);
            transition: transform 220ms ease, border-color 220ms ease, background-color 220ms ease, box-shadow 220ms ease, color 220ms ease;
        }

        .reference-pill:hover {
            transform: translate3d(0, -2px, 0);
            border-color: rgba(192, 138, 62, 0.58);
            background: rgba(255, 255, 255, 0.9);
            box-shadow: 0 16px 32px rgba(43, 33, 28, 0.11);
            color: var(--accent-dark);
        }

        .reference-pill--dark {
            border-color: rgba(241, 200, 121, 0.18);
            background: rgba(255, 255, 255, 0.05);
            box-shadow: none;
        }

        .reference-pill--dark:hover {
            border-color: rgba(241, 200, 121, 0.44);
            background: rgba(241, 200, 121, 0.12);
            color: var(--gold-soft);
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
            transform: translate3d(0, 18px, 0) scale(0.98);
            transition: opacity 220ms ease, transform 320ms ease;
        }

        .gallery-lightbox.is-open .gallery-lightbox__panel.is-expanded {
            opacity: 1;
            transform: translate3d(0, 0, 0) scale(1);
        }

        .section-reveal {
            opacity: 0;
            transform: translate3d(0, 42px, 0);
            transition: opacity 780ms ease, transform 1160ms cubic-bezier(0.16, 1, 0.3, 1);
            will-change: opacity, transform;
        }

        .section-reveal--left {
            transform: translate3d(-56px, 28px, 0);
        }

        .section-reveal--right {
            transform: translate3d(56px, 28px, 0);
        }

        .section-reveal.is-visible {
            opacity: 1;
            transform: translate3d(0, 0, 0);
        }

        .reveal-item {
            opacity: 0;
            transform: translate3d(0, 24px, 0);
            transition: opacity 560ms ease, transform 760ms cubic-bezier(0.16, 1, 0.3, 1);
            transition-delay: var(--reveal-delay, 0ms);
            will-change: opacity, transform;
        }

        .reveal-item.is-visible {
            opacity: 1;
            transform: translate3d(0, 0, 0);
        }

        @media (max-width: 767px) {
            .section-reveal,
            .section-reveal--left,
            .section-reveal--right {
                transform: translate3d(0, 30px, 0);
                transition-duration: 360ms, 560ms;
            }

            .reveal-item {
                transition-duration: 300ms, 460ms;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .section-reveal {
                opacity: 1;
                transform: none;
                transition: none;
            }

            .reveal-item {
                opacity: 1;
                transform: none;
                transition: none;
            }
        }
    </style>
</head>
<body class="min-h-screen overflow-x-hidden bg-[var(--page)] text-[color:var(--ink)] antialiased">

<header class="site-header sticky top-0 z-50 bg-[var(--surface)] border-b border-[var(--surface-soft)] shadow-lg">
    <div class="max-w-6xl mx-auto flex items-center justify-between px-4 py-3">
        <a href="index.php" class="whitespace-nowrap text-xl font-extrabold tracking-tight transition hover:opacity-90 sm:text-2xl md:text-[1.65rem]" aria-label="Hair By ReneNeme">
            <span class="text-[color:var(--cream)]">Hair By</span>
            <span class="text-[color:var(--gold)]">ReneNeme</span>
        </a>
        <nav class="hidden items-center gap-2 text-xs text-[color:var(--cream-soft)] lg:flex lg:gap-5 lg:text-sm">
            <a href="index.php#about" class="whitespace-nowrap transition hover:text-[color:var(--gold)]">O nás</a>
            <a href="index.php#visit" class="whitespace-nowrap transition hover:text-[color:var(--gold)]">Návštěva</a>
            <a href="index.php#services" class="whitespace-nowrap transition hover:text-[color:var(--gold)]">Služby</a>
            <a href="references.php" class="whitespace-nowrap font-semibold text-[color:var(--gold)]">Reference</a>
            <a href="cenik.php" class="whitespace-nowrap transition hover:text-[color:var(--gold)]">Ceník</a>
            <a href="contact.php" class="whitespace-nowrap transition hover:text-[color:var(--gold)]">Kontakt</a>
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
            <a href="index.php#booking" class="inline-flex whitespace-nowrap rounded-lg bg-[var(--accent)] px-4 py-2 font-semibold text-[color:var(--cream)] shadow-sm transition hover:-translate-y-0.5 hover:bg-[var(--accent-dark)] hover:shadow-md focus:outline-none focus:ring-2 focus:ring-[var(--gold)]">Rezervace</a>
        </nav>
        <button
            type="button"
            id="mobileMenuButton"
            class="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-[var(--muted-strong)] text-[color:var(--cream)] transition hover:bg-[var(--surface-soft)] focus:outline-none focus:ring-2 focus:ring-[var(--gold)] lg:hidden"
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
    <nav id="mobileMenu" class="hidden max-h-[calc(100vh-4.25rem)] overflow-y-auto border-t border-[var(--surface-soft)] bg-[var(--surface)] px-4 pb-4 pt-2 text-sm text-[color:var(--cream-soft)] shadow-lg lg:hidden">
        <a href="index.php#about" class="block rounded-lg px-3 py-3 hover:bg-[var(--surface-soft)] hover:text-[color:var(--gold)]">O nás</a>
        <a href="index.php#visit" class="block rounded-lg px-3 py-3 hover:bg-[var(--surface-soft)] hover:text-[color:var(--gold)]">Návštěva</a>
        <a href="index.php#services" class="block rounded-lg px-3 py-3 hover:bg-[var(--surface-soft)] hover:text-[color:var(--gold)]">Služby</a>
        <a href="references.php" class="block rounded-lg px-3 py-3 font-semibold text-[color:var(--gold)] hover:bg-[var(--surface-soft)]">Reference</a>
        <a href="cenik.php" class="block rounded-lg px-3 py-3 hover:bg-[var(--surface-soft)] hover:text-[color:var(--gold)]">Ceník</a>
        <a href="contact.php" class="block rounded-lg px-3 py-3 hover:bg-[var(--surface-soft)] hover:text-[color:var(--gold)]">Kontakt</a>
        <a href="index.php#booking" class="mt-2 inline-flex w-full items-center justify-center rounded-lg bg-[var(--accent)] px-4 py-3 font-semibold text-[color:var(--cream)] shadow-sm transition hover:bg-[var(--accent-dark)] focus:outline-none focus:ring-2 focus:ring-[var(--gold)]">Rezervovat termín</a>
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

<main class="max-w-6xl mx-auto px-4 py-8 sm:px-6 md:py-16">
    <section class="grid gap-7 md:grid-cols-[0.95fr_1.05fr] md:items-center" data-reveal-section-static>
        <div class="max-w-2xl">
            <p class="text-[11px] font-bold uppercase tracking-[0.22em] text-[color:var(--muted-strong)] sm:text-xs sm:tracking-[0.28em]">Reference</p>
            <h1 class="mt-3 text-3xl font-extrabold leading-[1.12] sm:text-4xl sm:leading-[1.08] md:text-[3.35rem] md:leading-[1.05]">
                <span class="block">Střihy a výsledky</span>
                <span class="mt-1 block text-[color:var(--accent)]">na jednom místě</span>
            </h1>
            <p class="mt-4 max-w-xl text-sm leading-7 text-[color:var(--muted)] md:text-base">
                Tady najdeš galerii pohromadě na jednom místě, takže se v ní pohodlněji listuje i na mobilu. Klikni na fotku a otevře se ve větším náhledu.
            </p>
            <div class="mt-5 flex flex-wrap gap-3">
                <div class="reference-pill px-4 py-2 text-sm font-semibold text-[color:var(--ink)]">
                    Různé typy střihů na jednom místě
                </div>
                <div class="reference-pill px-4 py-2 text-sm font-semibold text-[color:var(--ink)]">
                    Tvar, profil i detail ve větším náhledu
                </div>
            </div>
            <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                <a href="index.php#booking" class="inline-flex items-center justify-center rounded-lg bg-[var(--accent)] px-5 py-3 text-sm font-semibold text-[color:var(--cream)] transition hover:bg-[var(--accent-dark)] focus:outline-none focus:ring-2 focus:ring-[var(--gold)]">
                    Přejít na rezervaci
                </a>
                <a href="<?= htmlspecialchars($instagramUrl, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener" class="inline-flex items-center justify-center text-sm font-semibold accent-link">
                    <?= htmlspecialchars($instagramHandle, ENT_QUOTES, 'UTF-8') ?>
                </a>
            </div>
        </div>
        <div class="reveal-item rounded-2xl border border-[rgba(214,168,94,0.18)] bg-[linear-gradient(145deg,rgba(43,33,28,0.98),rgba(74,58,48,0.94))] p-5 text-[color:var(--cream)] shadow-[0_20px_40px_rgba(43,33,28,0.16)] sm:p-6" data-reveal-item>
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-[0.22em] text-[color:var(--gold)]">Jak vybírat</p>
                    <p class="mt-2 text-xl font-bold sm:text-[1.75rem]">Mrkni na tvar, délku i celkový dojem</p>
                </div>
                <a href="#gallery" class="hidden h-11 w-11 shrink-0 items-center justify-center rounded-2xl border border-[rgba(241,200,121,0.18)] bg-[rgba(255,255,255,0.05)] text-[color:var(--gold-soft)] transition hover:-translate-y-0.5 hover:border-[rgba(241,200,121,0.44)] hover:bg-[rgba(241,200,121,0.12)] hover:text-[color:var(--cream)] focus:outline-none focus:ring-2 focus:ring-[var(--gold)] sm:inline-flex" aria-label="Přejít na galerii fotek">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M5 12h14M12 5l7 7-7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </a>
            </div>
            <p class="mt-3 max-w-xl text-sm leading-7 text-[color:var(--cream-soft)]">
                Fotky slouží jako inspirace. Konečný střih se vždy doladí podle vlasů, směru růstu i toho, jak ho chceš nosit běžně.
            </p>
            <div class="mt-5 flex flex-wrap gap-3">
                <div class="reference-pill reference-pill--dark px-4 py-2 text-sm font-semibold text-[color:var(--cream)]">
                    Silueta a profil
                </div>
                <div class="reference-pill reference-pill--dark px-4 py-2 text-sm font-semibold text-[color:var(--cream)]">
                    Délka i přechody
                </div>
            </div>
        </div>
    </section>

    <section id="gallery" class="mt-10 scroll-mt-28 border-t border-[var(--line)] pt-8" data-reveal-section-static>
        <div class="mb-6 flex flex-col items-start gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.24em] text-[color:var(--muted-strong)]">Galerie</p>
                <h2 class="mt-1 text-2xl font-bold">Vybrané reference střihů</h2>
            </div>
            <a href="index.php#booking" class="text-sm font-semibold accent-link">Přejít k rezervaci</a>
        </div>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <?php foreach ($referenceCuts as $cut): ?>
                <?php
                $hasImage = is_file(__DIR__ . '/' . $cut['image']);
                $webpImage = preg_replace('/\.jpe?g$/i', '.webp', $cut['image']);
                $hasWebpImage = is_string($webpImage) && is_file(__DIR__ . '/' . $webpImage);
                $galleryImage = $hasWebpImage ? $webpImage : $cut['image'];
                ?>
                <?php if ($hasImage): ?>
                    <a
                        href="<?= htmlspecialchars($cut['image'], ENT_QUOTES, 'UTF-8') ?>"
                        data-gallery-image="<?= htmlspecialchars($galleryImage, ENT_QUOTES, 'UTF-8') ?>"
                        data-gallery-title="<?= htmlspecialchars($cut['title'], ENT_QUOTES, 'UTF-8') ?>"
                        data-gallery-description="<?= htmlspecialchars($cut['description'], ENT_QUOTES, 'UTF-8') ?>"
                        class="group premium-surface reveal-item flex h-full flex-col overflow-hidden"
                        data-reveal-item
                    >
                        <picture>
                            <?php if ($hasWebpImage): ?>
                                <source srcset="<?= htmlspecialchars($webpImage, ENT_QUOTES, 'UTF-8') ?>" type="image/webp">
                            <?php endif; ?>
                            <img
                                src="<?= htmlspecialchars($cut['image'], ENT_QUOTES, 'UTF-8') ?>"
                                alt="<?= htmlspecialchars($cut['title'], ENT_QUOTES, 'UTF-8') ?>"
                                width="1012"
                                height="1800"
                                class="h-56 w-full object-cover transition duration-300 group-hover:scale-[1.03] sm:h-72 lg:aspect-[4/5] lg:h-auto"
                                loading="lazy"
                                decoding="async"
                            >
                        </picture>
                        <div class="flex flex-1 flex-col p-4">
                            <p class="font-semibold"><?= htmlspecialchars($cut['title'], ENT_QUOTES, 'UTF-8') ?></p>
                            <p class="mt-1 text-sm text-[color:var(--muted)]"><?= htmlspecialchars($cut['description'], ENT_QUOTES, 'UTF-8') ?></p>
                            <p class="mt-auto pt-3 text-xs font-semibold uppercase tracking-[0.18em] text-[color:var(--accent)]">Otevřít foto</p>
                        </div>
                    </a>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </section>
</main>

<div
    id="galleryLightbox"
    class="gallery-lightbox fixed inset-0 z-[100] flex items-center justify-center bg-black/60 p-4 backdrop-blur-md"
    aria-hidden="true"
>
    <button type="button" id="galleryBackdrop" class="absolute inset-0 cursor-default" aria-label="Zavřít fotografii"></button>
    <div class="gallery-lightbox__panel relative z-10 max-h-[calc(100vh-2rem)] w-full max-w-4xl overflow-y-auto rounded-[1.75rem]">
        <button
            type="button"
            id="galleryClose"
            class="absolute right-3 top-3 z-20 inline-flex h-10 w-10 items-center justify-center rounded-full bg-[rgba(43,33,28,0.78)] text-[color:var(--cream)] shadow-lg transition hover:bg-[var(--ink)] focus:outline-none focus:ring-2 focus:ring-[var(--gold)]"
            aria-label="Zavřít fotografii"
        >
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
            </svg>
        </button>
        <figure class="overflow-hidden rounded-[1.75rem] bg-[var(--ink)] shadow-2xl">
            <img id="galleryImage" src="" alt="" class="max-h-[68vh] w-full object-contain bg-[var(--ink)] sm:max-h-[78vh]">
            <figcaption class="border-t border-[var(--surface-soft)] px-4 py-3 text-[color:var(--cream)] sm:px-5">
                <p id="galleryTitle" class="font-semibold"></p>
                <p id="galleryDescription" class="mt-1 text-sm text-[color:var(--cream-soft)]"></p>
            </figcaption>
        </figure>
    </div>
</div>

<footer class="border-t border-[var(--surface-soft)] bg-[var(--surface)] text-[color:var(--cream-soft)]">
    <div class="mx-auto grid max-w-6xl gap-6 px-4 py-8 sm:px-6 md:grid-cols-[1.2fr_0.8fr_0.8fr]">
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
                <a href="contact.php" class="block hover:text-[color:var(--gold)]">Kontaktní stránka</a>
            </div>
        </div>
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.22em] text-[color:var(--gold)]">Rychle</p>
            <div class="mt-3 space-y-2 text-sm">
                <a href="index.php#booking" class="block hover:text-[color:var(--gold)]">Rezervace</a>
                <a href="cenik.php" class="block hover:text-[color:var(--gold)]">Ceník</a>
                <a href="references.php" class="block hover:text-[color:var(--gold)]">Reference</a>
                <a href="<?= htmlspecialchars($instagramUrl, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener" class="block hover:text-[color:var(--gold)]">Instagram</a>
            </div>
        </div>
    </div>
    <div class="border-t border-[var(--surface-soft)] px-4 py-4 text-center text-xs text-[color:var(--cream-soft)]">
        <p>© <?php echo date('Y'); ?> Hair By ReneNeme · Web vytvořil Dejvidaak</p>
    </div>
</footer>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const siteHeader = document.querySelector('.site-header');
    const mobileMenuButton = document.getElementById('mobileMenuButton');
    const mobileMenu = document.getElementById('mobileMenu');
    const menuIconOpen = document.getElementById('menuIconOpen');
    const menuIconClose = document.getElementById('menuIconClose');
    const galleryLightbox = document.getElementById('galleryLightbox');
    const galleryImage = document.getElementById('galleryImage');
    const galleryTitle = document.getElementById('galleryTitle');
    const galleryDescription = document.getElementById('galleryDescription');
    const galleryClose = document.getElementById('galleryClose');
    const galleryBackdrop = document.getElementById('galleryBackdrop');
    const galleryPanel = document.querySelector('.gallery-lightbox__panel');
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    let galleryCloseTimer = null;

    function updateSiteHeader() {
        siteHeader?.classList.toggle('is-scrolled', window.scrollY > 16);
    }

    window.addEventListener('scroll', updateSiteHeader, { passive: true });
    updateSiteHeader();

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

    function openGalleryLightbox(trigger) {
        if (!galleryLightbox || !galleryPanel || !galleryImage || !galleryTitle || !galleryDescription) return;

        window.clearTimeout(galleryCloseTimer);
        galleryPanel.classList.remove('is-expanded');
        galleryLightbox.classList.remove('is-closing');
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
    }

    function closeGalleryLightbox() {
        if (!galleryLightbox || !galleryPanel || !galleryImage) return;

        galleryLightbox.classList.add('is-closing');
        galleryPanel.classList.remove('is-expanded');

        galleryCloseTimer = window.setTimeout(() => {
            galleryLightbox.classList.remove('is-open', 'is-closing');
            galleryLightbox.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('overflow-hidden');
            galleryImage.src = '';
        }, 320);
    }

    document.querySelectorAll('[data-gallery-image]').forEach(trigger => {
        trigger.addEventListener('click', event => {
            event.preventDefault();
            openGalleryLightbox(trigger);
        });
    });

    galleryClose?.addEventListener('click', closeGalleryLightbox);
    galleryBackdrop?.addEventListener('click', closeGalleryLightbox);

    document.querySelectorAll('a[href^="#"]').forEach(link => {
        link.addEventListener('click', event => {
            const href = link.getAttribute('href') || '';
            if (href === '#') return;

            const target = document.querySelector(href);
            if (!(target instanceof HTMLElement)) return;

            event.preventDefault();
            target.scrollIntoView({
                behavior: prefersReducedMotion.matches ? 'auto' : 'smooth',
                block: 'start',
            });

            window.history.pushState(null, '', href);
        });
    });

    document.addEventListener('keydown', event => {
        if (event.key === 'Escape' && galleryLightbox?.classList.contains('is-open')) {
            closeGalleryLightbox();
        }
    });

    const revealSections = Array.from(document.querySelectorAll('main > section:not([data-reveal-section-static])'));
    const mobileViewport = window.matchMedia('(max-width: 767px)');
    if (revealSections.length > 0) {
        if (prefersReducedMotion.matches) {
            revealSections.forEach(section => section.classList.add('is-visible'));
        } else {
            const revealObserver = new IntersectionObserver(entries => {
                entries.forEach(entry => {
                    entry.target.classList.toggle('is-visible', entry.isIntersecting);
                });
            }, {
                rootMargin: mobileViewport.matches ? '0px 0px -10% 0px' : '-8% 0px -8% 0px',
                threshold: mobileViewport.matches ? 0.05 : 0.18,
            });

            revealSections.forEach((section, index) => {
                section.classList.add('section-reveal');
                section.classList.add(index % 2 === 0 ? 'section-reveal--left' : 'section-reveal--right');
                revealObserver.observe(section);
            });
        }
    }

    const revealItems = Array.from(document.querySelectorAll('[data-reveal-item]'));
    if (revealItems.length > 0) {
        if (prefersReducedMotion.matches) {
            revealItems.forEach(item => item.classList.add('is-visible'));
        } else {
            const revealItemObserver = new IntersectionObserver(entries => {
                entries.forEach(entry => {
                    entry.target.classList.toggle('is-visible', entry.isIntersecting);
                });
            }, {
                rootMargin: mobileViewport.matches ? '0px 0px -12% 0px' : '-8% 0px -10% 0px',
                threshold: 0.12,
            });

            revealItems.forEach((item, index) => {
                const delayStep = mobileViewport.matches ? 45 : 90;
                const delayMax = mobileViewport.matches ? 135 : 360;
                item.style.setProperty('--reveal-delay', `${Math.min(index * delayStep, delayMax)}ms`);
                revealItemObserver.observe(item);
            });
        }
    }
});
</script>
</body>
</html>
