<?php
require_once __DIR__ . '/config.php';

$services = app_services();
$servicePrices = array_map(static fn(array $service): int => (int) $service['price'], $services);
$serviceDurations = array_map(static fn(array $service): int => (int) $service['duration'], $services);
$instagramUrl = 'https://www.instagram.com/hairbyreneneme/';
$instagramHandle = '@hairbyreneneme';
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Ceník - Hair By ReneNeme</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
            --line-soft: #E8DED0;
            --field: #F9F5EF;
            --cream: #F5EDE1;
            --cream-soft: #EDE8DD;
            --accent: #C08A3E;
            --accent-dark: #94642C;
            --gold: #D6A85E;
            --gold-soft: #F1C879;
        }

        .price-stat {
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(192, 138, 62, 0.34);
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.92), rgba(249, 245, 239, 0.86));
            box-shadow: 0 16px 30px rgba(43, 33, 28, 0.1);
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
            border: 1px solid rgba(192, 138, 62, 0.42);
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.96), rgba(249, 245, 239, 0.9));
            box-shadow: 0 14px 26px rgba(43, 33, 28, 0.12);
        }

        .price-badge::before {
            position: absolute;
            inset: 0 0 auto;
            height: 4px;
            background: linear-gradient(90deg, var(--gold-soft), var(--accent));
            content: "";
        }

        .price-badge--featured {
            border-color: rgba(241, 200, 121, 0.58);
            background: linear-gradient(180deg, rgba(241, 200, 121, 0.18), rgba(255, 255, 255, 0.08));
            box-shadow: 0 16px 28px rgba(0, 0, 0, 0.18);
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

<header class="sticky top-0 z-50 border-b border-[var(--surface-soft)] bg-[var(--surface)] shadow-lg">
    <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-3">
        <a href="index.php" class="whitespace-nowrap text-xl font-extrabold tracking-tight transition hover:opacity-90 sm:text-2xl md:text-[1.65rem]" aria-label="Hair By ReneNeme">
            <span class="text-[color:var(--cream)]">Hair By</span>
            <span class="text-[color:var(--gold)]">ReneNeme</span>
        </a>
        <nav class="hidden items-center gap-2 text-xs text-[color:var(--cream-soft)] lg:flex lg:gap-5 lg:text-sm">
            <a href="index.php#about" class="whitespace-nowrap transition hover:text-[color:var(--gold)]">O nás</a>
            <a href="index.php#visit" class="whitespace-nowrap transition hover:text-[color:var(--gold)]">Návštěva</a>
            <a href="index.php#services" class="whitespace-nowrap transition hover:text-[color:var(--gold)]">Služby</a>
            <a href="references.php" class="whitespace-nowrap transition hover:text-[color:var(--gold)]">Reference</a>
            <a href="cenik.php" class="whitespace-nowrap font-semibold text-[color:var(--gold)]">Ceník</a>
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
        <a href="references.php" class="block rounded-lg px-3 py-3 hover:bg-[var(--surface-soft)] hover:text-[color:var(--gold)]">Reference</a>
        <a href="cenik.php" class="block rounded-lg px-3 py-3 font-semibold text-[color:var(--gold)] hover:bg-[var(--surface-soft)]">Ceník</a>
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

<main class="mx-auto max-w-6xl px-4 py-8 sm:px-6 md:py-16">
    <section class="grid gap-7 md:grid-cols-[0.95fr_1.05fr] md:items-center" data-reveal-section-static>
        <div>
            <p class="mb-3 text-[11px] font-bold uppercase tracking-[0.22em] text-[color:var(--muted-strong)] sm:text-xs sm:tracking-[0.28em]">Ceník</p>
            <h1 class="text-3xl font-extrabold leading-tight sm:text-4xl md:text-5xl">
                Přehled služeb a cen
                <span class="text-[color:var(--accent)]">na jednom místě</span>
            </h1>
            <p class="mt-4 max-w-xl text-sm leading-7 text-[color:var(--muted)] md:text-base">
                V klidu si projdi všechny varianty, porovnej si cenu i délku služby a pak můžeš rovnou pokračovat k rezervaci bez dalšího hledání.
            </p>
            <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                <a href="index.php" class="inline-flex items-center justify-center rounded-lg border border-[var(--surface-soft)] px-5 py-3 text-sm font-semibold text-[color:var(--surface-soft)] transition hover:-translate-y-0.5 hover:bg-[var(--surface-soft)] hover:text-[color:var(--cream)] focus:outline-none focus:ring-2 focus:ring-[var(--surface-soft)]">
                    Na hlavní stránku
                </a>
                <a href="index.php#booking" class="inline-flex items-center justify-center text-sm font-semibold text-[color:var(--accent)] underline decoration-[var(--accent)] underline-offset-4 transition hover:text-[color:var(--accent-dark)] focus:outline-none focus:ring-2 focus:ring-[var(--gold)]">
                    Přejít na rezervaci
                </a>
            </div>
        </div>

        <div class="grid gap-3 min-[420px]:grid-cols-2 lg:grid-cols-3">
            <div class="price-stat reveal-item rounded-2xl px-5 py-4 pl-6" data-reveal-item>
                <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-[color:var(--muted-strong)]">Od</p>
                <p class="mt-2 text-3xl font-extrabold text-[color:var(--ink)]"><?= htmlspecialchars((string) min($servicePrices), ENT_QUOTES, 'UTF-8') ?> Kč</p>
                <p class="mt-1 text-sm text-[color:var(--muted)]">nejrychlejší varianta</p>
            </div>
            <div class="price-stat reveal-item rounded-2xl px-5 py-4 pl-6" data-reveal-item>
                <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-[color:var(--muted-strong)]">Délka</p>
                <p class="mt-2 text-3xl font-extrabold text-[color:var(--ink)]"><?= htmlspecialchars((string) min($serviceDurations), ENT_QUOTES, 'UTF-8') ?>-<?= htmlspecialchars((string) max($serviceDurations), ENT_QUOTES, 'UTF-8') ?> min</p>
                <p class="mt-1 text-sm text-[color:var(--muted)]">podle služby</p>
            </div>
            <div class="price-stat reveal-item rounded-2xl px-5 py-4 pl-6" data-reveal-item>
                <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-[color:var(--muted-strong)]">Služby</p>
                <p class="mt-2 text-3xl font-extrabold text-[color:var(--ink)]"><?= htmlspecialchars((string) count($services), ENT_QUOTES, 'UTF-8') ?></p>
                <p class="mt-1 text-sm text-[color:var(--muted)]">v aktuální nabídce</p>
            </div>
        </div>
    </section>

    <section class="mt-10 border-t border-[var(--line)] pt-8" data-reveal-section-static>
        <div class="mb-6 flex flex-col items-start gap-3 sm:flex-row sm:flex-wrap sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.24em] text-[color:var(--muted-strong)]">Kompletní ceník</p>
                <h2 class="mt-1 text-2xl font-bold">Vyber si variantu, která ti sedí</h2>
            </div>
            <a href="index.php#booking" class="text-sm font-semibold text-[color:var(--accent)] underline decoration-[var(--accent)] underline-offset-4 hover:text-[color:var(--accent-dark)]">
                Otevřít formulář
            </a>
        </div>
        <div class="grid gap-4 md:grid-cols-2">
            <?php foreach ($services as $serviceName => $service): ?>
                <?php $isFeatured = !empty($service['featured']); ?>
                <article class="group reveal-item relative overflow-hidden rounded-2xl border <?= $isFeatured ? 'border-[var(--accent)] bg-[linear-gradient(135deg,rgba(43,33,28,0.98),rgba(74,58,48,0.94))] text-[color:var(--cream)] shadow-xl' : 'border-[var(--line)] bg-white/78 text-[color:var(--ink)] shadow-sm' ?>" data-reveal-item>
                    <div class="absolute inset-x-0 top-0 h-1 <?= $isFeatured ? 'bg-[linear-gradient(90deg,var(--gold),var(--gold-soft))]' : 'bg-[linear-gradient(90deg,var(--accent),rgba(192,138,62,0.12))]' ?>"></div>
                    <div class="p-5 sm:p-6">
                        <div class="flex flex-col items-start gap-4">
                            <div class="w-full">
                                <span class="inline-flex rounded-full border px-3 py-1 text-[11px] font-bold uppercase tracking-[0.2em] <?= $isFeatured ? 'border-[rgba(241,200,121,0.32)] bg-[rgba(241,200,121,0.12)] text-[color:var(--gold-soft)]' : 'border-[var(--line)] bg-[var(--field)] text-[color:var(--muted-strong)]' ?>">
                                    <?= htmlspecialchars((string) ($service['badge'] ?? 'Služba'), ENT_QUOTES, 'UTF-8') ?>
                                </span>
                                <h3 class="mt-4 text-xl font-bold"><?= htmlspecialchars($serviceName, ENT_QUOTES, 'UTF-8') ?></h3>
                                <p class="mt-2 max-w-md text-sm leading-6 <?= $isFeatured ? 'text-[color:var(--cream-soft)]' : 'text-[color:var(--muted)]' ?>">
                                    <?= htmlspecialchars((string) ($service['description'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                                </p>
                                <p class="mt-3 max-w-md text-sm leading-6 <?= $isFeatured ? 'text-[color:var(--cream-soft)]' : 'text-[color:var(--muted)]' ?>">
                                    <?= htmlspecialchars((string) ($service['service_copy'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                                </p>
                            </div>
                            <div class="price-badge <?= $isFeatured ? 'price-badge--featured' : '' ?> w-full rounded-2xl px-5 pb-4 pt-5 text-left">
                                <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                                    <div>
                                        <p class="text-[11px] font-bold uppercase tracking-[0.2em] <?= $isFeatured ? 'text-[color:var(--gold-soft)]' : 'text-[color:var(--muted-strong)]' ?>">Cena</p>
                                        <p class="mt-2 text-3xl font-extrabold leading-none tracking-normal whitespace-nowrap <?= $isFeatured ? 'text-[color:var(--gold-soft)]' : 'text-[color:var(--accent-dark)]' ?>"><?= htmlspecialchars((string) $service['price_label'], ENT_QUOTES, 'UTF-8') ?></p>
                                    </div>
                                    <p class="text-sm font-semibold <?= $isFeatured ? 'text-[color:var(--cream-soft)]' : 'text-[color:var(--muted)]' ?>">cca <?= htmlspecialchars((string) $service['duration'], ENT_QUOTES, 'UTF-8') ?> minut</p>
                                </div>
                            </div>
                        </div>
                        <div class="mt-5 flex flex-col items-start gap-3 border-t pt-4 sm:flex-row sm:flex-wrap sm:items-center sm:justify-between <?= $isFeatured ? 'border-[rgba(241,200,121,0.18)]' : 'border-[var(--line-soft)]' ?>">
                            <p class="text-sm <?= $isFeatured ? 'text-[color:var(--cream-soft)]' : 'text-[color:var(--muted)]' ?>">
                                <?= htmlspecialchars((string) ($service['meta'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                            </p>
                            <a
                                href="index.php#booking"
                                class="inline-flex items-center justify-center rounded-xl border px-4 py-2.5 text-sm font-semibold transition focus:outline-none focus:ring-2 focus:ring-[var(--gold)] <?= $isFeatured ? 'border-[rgba(241,200,121,0.24)] bg-[rgba(255,255,255,0.08)] text-[color:var(--gold-soft)] hover:-translate-y-0.5 hover:border-[rgba(241,200,121,0.5)] hover:bg-[rgba(241,200,121,0.18)] hover:text-[color:var(--cream)]' : 'border-[rgba(74,58,48,0.14)] bg-[rgba(255,255,255,0.62)] text-[color:var(--accent-dark)] shadow-sm hover:-translate-y-0.5 hover:border-[rgba(192,138,62,0.42)] hover:bg-[rgba(192,138,62,0.14)] hover:text-[color:var(--ink)]' ?>"
                            >
                                Vybrat a rezervovat
                            </a>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
        <div class="reveal-item mt-5 rounded-2xl border border-[var(--line)] bg-white/80 p-5 shadow-sm" data-reveal-item>
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
                    <a href="mailto:renenemehair@seznam.cz?subject=D%C3%A1rkov%C3%BD%20poukaz%20Hair%20By%20ReneNeme" class="inline-flex items-center justify-center rounded-lg border border-[var(--line)] px-4 py-2.5 text-sm font-semibold text-[color:var(--ink)] transition hover:border-[var(--accent)] hover:text-[color:var(--accent-dark)] focus:outline-none focus:ring-2 focus:ring-[var(--gold)]">
                        Napsat e-mail
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>

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
                <a
                    href="https://www.google.com/maps/search/?api=1&query=Vackova%201064%2F39%2C%20612%2000%20Brno-Kr%C3%A1lovo%20Pole"
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
                <a href="index.php#booking" class="block hover:text-[color:var(--gold)]">Rezervace</a>
                <a href="cenik.php" class="block hover:text-[color:var(--gold)]">Ceník</a>
                <a href="<?= htmlspecialchars($instagramUrl, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener" class="block hover:text-[color:var(--gold)]">Instagram</a>
                <a href="contact.php" class="block hover:text-[color:var(--gold)]">Kontakt</a>
            </div>
        </div>
    </div>
    <div class="border-t border-[var(--surface-soft)] px-4 py-4 text-center text-xs text-[color:var(--cream-soft)]">
        <p>© <?php echo date('Y'); ?> Hair By ReneNeme · Made with love by Dejvidaak</p>
    </div>
</footer>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const mobileMenuButton = document.getElementById('mobileMenuButton');
    const mobileMenu = document.getElementById('mobileMenu');
    const menuIconOpen = document.getElementById('menuIconOpen');
    const menuIconClose = document.getElementById('menuIconClose');

    if (!mobileMenuButton || !mobileMenu || !menuIconOpen || !menuIconClose) return;

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

    const revealSections = Array.from(document.querySelectorAll('main > section:not([data-reveal-section-static])'));
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
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
                item.style.setProperty('--reveal-delay', `${Math.min(index * 90, 360)}ms`);
                revealItemObserver.observe(item);
            });
        }
    }
});
</script>
</body>
</html>
