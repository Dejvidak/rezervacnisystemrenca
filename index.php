<?php
require_once __DIR__ . '/config.php';

$services = app_services();
$instagramUrl = 'https://www.instagram.com/hairbyreneneme/';
$instagramHandle = '@hairbyreneneme';
$referenceCuts = [
    [
        'title' => 'Přirozený pánský střih',
        'description' => 'Lehce upravený tvar, čistší kontury a přirozený objem.',
        'image' => 'assets/references/moderni-pansky-strih.jpg',
    ],
    [
        'title' => 'Krátký fade',
        'description' => 'Kratší boky, čistý přechod a upravený horní objem.',
        'image' => 'assets/references/kratky-fade.jpg',
    ],
    [
        'title' => 'Upravený střih',
        'description' => 'Vyčištěné boky, uhlazený profil a střih připravený na běžné nošení.',
        'image' => 'assets/references/upraveny-strih.jpg',
    ],
    [
        'title' => 'Klasický styl',
        'description' => 'Nadčasový pánský střih s přirozenou délkou a měkkým tvarem.',
        'image' => 'assets/references/klasicky-styl.jpg',
    ],
    [
        'title' => 'Čistý fade',
        'description' => 'Výraznější přechod, čistá linie kolem uší a svěží celkový vzhled.',
        'image' => 'assets/references/cisty-fade.jpg',
    ],
    [
        'title' => 'Finální styling',
        'description' => 'Dokončený střih s lehkým stylingem pro upravený výsledný efekt.',
        'image' => 'assets/references/finalni-styling.jpg',
    ],
];
?>
<!DOCTYPE html>
<html lang="cs" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <title>Hair By ReneNeme - Pánské kadeřnictví & online rezervace</title>
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
            --cream: #F5EDE1;
            --cream-soft: #EDE8DD;
            --field: #F9F5EF;
            --field-border: #8C7560;
            --field-text: #231814;
            --accent: #C08A3E;
            --accent-dark: #94642C;
            --gold: #D6A85E;
            --gold-soft: #F1C879;
        }

        .booking-form {
            position: relative;
            box-shadow: 0 20px 40px rgba(43, 33, 28, 0.18);
            transform: translate3d(0, 0, 0);
            will-change: transform;
        }

        .booking-form::after {
            position: absolute;
            inset: -5px;
            z-index: -1;
            border: 2px solid rgba(192, 138, 62, 0.58);
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

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        @keyframes bookingPulse {
            0%, 100% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(214, 168, 94, 0.35);
            }
            50% {
                transform: scale(1.06);
                box-shadow: 0 0 0 12px rgba(214, 168, 94, 0);
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

        @media (prefers-reduced-motion: reduce) {
            .about-card__icon,
            .about-popover,
            .about-popover__panel,
            .gallery-lightbox,
            .gallery-lightbox__panel {
                transition: none;
            }
        }
    </style>
</head>
<body class="overflow-x-hidden bg-[var(--page)] text-[color:var(--ink)] antialiased">

<!-- NAV / HEADER -->
<header class="sticky top-0 z-50 bg-[var(--surface)] border-b border-[var(--surface-soft)] shadow-lg">
    <div class="max-w-6xl mx-auto flex items-center justify-between px-4 py-3">
        <a href="#top" class="whitespace-nowrap text-xl font-extrabold tracking-tight transition hover:opacity-90 sm:text-2xl md:text-[1.65rem]" aria-label="Hair By ReneNeme">
            <span class="text-[color:var(--cream)]">Hair By</span>
            <span class="text-[color:var(--gold)]">ReneNeme</span>
        </a>
        <nav class="hidden items-center gap-2 text-xs text-[color:var(--cream-soft)] lg:flex lg:gap-5 lg:text-sm">
            <a href="#about" class="whitespace-nowrap transition hover:text-[color:var(--gold)]">O nás</a>
            <a href="#visit" class="whitespace-nowrap transition hover:text-[color:var(--gold)]">Návštěva</a>
            <a href="#services" class="whitespace-nowrap transition hover:text-[color:var(--gold)]">Služby</a>
            <a href="#references" class="whitespace-nowrap transition hover:text-[color:var(--gold)]">Reference</a>
            <a href="#pricing" class="whitespace-nowrap transition hover:text-[color:var(--gold)]">Ceník</a>
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
    <nav id="mobileMenu" class="hidden max-h-[calc(100vh-4.25rem)] overflow-y-auto border-t border-[var(--surface-soft)] bg-[var(--surface)] px-4 pb-4 pt-2 text-sm text-[color:var(--cream-soft)] shadow-lg lg:hidden">
        <a href="#about" class="block rounded-lg px-3 py-3 hover:bg-[var(--surface-soft)] hover:text-[color:var(--gold)]">O nás</a>
        <a href="#visit" class="block rounded-lg px-3 py-3 hover:bg-[var(--surface-soft)] hover:text-[color:var(--gold)]">Návštěva</a>
        <a href="#services" class="block rounded-lg px-3 py-3 hover:bg-[var(--surface-soft)] hover:text-[color:var(--gold)]">Služby</a>
        <a href="#references" class="block rounded-lg px-3 py-3 hover:bg-[var(--surface-soft)] hover:text-[color:var(--gold)]">Reference</a>
        <a href="#pricing" class="block rounded-lg px-3 py-3 hover:bg-[var(--surface-soft)] hover:text-[color:var(--gold)]">Ceník</a>
        <a href="contact.php" class="block rounded-lg px-3 py-3 hover:bg-[var(--surface-soft)] hover:text-[color:var(--gold)]">Kontakt</a>
        <a href="<?= htmlspecialchars($instagramUrl, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener" class="flex items-center gap-2 rounded-lg px-3 py-3 hover:bg-[var(--surface-soft)] hover:text-[color:var(--gold)]">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <rect x="4" y="4" width="16" height="16" rx="5" stroke="currentColor" stroke-width="2" />
                <circle cx="12" cy="12" r="3.5" stroke="currentColor" stroke-width="2" />
                <path d="M17 7.2h.01" stroke="currentColor" stroke-width="3" stroke-linecap="round" />
            </svg>
            Instagram
        </a>
        <a href="#booking" class="mt-2 block rounded-lg bg-[var(--accent)] px-3 py-3 text-center font-semibold text-[color:var(--cream)] shadow-sm hover:bg-[var(--accent-dark)]">
            Rezervovat termín
        </a>
    </nav>
</header>

<main id="top" class="max-w-6xl mx-auto px-4 pb-14 sm:px-6 md:pb-16">

    <!-- HERO sekce-->
    <section class="grid gap-8 py-8 md:grid-cols-[0.95fr_1.05fr] md:items-center md:gap-10 md:py-16">
        <div>
            <p class="mb-3 text-[11px] font-bold uppercase tracking-[0.22em] text-[color:var(--muted-strong)] sm:text-xs sm:tracking-[0.28em]">Pánské kadeřnictví · Brno</p>
            <h1 class="mb-4 text-3xl font-extrabold leading-tight sm:text-4xl md:text-5xl">
                Pánské střihy, které sedí.
                V klidu a <span class="text-[color:var(--accent)]">bez čekání</span>.
            </h1>
            <p class="text-sm md:text-base mb-6 text-[color:var(--muted)]">
                Vyber si službu, volný termín a přijď rovnou do křesla.
                Hair By ReneNeme spojuje pečlivý střih, pohodovou atmosféru a rezervaci bez zbytečného domlouvání.
            </p>
            <div class="flex flex-col gap-3 sm:flex-row sm:flex-wrap">
                <a href="#booking"
                   class="inline-flex w-full items-center justify-center rounded-lg bg-[var(--accent)] px-5 py-3 text-sm font-semibold text-[color:var(--cream)] shadow-lg transition hover:-translate-y-0.5 hover:bg-[var(--accent-dark)] hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-[var(--accent)] sm:w-auto sm:py-2.5">
                    Rezervovat termín
                </a>
                <a href="#pricing"
                   class="inline-flex w-full items-center justify-center rounded-lg border border-[var(--surface-soft)] px-5 py-3 text-sm font-semibold text-[color:var(--surface-soft)] transition hover:-translate-y-0.5 hover:bg-[var(--surface-soft)] hover:text-[color:var(--cream)] focus:outline-none focus:ring-2 focus:ring-[var(--surface-soft)] sm:w-auto sm:py-2.5">
                    Podívat se na ceník
                </a>
            </div>
            <div class="mt-7 grid max-w-xl grid-cols-1 gap-3 text-sm min-[420px]:grid-cols-3 md:mt-8">
                <div class="border-l-2 border-[var(--accent)] pl-3">
                    <p class="font-bold">35-50 min</p>
                    <p class="text-xs text-[color:var(--muted-strong)]">délka služeb</p>
                </div>
                <div class="border-l-2 border-[var(--accent)] pl-3">
                    <p class="font-bold">Online</p>
                    <p class="text-xs text-[color:var(--muted-strong)]">rezervace</p>
                </div>
                <div class="border-l-2 border-[var(--accent)] pl-3">
                    <p class="font-bold">Brno</p>
                    <p class="text-xs text-[color:var(--muted-strong)]">Královo Pole</p>
                </div>
            </div>
            <div class="mt-7 flex flex-col gap-2 text-sm text-[color:var(--muted)] sm:flex-row sm:flex-wrap sm:items-center sm:gap-x-4 md:mt-8">
                <span>Otevřeno Po-Pá 9:00-19:00 · So 9:00-14:00</span>
                <a
                    href="https://www.google.com/maps/search/?api=1&query=Vackova%201064%2F39%2C%20612%2000%20Brno-Kr%C3%A1lovo%20Pole"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="font-semibold underline decoration-[var(--field-border)] underline-offset-4 hover:text-[color:var(--surface-soft)]"
                >
                    Vackova 1064/39, 612 00 Brno-Královo Pole
                </a>
            </div>
        </div>
        <div class="relative">
            <img
                src="assets/barbershop-hero.png"
                alt="Moderní interiér pánského kadeřnictví"
                class="h-[15rem] w-full rounded-lg border border-[var(--line)] object-cover shadow-2xl min-[420px]:h-[18rem] sm:h-[22rem] md:h-[30rem]"
            >
            <div class="absolute bottom-3 left-3 right-3 rounded-lg bg-[rgba(43,33,28,0.9)] p-3 text-[color:var(--cream)] shadow-xl backdrop-blur sm:bottom-4 sm:left-4 sm:right-4 sm:p-4">
                <p class="text-[10px] uppercase tracking-[0.2em] text-[color:var(--gold)] sm:text-xs sm:tracking-[0.24em]">Rezervace bez volání</p>
                <p class="mt-1 text-base font-bold sm:text-lg">Vybereš službu, datum a čas.</p>
                <p class="mt-1 text-sm text-[color:var(--cream-soft)]">Potvrzení dorazí e-mailem a termín se zapíše do kalendáře.</p>
            </div>
        </div>
    </section>

    <!-- O NÁS -->
    <section id="about" class="scroll-mt-28 border-t border-[var(--line)] py-8 md:scroll-mt-32 md:py-10">
        <h2 class="mb-4 text-2xl font-bold sm:text-3xl">O nás</h2>
        <p class="text-m text-[color:var(--muted)] mb-4 max-w-3xl">
            <strong>Hair By ReneNeme</strong> je malé pánské kadeřnictví v Brně, kde se řeší hlavně dobrý střih,
            čistý výsledek a pohodová návštěva. Každý účes se ladí podle vlasů, stylu i toho,
            kolik času mu chceš doma věnovat.
        </p>
        <div class="grid md:grid-cols-3 gap-4 text-sm">
            <article class="about-card rounded-lg border border-[var(--line)] bg-white/70 p-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                <button
                    type="button"
                    class="about-card__button flex w-full items-start justify-between gap-3 text-left"
                    data-about-title="Přátelský přístup"
                    data-about-summary="Klidná návštěva bez zbytečného spěchu. Sedneš si, domluvíme styl a jde se na věc."
                    data-about-detail="Renata se ptá na styl, zvyky i to, jak moc chceš účes ráno řešit. Cílem je střih, který bude vypadat dobře nejen po odchodu z křesla, ale i další dny doma."
                >
                    <span>
                        <span class="mb-1 block font-semibold">Přátelský přístup</span>
                        <span class="block text-[color:var(--muted)]">Klidná návštěva bez zbytečného spěchu. Sedneš si, domluvíme styl a jde se na věc.</span>
                    </span>
                    <span class="about-card__icon mt-1 inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-[var(--cream)] text-[color:var(--accent)]" aria-hidden="true">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none">
                            <path d="M7 17L17 7M9 7h8v8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </span>
                </button>
            </article>
            <article class="about-card rounded-lg border border-[var(--line)] bg-white/70 p-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                <button
                    type="button"
                    class="about-card__button flex w-full items-start justify-between gap-3 text-left"
                    data-about-title="Moderní střihy"
                    data-about-summary="Od klasiky po výraznější fade. Vždy tak, aby střih seděl k vlasům i běžnému nošení."
                    data-about-detail="Střih může být čistý, výrazný nebo úplně přirozený. Klidně dones fotku inspirace, výsledek se ale vždy upraví podle tvaru hlavy, hustoty vlasů a toho, co ti bude prakticky fungovat."
                >
                    <span>
                        <span class="mb-1 block font-semibold">Moderní střihy</span>
                        <span class="block text-[color:var(--muted)]">Od klasiky po výraznější fade. Vždy tak, aby střih seděl k vlasům i běžnému nošení.</span>
                    </span>
                    <span class="about-card__icon mt-1 inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-[var(--cream)] text-[color:var(--accent)]" aria-hidden="true">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none">
                            <path d="M7 17L17 7M9 7h8v8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </span>
                </button>
            </article>
            <article class="about-card rounded-lg border border-[var(--line)] bg-white/70 p-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                <button
                    type="button"
                    class="about-card__button flex w-full items-start justify-between gap-3 text-left"
                    data-about-title="Online rezervace"
                    data-about-summary="Termín si vybereš rovnou online. Bez volání, přepisování zpráv a čekání na potvrzení."
                    data-about-detail="Rezervace hlídá délku služby i dostupné časy. Po odeslání máš termín potvrzený e-mailem, takže nemusíš volat ani čekat na odpověď ve zprávách."
                >
                    <span>
                        <span class="mb-1 block font-semibold">Online rezervace</span>
                        <span class="block text-[color:var(--muted)]">Termín si vybereš rovnou online. Bez volání, přepisování zpráv a čekání na potvrzení.</span>
                    </span>
                    <span class="about-card__icon mt-1 inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-[var(--cream)] text-[color:var(--accent)]" aria-hidden="true">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none">
                            <path d="M7 17L17 7M9 7h8v8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </span>
                </button>
            </article>
        </div>
    </section>

    <!-- PRŮBĚH NÁVŠTĚVY -->
    <section id="visit" class="scroll-mt-28 border-t border-[var(--line)] py-8 md:scroll-mt-32 md:py-10">
        <div class="mb-5 max-w-3xl">
            <p class="text-xs uppercase tracking-[0.24em] text-[color:var(--muted-strong)] font-bold">Jak probíhá návštěva</p>
            <h2 class="mt-1 text-2xl font-bold">V klidu od příchodu až po odchod</h2>
            <p class="mt-3 text-sm leading-6 text-[color:var(--muted)]">
                Žádné zbytečné formality. Domluvíme styl, vezmeme to pečlivě a na konci odejdeš upravený,
                svěží a připravený rovnou mezi lidi.
            </p>
        </div>
        <div class="grid gap-4 md:grid-cols-3">
            <div class="rounded-lg border border-[var(--line)] bg-white/70 p-5 shadow-sm">
                <p class="mb-3 inline-flex h-9 w-9 items-center justify-center rounded-full bg-[var(--surface)] text-sm font-bold text-[color:var(--gold)]">1</p>
                <h3 class="font-semibold">Domluvíme styl</h3>
                <p class="mt-2 text-sm leading-6 text-[color:var(--muted)]">
                    Krátce probereme, co chceš, jak se o vlasy staráš a co má dávat smysl i po pár dnech.
                </p>
            </div>
            <div class="rounded-lg border border-[var(--line)] bg-white/70 p-5 shadow-sm">
                <p class="mb-3 inline-flex h-9 w-9 items-center justify-center rounded-full bg-[var(--surface)] text-sm font-bold text-[color:var(--gold)]">2</p>
                <h3 class="font-semibold">Střih bez spěchu</h3>
                <p class="mt-2 text-sm leading-6 text-[color:var(--muted)]">
                    Během střihu si dáš fresh nápoj a chvíli vypneš. My mezitím doladíme tvar, detaily i styling.
                </p>
            </div>
            <div class="rounded-lg border border-[var(--line)] bg-white/70 p-5 shadow-sm">
                <p class="mb-3 inline-flex h-9 w-9 items-center justify-center rounded-full bg-[var(--surface)] text-sm font-bold text-[color:var(--gold)]">3</p>
                <h3 class="font-semibold">Finální dojem</h3>
                <p class="mt-2 text-sm leading-6 text-[color:var(--muted)]">
                    Na závěr střih zkontrolujeme, upravíme poslední detaily a při odchodu budeš i krásně vonět.
                </p>
            </div>
        </div>
    </section>

    <!-- SLUŽBY -->
    <section id="services" class="scroll-mt-28 border-t border-[var(--line)] py-8 md:scroll-mt-32 md:py-10">
        <h2 class="text-2xl font-bold mb-4">Služby</h2>
        <div class="grid md:grid-cols-2 gap-4 text-sm">
            <div class="rounded-lg border border-[var(--surface-soft)] bg-[var(--ink)] p-5 text-[color:var(--cream)] shadow-lg transition hover:-translate-y-0.5 hover:border-[var(--gold)]">
                <p class="font-semibold mb-1">Pánský střih</p>
                <p class="mb-1 text-[color:var(--cream-soft)]">Rychlý, čistý střih na sucho bez mytí a úpravy vousů. (35 min)</p>
                
            </div>
            <div class="rounded-lg border border-[var(--surface-soft)] bg-[var(--ink)] p-5 text-[color:var(--cream)] shadow-lg transition hover:-translate-y-0.5 hover:border-[var(--gold)]">
                <p class="font-semibold mb-1">Kompletka 1 (zahrnuje střih a mytí)</p>
                <p class="mb-1 text-[color:var(--cream-soft)]">Střih s mytím, případně úpravou vousů podle domluvy. (45 min)</p>
                
            </div>
            <div class="rounded-lg border border-[var(--surface-soft)] bg-[var(--ink)] p-5 text-[color:var(--cream)] shadow-lg transition hover:-translate-y-0.5 hover:border-[var(--gold)]">
                <p class="font-semibold mb-1">Kompletka 2</p>
                <p class="mb-1 text-[color:var(--cream-soft)]">Střih vlasů, úprava vousů a finální styling v jednom termínu. (50 min)</p>
                
            </div>
            <div class="rounded-lg border border-[var(--surface-soft)] bg-[var(--ink)] p-5 text-[color:var(--cream)] shadow-lg transition hover:-translate-y-0.5 hover:border-[var(--gold)]">
                <p class="font-semibold mb-1">Dětský střih</p>
                <p class="mb-1 text-[color:var(--cream-soft)]">Chlapecký i holčičí střih. Cena se liší podle náročnosti, například výrazně vyholené boky mohou být za 420 Kč. (30 min)</p>
                
            </div>
        </div>
    </section>

    <!-- REFERENCE STŘIHŮ -->
    <section id="references" class="scroll-mt-28 border-t border-[var(--line)] py-8 md:scroll-mt-32 md:py-10">
        <div class="mb-5 flex flex-wrap items-end justify-between gap-3">
            <div>
                <p class="text-xs uppercase tracking-[0.24em] text-[color:var(--muted-strong)] font-bold">Inspirace před rezervací</p>
                <h2 class="mt-1 text-2xl font-bold">Reference střihů</h2>
            </div>
            <div class="flex flex-wrap gap-3 text-sm font-semibold">
                <a href="<?= htmlspecialchars($instagramUrl, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener" class="inline-flex items-center gap-2 text-[color:var(--accent)] underline decoration-[var(--accent)] underline-offset-4 hover:text-[color:var(--accent-dark)]">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <rect x="4" y="4" width="16" height="16" rx="5" stroke="currentColor" stroke-width="2" />
                        <circle cx="12" cy="12" r="3.5" stroke="currentColor" stroke-width="2" />
                        <path d="M17 7.2h.01" stroke="currentColor" stroke-width="3" stroke-linecap="round" />
                    </svg>
                    <?= htmlspecialchars($instagramHandle, ENT_QUOTES, 'UTF-8') ?>
                </a>
                <a href="#booking" class="text-[color:var(--accent)] underline decoration-[var(--accent)] underline-offset-4 hover:text-[color:var(--accent-dark)]">
                    Rezervovat podobný střih
                </a>
            </div>
        </div>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <?php foreach ($referenceCuts as $cut): ?>
                <?php $hasImage = is_file(__DIR__ . '/' . $cut['image']); ?>
                <?php if ($hasImage): ?>
                    <a
                        href="<?= htmlspecialchars($cut['image'], ENT_QUOTES, 'UTF-8') ?>"
                        data-gallery-image="<?= htmlspecialchars($cut['image'], ENT_QUOTES, 'UTF-8') ?>"
                        data-gallery-title="<?= htmlspecialchars($cut['title'], ENT_QUOTES, 'UTF-8') ?>"
                        data-gallery-description="<?= htmlspecialchars($cut['description'], ENT_QUOTES, 'UTF-8') ?>"
                        class="group overflow-hidden rounded-lg border border-[var(--line)] bg-white/80 shadow-sm transition hover:-translate-y-0.5 hover:shadow-lg"
                    >
                        <img
                            src="<?= htmlspecialchars($cut['image'], ENT_QUOTES, 'UTF-8') ?>"
                            alt="<?= htmlspecialchars($cut['title'], ENT_QUOTES, 'UTF-8') ?>"
                            class="aspect-[4/5] w-full object-cover transition duration-300 group-hover:scale-[1.03]"
                            loading="lazy"
                        >
                        <div class="p-4">
                            <p class="font-semibold"><?= htmlspecialchars($cut['title'], ENT_QUOTES, 'UTF-8') ?></p>
                            <p class="mt-1 text-sm text-[color:var(--muted)]"><?= htmlspecialchars($cut['description'], ENT_QUOTES, 'UTF-8') ?></p>
                            <p class="mt-3 text-xs font-semibold uppercase tracking-[0.18em] text-[color:var(--accent)]">Zobrazit foto</p>
                        </div>
                    </a>
                <?php else: ?>
                    <div class="rounded-lg border border-dashed border-[var(--line)] bg-white/55 p-4 shadow-sm">
                        <div class="flex aspect-[4/5] items-center justify-center rounded-md bg-[var(--ink)] px-5 text-center text-[color:var(--cream)]">
                            <p class="text-sm font-semibold"><?= htmlspecialchars($cut['title'], ENT_QUOTES, 'UTF-8') ?></p>
                        </div>
                        <p class="mt-4 font-semibold"><?= htmlspecialchars($cut['title'], ENT_QUOTES, 'UTF-8') ?></p>
                        <p class="mt-1 text-sm text-[color:var(--muted)]"><?= htmlspecialchars($cut['description'], ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </section>
    <!-- CENÍK -->
    <section id="pricing" class="scroll-mt-28 border-t border-[var(--line)] py-8 md:scroll-mt-32 md:py-10">
        <div class="mb-5 flex flex-wrap items-end justify-between gap-3">
            <div>
                <p class="text-xs uppercase tracking-[0.24em] text-[color:var(--muted-strong)] font-bold">Transparentní ceny</p>
                <h2 class="mt-1 text-2xl font-bold">Ceník</h2>
            </div>
            <a href="#booking" class="text-sm font-semibold text-[color:var(--accent)] underline decoration-[var(--accent)] underline-offset-4 hover:text-[color:var(--accent-dark)]">
                Rezervovat podle ceníku
            </a>
        </div>
        <div class="overflow-hidden rounded-lg border border-[var(--line)] bg-white/75 shadow-sm">
            <?php foreach ($services as $serviceName => $service): ?>
                <div class="flex justify-between gap-4 border-b border-[var(--line-soft)] px-4 py-3 last:border-b-0 hover:bg-[var(--field)]">
                    <span><?= htmlspecialchars($serviceName, ENT_QUOTES, 'UTF-8') ?></span>
                    <span class="font-bold text-[color:var(--ink)] whitespace-nowrap"><?= htmlspecialchars($service['price_label'], ENT_QUOTES, 'UTF-8') ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- REZERVAČNÍ FORMULÁŘ -->
    <section id="booking" class="mt-2 scroll-mt-24 border-t border-[var(--line)] py-8 md:mt-4 md:scroll-mt-28 md:py-10">
        <div class="grid gap-6 md:grid-cols-[0.8fr_1.2fr] md:items-start md:gap-8">
            <div>
                <p class="text-xs uppercase tracking-[0.24em] text-[color:var(--muted-strong)] font-bold">Bez telefonování</p>
                <h2 class="mt-1 text-2xl font-bold mb-3">Online rezervace</h2>
                <p class="text-sm text-[color:var(--muted)]">
                    Vyplň pár údajů a vyber si volný čas. Po odeslání uvidíš shrnutí rezervace
                    a potvrzení ti přijde e-mailem.
                </p>
                <div class="mt-6 space-y-3 text-sm text-[color:var(--muted)]">
                    <div class="rounded-lg border border-[var(--line)] bg-white/70 p-4">
                        <p class="font-semibold text-[color:var(--ink)]">Termín držíme hned po odeslání</p>
                        <p class="mt-1">Systém počítá s délkou vybrané služby a ukáže jen dostupné časy.</p>
                    </div>
                    <div class="rounded-lg border border-[var(--line)] bg-white/70 p-4">
                        <p class="font-semibold text-[color:var(--ink)]">Potvrzení do e-mailu</p>
                        <p class="mt-1">Dostaneš přehled rezervace a termín se zapíše do kalendáře.</p>
                    </div>
                </div>
            </div>

            <div>
                <form id="bookingForm" action="save_reservation.php" method="POST" class="booking-form space-y-4 rounded-lg bg-[var(--ink)] p-4 text-[color:var(--cream)] sm:p-5 md:p-6">
                    <!-- Jméno -->
                    <div>
                        <label for="name" class="block text-sm font-medium mb-1">Jméno a příjmení *</label>
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
                        <label for="phone" class="block text-sm font-medium mb-1">Telefon *</label>
                        <input
                            type="tel"
                            id="phone"
                            name="phone"
                            required
                            class="w-full rounded-lg bg-[var(--field)] border border-[var(--field-border)] px-3 py-2 text-base text-[color:var(--field-text)] sm:text-sm focus:outline-none focus:ring-2 focus:ring-[var(--gold)]"
                            placeholder="např. +420 777 123 456"
                        >
                    </div>

                    <!-- Datum + čas -->
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div>
                            <label for="date" class="block text-sm font-medium mb-1">Datum *</label>
                            <input
                                type="date"
                                id="date"
                                name="date"
                                required
                                class="w-full rounded-lg bg-[var(--field)] border border-[var(--field-border)] px-3 py-2 text-base text-[color:var(--field-text)] sm:text-sm focus:outline-none focus:ring-2 focus:ring-[var(--gold)]"
                            >
                        </div>
                        <div>
                            <label for="time" class="block text-sm font-medium mb-1">Čas *</label>
                            <select
                                     id="time"
                                     name="time"
                                     required
                                     class="w-full rounded-lg bg-[var(--field)] border border-[var(--field-border)] px-3 py-2 text-base text-[color:var(--field-text)] sm:text-sm focus:outline-none focus:ring-2 focus:ring-[var(--gold)]"
                                    >
                            <option value="">Nejprve vyber datum</option>
                            </select>

                        </div>
                    </div>

                    <!-- Typ služby -->
                    <div>
                        <label for="service" class="block text-sm font-medium mb-1">Typ služby *</label>
                        <select
                            id="service"
                            name="service"
                            required
                            class="w-full rounded-lg bg-[var(--field)] border border-[var(--field-border)] px-3 py-2 text-base text-[color:var(--field-text)] sm:text-sm focus:outline-none focus:ring-2 focus:ring-[var(--gold)]"
                        >
                            <option value="">Vyber službu...</option>
                            <?php foreach ($services as $serviceName => $service): ?>
                                <option value="<?= htmlspecialchars($serviceName, ENT_QUOTES, 'UTF-8') ?>">
                                    <?= htmlspecialchars($serviceName, ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <p id="priceInfo" class="mt-1 text-xs text-[color:var(--cream-soft)] hidden">
                            Cena služby: <span id="priceValue"></span>
                        </p>
                    </div>

                    <!-- Poznámka -->
                    <div>
                        <label for="note" class="block text-sm font-medium mb-3">Poznámka (nepovinné)</label>
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
                            Souhlasím se zpracováním osobních údajů pro účely rezervace.
                        </label>
                    </div>

                    <!-- Chyba z JS -->
                    <p id="errorMsg" class="text-xs text-red-300 hidden"></p>
                    <p id="bookingLoadingMsg" class="hidden rounded-lg border border-[var(--surface-soft)] bg-[rgba(255,255,255,0.06)] px-3 py-2 text-xs text-[color:var(--cream-soft)]">
                        Rezervaci právě ukládáme. Chvilku vydrž, kontrolujeme termín a posíláme potvrzení.
                    </p>

                    <!-- Tlačítko -->
                    <button
                        type="submit"
                        id="bookingSubmitButton"
                        class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-[var(--accent)] py-3 text-sm font-semibold shadow-lg transition hover:-translate-y-0.5 hover:bg-[var(--accent-dark)] hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-[var(--gold)] disabled:cursor-wait disabled:opacity-80"
                    >
                        <span id="bookingSubmitText">Odeslat rezervaci</span>
                        <span id="bookingSubmitLoading" class="hidden items-center gap-2">
                            <span class="booking-submit__spinner h-4 w-4 rounded-full border-2 border-[rgba(245,237,225,0.42)] border-t-[var(--cream)]"></span>
                            Odesíláme...
                        </span>
                    </button>
                </form>
            </div>
        </div>
    </section>
</main>

<div
    id="bookingLoadingOverlay"
    class="booking-loading-overlay fixed inset-0 z-[120] flex items-center justify-center bg-[rgba(43,33,28,0.76)] p-4 backdrop-blur-md"
    aria-hidden="true"
>
    <div class="booking-loading-card max-h-[calc(100vh-2rem)] w-full max-w-md overflow-y-auto rounded-lg border border-[var(--surface-soft)] bg-[var(--cream)] p-5 text-center shadow-2xl sm:p-6">
        <div class="booking-loading-pulse mx-auto mb-5 inline-flex h-16 w-16 items-center justify-center rounded-full bg-[var(--accent)] text-[color:var(--cream)] shadow-lg">
            <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M5 12.5l4.2 4.2L19 7" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </div>
        <p class="text-xs font-bold uppercase tracking-[0.24em] text-[color:var(--muted-strong)]">Rezervace se odesílá</p>
        <h2 class="mt-2 text-2xl font-extrabold text-[color:var(--ink)]">Držíme ti termín</h2>
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
    </div>
</div>

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
    <section class="about-popover__panel relative z-10 max-h-[calc(100vh-2rem)] w-full max-w-2xl overflow-y-auto rounded-lg border border-[var(--line)] bg-[var(--cream)] shadow-2xl">
        <button
            type="button"
            id="aboutClose"
            class="absolute right-3 top-3 z-20 inline-flex h-10 w-10 items-center justify-center rounded-full bg-[rgba(43,33,28,0.78)] text-[color:var(--cream)] shadow-lg transition hover:bg-[var(--ink)] focus:outline-none focus:ring-2 focus:ring-[var(--gold)]"
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
            <p id="aboutPopoverSummary" class="text-base font-semibold text-[color:var(--ink)]"></p>
            <p id="aboutPopoverDetail" class="mt-4 leading-7 text-[color:var(--muted)]"></p>
            <a href="#booking" class="mt-6 inline-flex rounded-lg bg-[var(--accent)] px-5 py-3 text-sm font-semibold text-[color:var(--cream)] shadow-md transition hover:-translate-y-0.5 hover:bg-[var(--accent-dark)] hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-[var(--gold)]">
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
    <div class="gallery-lightbox__panel relative z-10 max-h-[calc(100vh-2rem)] w-full max-w-4xl overflow-y-auto">
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
        <figure class="overflow-hidden rounded-lg bg-[var(--ink)] shadow-2xl">
            <img
                id="galleryImage"
                src=""
                alt=""
                class="max-h-[68vh] w-full object-contain bg-[var(--ink)] sm:max-h-[78vh]"
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
                <a href="tel:+420608419610" class="block hover:text-[color:var(--gold)]">608 419 610</a>
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
                <a href="#booking" class="block hover:text-[color:var(--gold)]">Rezervace</a>
                <a href="#pricing" class="block hover:text-[color:var(--gold)]">Ceník</a>
                <a href="<?= htmlspecialchars($instagramUrl, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener" class="block hover:text-[color:var(--gold)]">Instagram</a>
                <a href="contact.php" class="block hover:text-[color:var(--gold)]">Kontakt</a>
            </div>
        </div>
    </div>
    <div class="border-t border-[var(--surface-soft)] px-4 py-4 text-center text-xs text-[color:var(--cream-soft)]">
        <p>© <?php echo date('Y'); ?> Hair By ReneNeme · Made with love by Dejvidaak</p>
    </div>
</footer>


<!-- JavaScript – datum/čas + cena -->
<script>
    const serviceSelect = document.getElementById('service');
    const dateInput = document.getElementById('date');
    const timeInput = document.getElementById('time');
    const priceInfo = document.getElementById('priceInfo');
    const priceValue = document.getElementById('priceValue');
    const errorMsg = document.getElementById('errorMsg');
    const bookingSubmitButton = document.getElementById('bookingSubmitButton');
    const bookingSubmitText = document.getElementById('bookingSubmitText');
    const bookingSubmitLoading = document.getElementById('bookingSubmitLoading');
    const bookingLoadingMsg = document.getElementById('bookingLoadingMsg');
    const bookingLoadingOverlay = document.getElementById('bookingLoadingOverlay');
    const bookingLoadingStep = document.getElementById('bookingLoadingStep');
    const services = <?= json_encode($services, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;
    let isBookingSubmitting = false;
    let bookingLoadingStepTimer = null;

    // Minimální dnešní datum
    const today = new Date();
    const todayDateStr = formatLocalDate(today);
    dateInput.min = todayDateStr;

    // Zobrazení ceny pod selectem místo alertu
    serviceSelect.addEventListener('change', () => {
        const selected = serviceSelect.value;
        if (services[selected]) {
            priceValue.textContent = services[selected].price_label;
            priceInfo.classList.remove('hidden');
        } else {
            priceInfo.classList.add('hidden');
        }
    });

    // Kontrola, že nejde rezervovat minulost
    document.querySelector('#booking form').addEventListener('submit', function(e) {
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
            errorMsg.textContent = "Nemůžeš si rezervovat termín v minulosti (datum).";
            errorMsg.classList.remove('hidden');
            return;
        }

        if (chosenDate === todayStr && chosenTime < nowTimeStr) {
            e.preventDefault();
            errorMsg.textContent = "Nemůžeš si rezervovat čas, který už proběhl.";
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
        bookingLoadingOverlay?.setAttribute('aria-hidden', 'false');
        document.body.classList.add('overflow-hidden');

        const loadingSteps = [
            'Kontrolujeme dostupnost vybraného času...',
            'Ukládáme rezervaci do systému...',
            'Posíláme potvrzení a připravujeme shrnutí...'
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
    });

    window.addEventListener('pageshow', () => {
        window.clearInterval(bookingLoadingStepTimer);
        isBookingSubmitting = false;
        bookingSubmitButton.disabled = false;
        bookingSubmitText?.classList.remove('hidden');
        bookingSubmitLoading?.classList.add('hidden');
        bookingSubmitLoading?.classList.remove('inline-flex');
        bookingLoadingMsg?.classList.add('hidden');
        bookingLoadingOverlay?.classList.remove('is-visible');
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
    const dateInput = document.getElementById('date');
    const timeSelect = document.getElementById('time');
    const serviceSelect = document.getElementById('service');
    let previouslyFocusedElement = null;
    let aboutPreviouslyFocusedElement = null;
    let activeAboutTrigger = null;
    let aboutCloseTimer = null;
    let activeGalleryTrigger = null;
    let galleryCloseTimer = null;

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

    document.querySelectorAll('a[href="#booking"]').forEach(link => {
        link.addEventListener('click', event => {
            if (!bookingSection) return;

            event.preventDefault();
            if (mobileMenu && !mobileMenu.classList.contains('hidden')) {
                mobileMenu.classList.add('hidden');
                menuIconOpen?.classList.remove('hidden');
                menuIconClose?.classList.add('hidden');
                mobileMenuButton?.setAttribute('aria-expanded', 'false');
                mobileMenuButton?.setAttribute('aria-label', 'Otevřít menu');
            }

            bookingSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
            window.history.pushState(null, '', '#booking');
            window.setTimeout(focusBookingForm, 520);
        });
    });

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
