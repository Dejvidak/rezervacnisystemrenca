<?php
require_once __DIR__ . '/config.php';

$services = app_services();
?>
<!DOCTYPE html>
<html lang="cs" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <title>BarberShop Skluzavková kotleta - Pánské kadeřnictví & online rezervace</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#F5EDE1]">

<!-- NAV / HEADER -->
<header class="sticky top-0 z-50 bg-[#3F332A]/90 border-b border-[#5E4E41] shadow-xl">
    <div class="max-w-6xl mx-auto flex items-center justify-between px-4 py-3">
        <a href="#top" class="font-extrabold tracking-tight text-xl">
            <span class="text-[#F5EDE1]">Hair By</span>
            <span class="text-[#D8C8B0]">ReneNeme</span>
        </a>
        <nav class="flex gap-3 md:gap-6 text-sm text-[#EDE8DD]">
            <a href="#about" class="hover:text-[#C99A5B] transition">O nás</a>
            <a href="#services" class="hover:text-[#C99A5B] transition">Služby</a>
            <a href="#pricing" class="hover:text-[#C99A5B] transition">Ceník</a>
            <a href="#booking" class="hover:text-[#C99A5B] transition font-semibold">Rezervace</a>
        </nav>
    </div>
</header>

<main id="top" class="max-w-6xl mx-auto px-4 pb-16">

    <!-- HERO sekce-->
    <section class="grid md:grid-cols-2 gap-10 items-center py-10 md:py-16">
        <div>
            <p class="text-xs uppercase tracking-[0.4em] text-stone-800 mb-3 font-bold">Pánské kadeřnictví · Brno</p>
            <h1 class="text-4xl md:text-5xl font-extrabold leading-tight mb-4">
                Moderní <span class="text-[#8C7560]">barber shop</span>,  
                kvalitní střihy, přátelská atmosféra.
            </h1>
            <p class="text-sm md:text-base mb-6 text-[#5E4E41]">
                Online rezervace během pár vteřin. Vyber si termín, službu a přijď si užít svůj čas v křesle.
                Žádné telefonování, žádné čekání ve frontě.
            </p>
            <div class="flex flex-wrap gap-3">
                <a href="#booking"
                   class="inline-flex items-center justify-center rounded-full bg-[#2E7D5A] px-5 py-2.5 text-sm font-semibold text-[#F5EDE1] hover:bg-[#245F44] transition shadow-xl">
                    Rezervovat termín
                </a>
                <a href="#pricing"
                   class="inline-flex items-center justify-center rounded-full border border-[#3F332A] px-5 py-2.5 text-sm font-semibold text-[#3F332A] hover:bg-[#3F332A] hover:text-[#F5EDE1] transition shadow-xl">
                    Podívat se na ceník
                </a>
            </div>
            <p class="mt-8 text-sm text-[#5E4E41]">
                Otevřeno Po-Pá 9:00-19:00 · So 9:00-14:00 Vackova 1064/39, 612 00 Brno-Královo Pole
            </p>
        </div>
        <div>
            <div class="h-64 rounded-3xl bg-[#6B5947] border border-black flex items-center justify-center">
                <div class="text-center px-8">
                    <p class="text-sm uppercase tracking-[0.2em] text-[#F0E7DB] mb-2">Online rezervace</p>
                    <p class="text-2xl font-bold mb-2 text-[#F5EDE1]">Stačí pár kliknutí</p>
                    <p class="text-sm text-[#F0E7DB]">
                        Vyber si službu, termín a čas. <br>Rezervaci hned ukládáme, aby se na tebe nezapomnělo.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- O NÁS -->
    <section id="about" class="py-10 border-t border-[#3F332A] scroll-mt-32">
        <h2 class="text-3xl font-bold mb-4">O nás</h2>
        <p class="text-m text-[#5E4E41] mb-4 max-w-3xl">
            <strong>Hair By ReneNeme</strong> je malé pánské kadeřnictví, které kombinuje <strong>klasické střihy</strong> a <strong>moderní techniky</strong>.
            Zakládáme si na individuálním přístupu, příjemné atmosféře a tom, abys odcházel s účesem,
            se kterým se budeš cítit dobře.
        </p>
        <div class="grid md:grid-cols-3 gap-4 text-sm">
            <div class="bg-[#6B5947] rounded-xl border border-[#3F332A] p-4 text-[#F5EDE1] shadow-xl">
                <p class="font-semibold mb-1">Přátelský přístup</p>
                <p>Žádné strojené prostředí. Prostě si sedneš, dáš řeč a my se postaráme o zbytek.</p>
            </div>
            <div class="bg-[#6B5947] rounded-xl border border-[#3F332A] p-4 text-[#F5EDE1] shadow-xl">
                <p class="font-semibold mb-1">Moderní střihy</p>
                <p>Skin fade, úprava vousů, styling - vše, co od barbershopu čekáš.</p>
            </div>
            <div class="bg-[#6B5947] rounded-xl border border-[#3F332A] p-4 text-[#F5EDE1] shadow-xl">
                <p class="font-semibold mb-1">Online rezervace</p>
                <p>Vyber si termín, který ti sedí. Rezervace je hotová během pár vteřin.</p>
            </div>
        </div>
    </section>

    <!-- SLUŽBY -->
    <section id="services" class="py-10 border-t border-[#3F332A] scroll-mt-32">
        <h2 class="text-2xl font-bold mb-4">Služby</h2>
        <div class="grid md:grid-cols-2 gap-4 text-sm">
            <div class="bg-[#6B5947] border border-[#3F332A] rounded-xl p-4 text-[#F5EDE1] shadow-xl">
                <p class="font-semibold mb-1">Pánský střih</p>
                <p class="mb-1 text-[#F0E7DB]">Pánský střih na sucho bez mytí a vousů. (35 min)</p>
                
            </div>
            <div class="bg-[#6B5947] border border-[#3F332A] rounded-xl p-4 text-[#F5EDE1] shadow-xl">
                <p class="font-semibold mb-1">Kompletka 1 (zahrnuje střih a mytí)</p>
                <p class="mb-1 text-[#F0E7DB]">Pánský střih s mytím či úpravou vousů. (45 min)</p>
                
            </div>
            <div class="bg-[#6B5947] border border-[#3F332A] rounded-xl p-4 text-[#F5EDE1] shadow-xl">
                <p class="font-semibold mb-1">Kompletka 2</p>
                <p class="mb-1 text-[#F0E7DB]">50 minut · střih vlasů, úprava vousů, styling. (50 min)</p>
                
            </div>
            <div class="bg-[#6B5947] border border-[#3F332A] rounded-xl p-4 text-[#F5EDE1] shadow-xl">
                <p class="font-semibold mb-1">Dětský střih</p>
                <p class="mb-1 text-[#F0E7DB]">Chlapecký i holčičí střih Cena se liší dle náročnosti účesu u chlapců například boky úplně vyholené do kůže už by byly za 420,- (30 min)</p>
                
            </div>
        </div>
    </section>
<div>
    <!-- CENÍK -->
    <section id="pricing" class="py-10 border-t border-[#3F332A] scroll-mt-32">
        <h2 class="text-2xl font-bold mb-4">Ceník</h2>
        <div class="max-w text-m">
            <?php foreach ($services as $serviceName => $service): ?>
                <div class="flex justify-between gap-4 border-b border-[#3F332A] py-2">
                    <span><?= htmlspecialchars($serviceName, ENT_QUOTES, 'UTF-8') ?></span>
                    <span class="font-bold text-black whitespace-nowrap"><?= htmlspecialchars($service['price_label'], ENT_QUOTES, 'UTF-8') ?></span>
                </div>
            <?php endforeach; ?>
        </div>
        
    </section>

    <!-- REZERVAČNÍ FORMULÁŘ -->
    <section id="booking" class="py-8 mt-4 border-t border-[#3F332A] scroll-mt-28">
        <div class="grid md:grid-cols-2 gap-8 items-start mb-10">
            <div>
                <h2 class="text-2xl font-bold mb-3">Online rezervace</h2>
                <p class="text-sm text-[#5E4E41] ">
                    Vyplň formulář a zarezervuj si svůj termín. Po odeslání se ti zobrazí shrnutí rezervace
                    a všechna data jsou uložena do rezervační databáze.
                </p>

            </div>
</div>

            <div>
                <form action="save_reservation.php" method="POST" class="space-y-4 bg-[#6B5947] rounded-2xl p-5 text-[#F5EDE1]">
                    <!-- Jméno -->
                    <div>
                        <label for="name" class="block text-sm font-medium mb-1">Jméno a příjmení *</label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            required
                            class="w-full rounded-lg bg-[#F5EDE1] border border-[#8C7560] px-3 py-2 text-sm text-[#231814] focus:outline-none focus:ring-2 focus:ring-[#2E7D5A]"
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
                            class="w-full rounded-lg bg-[#F5EDE1] border border-[#8C7560] px-3 py-2 text-sm text-[#231814] focus:outline-none focus:ring-2 focus:ring-[#2E7D5A]"
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
                            class="w-full rounded-lg bg-[#F5EDE1] border border-[#8C7560] px-3 py-2 text-sm text-[#231814] focus:outline-none focus:ring-2 focus:ring-[#2E7D5A]"
                            placeholder="např. +420 777 123 456"
                        >
                    </div>

                    <!-- Datum + čas -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label for="date" class="block text-sm font-medium mb-1">Datum *</label>
                            <input
                                type="date"
                                id="date"
                                name="date"
                                required
                                class="w-full rounded-lg bg-[#F5EDE1] border border-[#8C7560] px-3 py-2 text-sm text-[#231814] focus:outline-none focus:ring-2 focus:ring-[#2E7D5A]"
                            >
                        </div>
                        <div>
                            <label for="time" class="block text-sm font-medium mb-1">Čas *</label>
                            <select
                                     id="time"
                                     name="time"
                                     required
                                     class="w-full rounded-lg bg-[#F5EDE1] border border-[#8C7560] px-3 py-2 text-sm text-[#231814] focus:outline-none focus:ring-2 focus:ring-[#2E7D5A]"
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
                            class="w-full rounded-lg bg-[#F5EDE1] border border-[#8C7560] px-3 py-2 text-sm text-[#231814] focus:outline-none focus:ring-2 focus:ring-[#2E7D5A]"
                        >
                            <option value="">Vyber službu...</option>
                            <?php foreach ($services as $serviceName => $service): ?>
                                <option value="<?= htmlspecialchars($serviceName, ENT_QUOTES, 'UTF-8') ?>">
                                    <?= htmlspecialchars($serviceName, ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <p id="priceInfo" class="mt-1 text-xs text-[#F0E7DB] hidden">
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
                            class="w-full rounded-lg bg-[#F5EDE1] border border-[#8C7560] px-3 py-2 text-sm text-[#231814] focus:outline-none focus:ring-2 focus:ring-[#2E7D5A]"
                            placeholder="Např. delší vlasy, speciální přání..."
                        ></textarea>
                    </div>

                    <!-- Souhlas -->
                    <div class="flex gap-2 text-xs text-[#F0E7DB]">
                        <input
                            type="checkbox"
                            id="gdpr"
                            name="gdpr"
                            required
                            class="mt-1 rounded border-[#8C7560] bg-[#F5EDE1]"
                        >
                        <label for="gdpr">
                            Souhlasím se zpracováním osobních údajů pro účely rezervace.
                        </label>
                    </div>

                    <!-- Chyba z JS -->
                    <p id="errorMsg" class="text-xs text-red-300 hidden"></p>

                    <!-- Tlačítko -->
                    <button
                        type="submit"
                        class="w-full rounded-full bg-[#2E7D5A] py-2.5 text-sm font-semibold hover:bg-[#245F44] transition"
                    >
                        Odeslat rezervaci
                    </button>
                </form>
            </div>
        </div>
    </section>
</main>

<!-- FOOTER -->
<footer class="border-t border-[#3F332A] py-4 text-center text-xs text-[#5E4E41]">
    BarberShop Skluzavková kotleta · Ukázkový projekt pro kurz web development · <?php echo date('Y'); ?>
</footer>


<!-- JavaScript – datum/čas + cena -->
<script>
    const serviceSelect = document.getElementById('service');
    const dateInput = document.getElementById('date');
    const timeInput = document.getElementById('time');
    const priceInfo = document.getElementById('priceInfo');
    const priceValue = document.getElementById('priceValue');
    const errorMsg = document.getElementById('errorMsg');
    const services = <?= json_encode($services, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;

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
        errorMsg.classList.add('hidden');
        errorMsg.textContent = '';

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
    const dateInput = document.getElementById('date');
    const timeSelect = document.getElementById('time');
    const serviceSelect = document.getElementById('service');

    if (!dateInput || !timeSelect || !serviceSelect) return;

    async function loadAvailableTimes() {
        const date = dateInput.value;

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
            placeholder.selected = true;
            timeSelect.appendChild(placeholder);

            freeTimes.forEach(time => {
                const opt = document.createElement('option');
                opt.value = time;
                opt.textContent = time;
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
