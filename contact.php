<?php
$instagramUrl = 'https://www.instagram.com/hairbyreneneme/';
$instagramHandle = '@hairbyreneneme';
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Kontakt - Hair By ReneNeme</title>
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
            --accent: #C08A3E;
            --accent-dark: #94642C;
            --gold: #D6A85E;
            --gold-soft: #F1C879;
        }
    </style>
</head>
<body class="min-h-screen overflow-x-hidden bg-[var(--page)] text-[color:var(--ink)] antialiased">

<header class="sticky top-0 z-50 bg-[var(--surface)] border-b border-[var(--surface-soft)] shadow-lg">
    <div class="max-w-6xl mx-auto flex items-center justify-between px-4 py-3">
        <a href="index.php" class="whitespace-nowrap text-xl font-extrabold tracking-tight transition hover:opacity-90 sm:text-2xl md:text-[1.65rem]" aria-label="Hair By ReneNeme">
            <span class="text-[color:var(--cream)]">Hair By</span>
            <span class="text-[color:var(--gold)]">ReneNeme</span>
        </a>
        <nav class="hidden items-center gap-2 text-xs text-[color:var(--cream-soft)] lg:flex lg:gap-5 lg:text-sm">
            <a href="index.php#about" class="whitespace-nowrap transition hover:text-[color:var(--gold)]">O nás</a>
            <a href="index.php#visit" class="whitespace-nowrap transition hover:text-[color:var(--gold)]">Návštěva</a>
            <a href="index.php#services" class="whitespace-nowrap transition hover:text-[color:var(--gold)]">Služby</a>
            <a href="index.php#references" class="whitespace-nowrap transition hover:text-[color:var(--gold)]">Reference</a>
            <a href="cenik.php" class="whitespace-nowrap transition hover:text-[color:var(--gold)]">Ceník</a>
            <a href="contact.php" class="whitespace-nowrap font-semibold text-[color:var(--gold)]">Kontakt</a>
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
        <a href="index.php#references" class="block rounded-lg px-3 py-3 hover:bg-[var(--surface-soft)] hover:text-[color:var(--gold)]">Reference</a>
        <a href="cenik.php" class="block rounded-lg px-3 py-3 hover:bg-[var(--surface-soft)] hover:text-[color:var(--gold)]">Ceník</a>
        <a href="contact.php" class="block rounded-lg px-3 py-3 font-semibold text-[color:var(--gold)] hover:bg-[var(--surface-soft)]">Kontakt</a>
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
    <section class="grid gap-7 md:grid-cols-[0.85fr_1.15fr] md:items-start">
        <div>
            <p class="mb-3 text-[11px] font-bold uppercase tracking-[0.22em] text-[color:var(--muted-strong)] sm:text-xs sm:tracking-[0.28em]">Kontakt</p>
            <h1 class="text-3xl font-extrabold leading-tight sm:text-4xl md:text-5xl">
                Hair By <span class="text-[color:var(--accent)]">ReneNeme</span>
            </h1>
            <p class="mt-4 max-w-xl text-sm leading-7 text-[color:var(--muted)] md:text-base">
                Nejrychlejší cesta k termínu je online rezervace. Pokud potřebujete domluvit detail ke střihu,
                upravit termín nebo dohledat fakturační údaje, všechno najdete tady
            </p>
            <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                <a href="index.php" class="inline-flex items-center justify-center rounded-lg border border-[var(--surface-soft)] px-5 py-3 text-sm font-semibold text-[color:var(--surface-soft)] transition hover:-translate-y-0.5 hover:bg-[var(--surface-soft)] hover:text-[color:var(--cream)] focus:outline-none focus:ring-2 focus:ring-[var(--surface-soft)]">
                    Zpět na web
                </a>
                <a href="index.php#booking" class="inline-flex items-center justify-center text-sm font-semibold text-[color:var(--accent)] underline decoration-[var(--accent)] underline-offset-4 transition hover:text-[color:var(--accent-dark)] focus:outline-none focus:ring-2 focus:ring-[var(--gold)]">
                    Přejít na rezervaci
                </a>
            </div>
        </div>

        <div class="rounded-lg border border-[var(--line)] bg-[var(--cream)] p-5 shadow-xl shadow-[rgba(43,33,28,0.08)] sm:p-7">
            <div class="grid gap-4 min-[460px]:grid-cols-2">
                <div class="rounded-lg border border-[var(--line-soft)] bg-[var(--page)] p-4">
                    <p class="text-xs font-bold uppercase tracking-[0.2em] text-[color:var(--muted-strong)]">Kontaktní osoba</p>
                    <p class="mt-2 text-lg font-bold">Renata Nemeškalová</p>
                </div>
                <div class="rounded-lg border border-[var(--line-soft)] bg-[var(--page)] p-4">
                    <p class="text-xs font-bold uppercase tracking-[0.2em] text-[color:var(--muted-strong)]">IČO</p>
                    <p class="mt-2 text-lg font-bold">19671415</p>
                </div>
                <a href="tel:+420608419610" class="rounded-lg border border-[var(--line-soft)] bg-[var(--page)] p-4 transition hover:-translate-y-0.5 hover:border-[var(--accent)] hover:shadow-md">
                    <p class="text-xs font-bold uppercase tracking-[0.2em] text-[color:var(--muted-strong)]">Telefon</p>
                    <p class="mt-2 text-base font-bold min-[420px]:text-lg">+420 608 419 610</p>
                </a>
                <a href="mailto:renenemehair@seznam.cz" class="rounded-lg border border-[var(--line-soft)] bg-[var(--page)] p-4 transition hover:-translate-y-0.5 hover:border-[var(--accent)] hover:shadow-md">
                    <p class="text-xs font-bold uppercase tracking-[0.2em] text-[color:var(--muted-strong)]">E-mail</p>
                    <p class="mt-2 break-words text-base font-bold min-[420px]:text-lg">renenemehair@seznam.cz</p>
                </a>
                <a href="<?= htmlspecialchars($instagramUrl, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener" class="rounded-lg border border-[var(--line-soft)] bg-[var(--page)] p-4 transition hover:-translate-y-0.5 hover:border-[var(--accent)] hover:shadow-md">
                    <p class="text-xs font-bold uppercase tracking-[0.2em] text-[color:var(--muted-strong)]">Instagram</p>
                    <p class="mt-2 flex items-center gap-2 text-base font-bold min-[420px]:text-lg">
                        <svg class="h-5 w-5 text-[color:var(--accent)]" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <rect x="4" y="4" width="16" height="16" rx="5" stroke="currentColor" stroke-width="2" />
                            <circle cx="12" cy="12" r="3.5" stroke="currentColor" stroke-width="2" />
                            <path d="M17 7.2h.01" stroke="currentColor" stroke-width="3" stroke-linecap="round" />
                        </svg>
                        <?= htmlspecialchars($instagramHandle, ENT_QUOTES, 'UTF-8') ?>
                    </p>
                </a>
            </div>

            <div class="mt-5 rounded-lg bg-[var(--surface)] p-5 text-[color:var(--cream)]">
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-[color:var(--gold)]">Provozovna</p>
                <p class="mt-2 text-lg font-bold">Vackova 1064/39</p>
                <p class="text-sm text-[color:var(--cream-soft)]">612 00 Brno-Královo Pole</p>
                <a
                    href="https://www.google.com/maps/search/?api=1&query=Vackova%201064%2F39%2C%20612%2000%20Brno-Kr%C3%A1lovo%20Pole"
                    target="_blank"
                    rel="noopener"
                    class="mt-4 inline-flex rounded-lg border border-[var(--gold)] px-4 py-2 text-sm font-semibold text-[color:var(--gold-soft)] transition hover:bg-[var(--gold)] hover:text-[color:var(--surface)]"
                >
                    Otevřít v mapě
                </a>
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
                <a href="index.php" class="block hover:text-[color:var(--gold)]">Zpět na web</a>
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
});
</script>
</body>
</html>
