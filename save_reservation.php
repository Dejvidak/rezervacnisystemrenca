<?php

require __DIR__ . '/db.php';
require_once __DIR__ . '/integrations.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php#booking');
    exit;
}

$services = app_services();

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = app_normalize_phone((string) ($_POST['phone'] ?? ''));
$date = trim($_POST['date'] ?? '');
$time = trim($_POST['time'] ?? '');
$service = trim($_POST['service'] ?? '');
$note = trim($_POST['note'] ?? '');
$gdpr = isset($_POST['gdpr']);

$errors = [];

if (!app_verify_csrf_token($_POST['csrf_token'] ?? null)) {
    $errors[] = 'Formulář už vypršel nebo se nepodařilo ověřit odeslání. Otevři prosím rezervaci znovu.';
}

if (!app_validate_booking_honeypot($_POST['website'] ?? null)) {
    $errors[] = 'Žádost se nepodařilo ověřit. Zkus to prosím znovu.';
}

if (!app_validate_booking_form_timing($_POST['form_started_at'] ?? null)) {
    $errors[] = 'Formulář potřebujeme odeslat znovu. Vyber prosím termín ještě jednou.';
}

if ($name === '') {
    $errors[] = 'Doplň prosím jméno, ať víme, pro koho termín držíme.';
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'E-mail nevypadá správně. Mrkni prosím, jestli v něm nechybí znak @ nebo koncovka.';
}

if (!app_phone_is_valid($phone)) {
    $errors[] = 'Telefon nevypadá jako platné číslo. Může být třeba +420 777 123 456.';
}

$dateObject = DateTime::createFromFormat('!Y-m-d', $date);
if (!$dateObject || $dateObject->format('Y-m-d') !== $date) {
    $errors[] = 'Vyber prosím datum z nabídky.';
}

$duration = isset($services[$service]) ? (int) $services[$service]['duration'] : 30;
$availableTimes = app_time_slots_for_duration($date, $duration);
if (!in_array($time, $availableTimes, true)) {
    $errors[] = 'Vybraný čas už není dostupný. Zkus prosím jiný volný čas.';
}

if (!isset($services[$service])) {
    $errors[] = 'Vyber prosím službu, na kterou chceš přijít.';
}

if (!$gdpr) {
    $errors[] = 'Potřebujeme souhlas se zpracováním údajů, abychom mohli rezervaci vyřídit.';
}

if ($dateObject) {
    $today = new DateTime('today');
    $lastBookableDate = app_booking_last_date();
    $appointment = DateTime::createFromFormat('Y-m-d H:i', $date . ' ' . $time);

    if ($dateObject < $today) {
        $errors[] = 'Tohle datum už je za námi. Vyber prosím některý z dalších dnů.';
    } elseif ($dateObject > $lastBookableDate) {
        $errors[] = 'Online jde vybrat termín nejvýše 7 dní dopředu.';
    } elseif ($appointment && $appointment <= new DateTime()) {
        $errors[] = 'Tenhle čas už proběhl. Vyber prosím pozdější termín.';
    }
}

if (empty($errors)) {
    $stmt = $pdo->prepare('SELECT time, service, duration FROM reservations WHERE date = :date');
    $stmt->execute([':date' => $date]);
    $reservationsForDate = $stmt->fetchAll();

    if (app_reservations_overlap($reservationsForDate, $time, $duration)) {
        $errors[] = 'Tenhle termín je čerstvě obsazený. Vyber prosím jiný čas.';
    }
}

if (empty($errors)) {
    $googleCalendarCheck = app_google_calendar_overlaps($date, $time, $duration);

    if (!empty($googleCalendarCheck['errors'])) {
        $errors[] = 'Dostupnost termínu se teď nepodařilo ověřit. Zkus to prosím za chvíli znovu.';
    } elseif (!empty($googleCalendarCheck['overlaps'])) {
        $errors[] = 'Tenhle termín je už obsazený v kalendáři. Vyber prosím jiný čas.';
    }
}

if (empty($errors)) {
    $price = (int) $services[$service]['price'];

    try {
        $pdo->exec('BEGIN IMMEDIATE TRANSACTION');

        $lockingStmt = $pdo->prepare('SELECT time, service, duration FROM reservations WHERE date = :date');
        $lockingStmt->execute([':date' => $date]);
        $freshReservationsForDate = $lockingStmt->fetchAll();

        if (app_reservations_overlap($freshReservationsForDate, $time, $duration)) {
            $pdo->rollBack();
            $errors[] = 'Tenhle termín právě někdo obsadil. Vyber prosím jiný čas.';
        } else {
        $stmt = $pdo->prepare('
            INSERT INTO reservations (name, email, phone, date, time, service, price, duration, note, gdpr_accepted, status, created_at)
            VALUES (:name, :email, :phone, :date, :time, :service, :price, :duration, :note, :gdpr_accepted, :status, :created_at)
        ');

        $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':phone' => $phone,
            ':date' => $date,
            ':time' => $time,
            ':service' => $service,
            ':price' => $price,
            ':duration' => $duration,
            ':note' => $note === '' ? null : $note,
            ':gdpr_accepted' => 1,
            ':status' => 'pending',
            ':created_at' => (new DateTime())->format('Y-m-d H:i:s'),
        ]);

        $pdo->commit();

        $reservationId = (int) $pdo->lastInsertId();
        $reservation = [
            'id' => $reservationId,
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'date' => $date,
            'time' => $time,
            'service' => $service,
            'price' => $price,
            'duration' => $duration,
            'note' => $note,
        ];

        $integrationResult = app_run_reservation_request_integrations($reservation);
        $updateStmt = $pdo->prepare('
            UPDATE reservations
            SET owner_email_sent = :owner_email_sent,
                customer_email_sent = :customer_email_sent,
                calendar_event_id = :calendar_event_id,
                integration_errors = :integration_errors
            WHERE id = :id
        ');
        $updateStmt->execute([
            ':owner_email_sent' => $integrationResult['owner_email_sent'] ? 1 : 0,
            ':customer_email_sent' => $integrationResult['customer_email_sent'] ? 1 : 0,
            ':calendar_event_id' => $integrationResult['calendar_event_id'],
            ':integration_errors' => empty($integrationResult['errors'])
                ? null
                : implode("\n", $integrationResult['errors']),
            ':id' => $reservationId,
        ]);
        }
    } catch (PDOException $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        if ($e->getCode() === '23000') {
            $errors[] = 'Tenhle termín právě někdo obsadil. Vyber prosím jiný čas.';
        } else {
            $errors[] = 'Žádost se nepodařilo uložit. Zkus to prosím znovu, případně se ozvi telefonicky.';
        }
    }
}

function h(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

$displayDate = $dateObject ? $dateObject->format('d.m.Y') : $date;
$priceLabel = isset($services[$service]) ? app_price_label($service) : '';
$businessPhone = app_business_phone_display();
$businessEmail = app_business_email();
$businessAddress = app_business_full_address_inline();

unset($_SESSION['booking_form_started_at']);

?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<?= app_head_assets() ?>
    <title><?= empty($errors) ? 'Žádost o rezervaci přijata' : 'Rezervaci je potřeba upravit' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            --page: #0D0D0B;
            --cream: #F7F3EA;
            --cream-soft: #DCD3C2;
            --muted: #C8C1B4;
            --surface: #171613;
            --surface-soft: #1F1D19;
            --surface-dark: #080807;
            --line: #302D27;
            --line-soft: #5B554B;
            --accent: #C8AD63;
            --accent-dark: #A98A42;
            --gold: #D8BF7A;
            --gold-soft: #F0DFA9;
            --danger: #7B2D26;
            --danger-soft: #3A211E;
            --danger-text: #F4B8B0;
        }

        html,
        body {
            margin: 0;
            max-width: 100%;
            overflow-x: hidden;
        }

        .reservation-result {
            min-height: 100vh;
            background:
                radial-gradient(circle at top left, rgba(216, 191, 122, 0.12), transparent 34rem),
                linear-gradient(180deg, #141310 0%, var(--page) 46%, #080807 100%);
            color: var(--cream);
            font-family: Arial, Helvetica, sans-serif;
        }

        .reservation-result__main {
            min-height: 100vh;
            padding: 2rem 1rem;
        }

        .reservation-result__header,
        .reservation-result__card {
            width: min(100%, 54rem);
            margin-inline: auto;
        }

        .reservation-result__header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .reservation-result__brand {
            color: var(--cream);
            font-size: 1.25rem;
            font-weight: 900;
            letter-spacing: -0.02em;
            text-decoration: none;
        }

        .reservation-result__brand span:last-child {
            color: var(--gold);
        }

        .reservation-result__call {
            display: inline-flex;
            border: 1px solid var(--line);
            border-radius: 999px;
            padding: 0.55rem 1rem;
            color: var(--cream);
            font-size: 0.875rem;
            font-weight: 700;
            text-decoration: none;
            transition: border-color 220ms ease, color 220ms ease, transform 220ms ease;
        }

        .reservation-result__call:hover {
            transform: translateY(-1px);
            border-color: var(--accent);
            color: var(--gold-soft);
        }

        .reservation-result__card {
            overflow: hidden;
            border: 1px solid var(--line);
            border-radius: 1.5rem;
            background: var(--surface);
            box-shadow: 0 28px 70px rgba(0, 0, 0, 0.42);
        }

        .reservation-result__error-grid {
            display: grid;
        }

        .reservation-result__hero {
            background: linear-gradient(145deg, var(--surface-dark), #24221E);
            padding: 1.65rem 1.5rem;
            color: var(--cream);
        }

        .reservation-result__success-grid {
            display: grid;
            gap: 1.2rem;
            align-items: center;
        }

        .reservation-result__icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
            border-radius: 999px;
        }

        .reservation-result__icon svg {
            display: block;
        }

        .reservation-result__icon--success {
            width: 3.65rem;
            height: 3.65rem;
            background: var(--accent);
            color: var(--surface-dark);
            box-shadow: 0 18px 34px rgba(0, 0, 0, 0.3), 0 0 0 8px rgba(216, 191, 122, 0.16);
        }

        .reservation-result__icon--error {
            width: 3.5rem;
            height: 3.5rem;
            border: 1px solid var(--danger);
            background: var(--danger-soft);
            color: var(--danger-text);
        }

        .reservation-result__eyebrow {
            margin: 0;
            color: var(--gold);
            font-size: 0.68rem;
            font-weight: 900;
            letter-spacing: 0.22em;
            text-transform: uppercase;
        }

        .reservation-result__title {
            margin: 0.65rem 0 0;
            color: var(--cream);
            font-size: clamp(1.85rem, 4.4vw, 2.28rem);
            font-weight: 900;
            line-height: 1.08;
            max-width: 9.5em;
        }

        .reservation-result__copy {
            margin: 1rem 0 0;
            color: var(--cream-soft);
            font-size: 0.95rem;
            line-height: 1.7;
        }

        .reservation-result__side {
            display: grid;
            gap: 0.9rem;
        }

        .reservation-result__notice,
        .reservation-result__steps,
        .reservation-result__box,
        .reservation-result__note {
            border: 1px solid rgba(216, 191, 122, 0.34);
            border-radius: 1rem;
            background: linear-gradient(180deg, rgba(31, 29, 25, 0.96), rgba(18, 17, 15, 0.94));
            padding: 0.95rem;
            box-shadow:
                inset 0 1px 0 rgba(255, 255, 255, 0.045),
                inset 0 0 0 1px rgba(255, 255, 255, 0.025),
                0 14px 30px rgba(0, 0, 0, 0.18);
        }

        .reservation-result__notice {
            border-color: var(--gold);
            background: linear-gradient(145deg, rgba(216, 191, 122, 0.18), rgba(24, 22, 18, 0.88));
            box-shadow:
                inset 0 1px 0 rgba(255, 255, 255, 0.08),
                0 18px 34px rgba(0, 0, 0, 0.2);
        }

        .reservation-result__notice-row {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .reservation-result__notice-icon {
            display: inline-flex;
            flex: 0 0 auto;
            align-items: center;
            justify-content: center;
            width: 2.55rem;
            height: 2.55rem;
            border-radius: 999px;
            background: var(--gold);
            color: var(--surface-dark);
            box-shadow:
                inset 0 0 0 1px rgba(8, 8, 7, 0.12),
                0 0 0 5px rgba(216, 191, 122, 0.12);
        }

        .reservation-result__notice-icon svg {
            display: block;
            width: 1.28rem;
            height: 1.28rem;
        }

        .reservation-result__notice-title {
            margin: 0;
            color: var(--gold-soft);
            font-size: 0.68rem;
            font-weight: 900;
            letter-spacing: 0.2em;
            text-transform: uppercase;
        }

        .reservation-result__notice-text {
            margin: 0.25rem 0 0;
            color: var(--cream);
            font-size: 0.88rem;
            line-height: 1.55;
        }

        .reservation-result__steps-grid {
            display: grid;
            gap: 0.55rem;
            margin-top: 0.85rem;
            color: var(--cream-soft);
            font-size: 0.88rem;
        }

        .reservation-result__step {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
        }

        .reservation-result__step p,
        .reservation-result__note p {
            margin: 0;
        }

        .reservation-result__dot {
            flex: 0 0 auto;
            width: 0.5rem;
            height: 0.5rem;
            margin-top: 0.43rem;
            border-radius: 999px;
            background: var(--gold);
        }

        .reservation-result__summary {
            border-top: 1px solid var(--line);
            padding: 1.65rem 1.5rem;
        }

        .reservation-result__summary-grid {
            display: grid;
            gap: 0.7rem;
            margin-top: 0.85rem;
        }

        .reservation-result__box-label {
            margin: 0;
            color: var(--gold);
            font-size: 0.66rem;
            font-weight: 900;
            letter-spacing: 0.18em;
            text-transform: uppercase;
        }

        .reservation-result__box-value {
            margin: 0.28rem 0 0;
            color: var(--cream);
            font-weight: 800;
        }

        .reservation-result__box-muted,
        .reservation-result__note {
            color: var(--muted);
            font-size: 0.88rem;
            line-height: 1.5;
        }

        .reservation-result__box-muted {
            margin-top: 0.55rem;
        }

        .reservation-result__actions {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            margin-top: 1.5rem;
        }

        .reservation-result__button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.9rem;
            padding: 0.85rem 1.25rem;
            font-size: 0.9rem;
            font-weight: 800;
            text-decoration: none;
            transition: transform 220ms ease, border-color 220ms ease, background 220ms ease, color 220ms ease;
        }

        .reservation-result__button:hover {
            transform: translateY(-1px);
        }

        .reservation-result__button--primary {
            background: var(--accent);
            color: var(--surface-dark);
            box-shadow: 0 18px 34px rgba(0, 0, 0, 0.24);
        }

        .reservation-result__button--primary:hover {
            background: var(--gold);
        }

        .reservation-result__button--secondary {
            border: 1px solid var(--line-soft);
            color: var(--cream);
        }

        .reservation-result__button--secondary:hover {
            border-color: var(--accent);
            color: var(--gold-soft);
        }

        .reservation-result__errors {
            padding: 2rem 1.5rem;
        }

        .reservation-result__error-list {
            display: grid;
            gap: 0.5rem;
            margin: 1rem 0 0;
            padding: 0;
            list-style: none;
        }

        .reservation-result__error-item {
            border: 1px solid var(--line);
            border-radius: 1rem;
            background: var(--surface-soft);
            padding: 0.85rem 1rem;
            color: var(--cream-soft);
            font-size: 0.9rem;
        }

        .pending-confirmation-card {
            animation: pendingGlow 2.6s ease-in-out infinite;
        }

        .pending-confirmation-icon {
            animation: pendingIconPulse 1.8s ease-in-out infinite;
        }

        @keyframes pendingGlow {
            0%, 100% {
                box-shadow: 0 18px 34px rgba(0, 0, 0, 0.2), 0 0 0 0 rgba(216, 191, 122, 0.3);
                transform: translate3d(0, 0, 0);
            }

            50% {
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.26), 0 0 0 8px rgba(216, 191, 122, 0);
                transform: translate3d(0, -1px, 0);
            }
        }

        @keyframes pendingIconPulse {
            0%, 100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.08);
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .pending-confirmation-card,
            .pending-confirmation-icon {
                animation: none;
            }
        }

        @media (min-width: 640px) {
            .reservation-result__actions {
                flex-direction: row;
                flex-wrap: wrap;
            }

            .reservation-result__summary-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (min-width: 768px) {
            .reservation-result__main {
                padding-block: 3rem;
            }

            .reservation-result__hero,
            .reservation-result__summary,
            .reservation-result__errors {
                padding: 1.85rem 2rem;
            }

            .reservation-result__success-grid {
                grid-template-columns: 0.88fr 1.12fr;
                align-items: center;
            }

            .reservation-result__error-grid {
                grid-template-columns: 0.85fr 1.15fr;
            }
        }
    </style>
</head>
<body class="reservation-result min-h-screen bg-[#0D0D0B] text-[#F7F3EA]">
    <main class="reservation-result__main min-h-screen px-4 py-8 sm:py-12">
        <div class="reservation-result__header mx-auto mb-6 flex max-w-4xl items-center justify-between gap-4">
            <a href="index.php" class="reservation-result__brand text-xl font-extrabold tracking-tight transition hover:opacity-85">
                <span>Hair By</span>
                <span class="text-[#D8BF7A]">ReneNeme</span>
            </a>
            <a href="tel:+420608419610" class="reservation-result__call hidden rounded-full border border-[#302D27] px-4 py-2 text-sm font-semibold text-[#F7F3EA] transition hover:border-[#C8AD63] hover:text-[#F0DFA9] sm:inline-flex">
                Zavolat
            </a>
        </div>

        <div class="reservation-result__card mx-auto w-full max-w-4xl overflow-hidden rounded-3xl border border-[#302D27] bg-[#171613] shadow-2xl shadow-black/40">
        <?php if (!empty($errors)): ?>
            <div class="reservation-result__error-grid grid md:grid-cols-[0.85fr_1.15fr]">
                <div class="reservation-result__hero bg-[linear-gradient(145deg,#080807,#24221E)] px-6 py-8 text-[#F7F3EA] sm:px-8 md:py-10">
                    <div class="reservation-result__icon reservation-result__icon--error mb-5 inline-flex h-14 w-14 items-center justify-center rounded-full border border-[#7B2D26] bg-[#3A211E] text-[#F4B8B0]">
                        <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M12 8v5M12 17h.01" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"/>
                            <path d="M10.3 4.2h3.4L21 18.5 19.3 21H4.7L3 18.5 10.3 4.2Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <p class="reservation-result__eyebrow text-xs font-bold uppercase tracking-[0.24em] text-[#D8BF7A]">Ještě to doladíme</p>
                    <h1 class="reservation-result__title mt-3 text-3xl font-extrabold leading-tight sm:text-4xl">Rezervaci se nepodařilo odeslat</h1>
                    <p class="reservation-result__copy mt-4 text-sm leading-6 text-[#DCD3C2]">
                        Některý údaj nesedí nebo termín mezitím přestal být volný. Mrkni na přehled vedle a zkus to prosím znovu.
                    </p>
                </div>
                <div class="reservation-result__errors px-6 py-8 sm:px-8 md:py-10">
                    <p class="reservation-result__eyebrow text-xs font-bold uppercase tracking-[0.22em] text-[#D8BF7A]">Co je potřeba upravit</p>
                    <ul class="reservation-result__error-list mt-4 space-y-2 text-sm">
                        <?php foreach ($errors as $error): ?>
                            <li class="reservation-result__error-item rounded-2xl border border-[#302D27] bg-[#1F1D19] px-4 py-3 text-[#DCD3C2]"><?= h($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <div class="reservation-result__actions mt-6 flex flex-col gap-3 sm:flex-row">
                        <a href="index.php#booking" class="reservation-result__button reservation-result__button--primary inline-flex justify-center rounded-xl bg-[#C8AD63] px-5 py-3 text-sm font-semibold text-[#080807] shadow-md shadow-black/30 transition hover:bg-[#D8BF7A]">
                            Upravit rezervaci
                        </a>
                        <a href="tel:+420608419610" class="reservation-result__button reservation-result__button--secondary inline-flex justify-center rounded-xl border border-[#5B554B] px-5 py-3 text-sm font-semibold text-[#F7F3EA] transition hover:border-[#C8AD63] hover:text-[#F0DFA9]">
                            Zavolat
                        </a>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div>
                <div class="reservation-result__hero bg-[linear-gradient(145deg,#080807,#24221E)] px-6 py-8 text-[#F7F3EA] sm:px-8 md:py-10">
                    <div class="reservation-result__success-grid grid gap-6 md:grid-cols-[0.9fr_1.1fr] md:items-start">
                        <div>
                            <div class="reservation-result__icon reservation-result__icon--success mb-5 inline-flex h-16 w-16 items-center justify-center rounded-full bg-[#C8AD63] text-[#080807] shadow-lg shadow-black/30 ring-8 ring-[rgba(216,191,122,0.16)]">
                                <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M5 12.5l4.2 4.2L19 7" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </div>
                            <p class="reservation-result__eyebrow text-xs font-bold uppercase tracking-[0.24em] text-[#D8BF7A]">Žádost přijata</p>
                            <h1 class="reservation-result__title mt-3 text-3xl font-extrabold leading-tight sm:text-4xl">Díky, <?= h($name) ?>. Termín držíme v přehledu.</h1>
                        </div>
                        <div class="reservation-result__side space-y-4">
                            <div class="reservation-result__notice pending-confirmation-card rounded-2xl border border-[#D8BF7A] bg-[rgba(216,191,122,0.14)] p-4 shadow-lg shadow-black/20">
                                <div class="reservation-result__notice-row flex items-start gap-3">
                                    <span class="reservation-result__notice-icon pending-confirmation-icon mt-0.5 inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-[#D8BF7A] text-[#080807]">
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path d="M12 7v5l3 2" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" stroke="currentColor" stroke-width="2.4"/>
                                        </svg>
                                    </span>
                                    <div>
                                        <p class="reservation-result__notice-title text-xs font-extrabold uppercase tracking-[0.22em] text-[#F0DFA9]">Čeká na potvrzení</p>
                                        <p class="reservation-result__notice-text mt-1 text-sm leading-6 text-[#F7F3EA]">
                                            Termín zatím není finálně potvrzený. Jakmile ho přijmeme, dorazí ti e-mail se shrnutím.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="reservation-result__steps rounded-2xl border border-[rgba(216,191,122,0.22)] bg-[rgba(255,255,255,0.05)] p-4">
                                <p class="reservation-result__eyebrow text-xs font-bold uppercase tracking-[0.2em] text-[#F0DFA9]">Co bude dál</p>
                                <div class="reservation-result__steps-grid mt-4 grid gap-3 text-sm text-[#DCD3C2] sm:grid-cols-3 md:grid-cols-1">
                                    <div class="reservation-result__step flex gap-3">
                                        <span class="reservation-result__dot mt-1 h-2.5 w-2.5 shrink-0 rounded-full bg-[#D8BF7A]"></span>
                                        <p>Rezervaci zkontrolujeme v administraci.</p>
                                    </div>
                                    <div class="reservation-result__step flex gap-3">
                                        <span class="reservation-result__dot mt-1 h-2.5 w-2.5 shrink-0 rounded-full bg-[#D8BF7A]"></span>
                                        <p>Po potvrzení ti přijde e-mail se všemi údaji.</p>
                                    </div>
                                    <div class="reservation-result__step flex gap-3">
                                        <span class="reservation-result__dot mt-1 h-2.5 w-2.5 shrink-0 rounded-full bg-[#D8BF7A]"></span>
                                        <p>Kdyby bylo potřeba něco doladit, ozveme se.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="reservation-result__summary border-t border-[#302D27] px-6 py-8 sm:px-8 md:py-10">
                    <p class="reservation-result__eyebrow text-xs font-bold uppercase tracking-[0.22em] text-[#D8BF7A]">Shrnutí rezervace</p>
                    <div class="reservation-result__summary-grid mt-4 grid gap-3 sm:grid-cols-2">
                        <div class="reservation-result__box rounded-2xl border border-[#302D27] bg-[#1F1D19] p-4">
                            <p class="reservation-result__box-label text-xs font-bold uppercase tracking-[0.18em] text-[#D8BF7A]">Služba</p>
                            <p class="reservation-result__box-value mt-1 font-bold"><?= h($service) ?></p>
                            <?php if ($priceLabel !== ''): ?>
                                <p class="reservation-result__box-muted mt-1 text-sm text-[#C8C1B4]"><?= h($priceLabel) ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="reservation-result__box rounded-2xl border border-[#302D27] bg-[#1F1D19] p-4">
                            <p class="reservation-result__box-label text-xs font-bold uppercase tracking-[0.18em] text-[#D8BF7A]">Termín</p>
                            <p class="reservation-result__box-value mt-1 font-bold"><?= h($displayDate) ?> v <?= h($time) ?></p>
                            <p class="reservation-result__box-muted mt-1 text-sm text-[#C8C1B4]"><?= (int) $duration ?> minut</p>
                        </div>
                        <div class="reservation-result__box rounded-2xl border border-[#302D27] bg-[#1F1D19] p-4">
                            <p class="reservation-result__box-label text-xs font-bold uppercase tracking-[0.18em] text-[#D8BF7A]">Kontakt</p>
                            <p class="reservation-result__box-value mt-1 font-bold"><?= h($phone) ?></p>
                            <p class="reservation-result__box-muted mt-1 break-words text-sm text-[#C8C1B4]"><?= h($email) ?></p>
                        </div>
                        <div class="reservation-result__box rounded-2xl border border-[#302D27] bg-[#1F1D19] p-4">
                            <p class="reservation-result__box-label text-xs font-bold uppercase tracking-[0.18em] text-[#D8BF7A]">Místo</p>
                            <p class="reservation-result__box-value mt-1 font-bold">Hair By ReneNeme</p>
                            <p class="reservation-result__box-muted mt-1 text-sm text-[#C8C1B4]"><?= h($businessAddress) ?></p>
                        </div>
                    </div>
                    <?php if ($note !== ''): ?>
                        <div class="reservation-result__box mt-3 rounded-2xl border border-[#302D27] bg-[#1F1D19] p-4">
                            <p class="reservation-result__box-label text-xs font-bold uppercase tracking-[0.18em] text-[#D8BF7A]">Přání ke střihu</p>
                            <p class="reservation-result__box-muted mt-1 text-sm leading-6 text-[#C8C1B4]"><?= nl2br(h($note)) ?></p>
                        </div>
                    <?php endif; ?>
                    <div class="reservation-result__note mt-5 rounded-2xl border border-[#302D27] bg-[#1F1D19] p-4 text-sm leading-6 text-[#C8C1B4]">
                        <p class="reservation-result__box-value font-semibold text-[#F7F3EA]">Potřebuješ něco změnit?</p>
                        <p>Zavolej na <?= h($businessPhone) ?> nebo napiš na <?= h($businessEmail) ?>.</p>
                    </div>
                    <div class="reservation-result__actions mt-6 flex flex-col gap-3 sm:flex-row">
                        <a href="index.php" class="reservation-result__button reservation-result__button--primary inline-flex justify-center rounded-xl bg-[#C8AD63] px-5 py-3 text-sm font-semibold text-[#080807] shadow-md shadow-black/30 transition hover:bg-[#D8BF7A]">
                            Zpět na web
                        </a>
                        <a href="index.php#booking" class="reservation-result__button reservation-result__button--secondary inline-flex justify-center rounded-xl border border-[#5B554B] px-5 py-3 text-sm font-semibold text-[#F7F3EA] transition hover:border-[#C8AD63] hover:text-[#F0DFA9]">
                            Vytvořit další rezervaci
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        </div>
    </main>
</body>
</html>
