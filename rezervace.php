<?php
require_once __DIR__ . '/config.php';

$services = app_services();
$businessAddress = app_business_full_address_inline();
$businessPhone = app_business_phone_display();
$businessPhoneHref = 'tel:' . preg_replace('/\s+/', '', app_business_phone());
$businessOpeningHoursLabel = 'Po-Pá 9:00-18:00';
$requestedService = trim((string) ($_GET['service'] ?? ''));
$initialService = isset($services[$requestedService]) ? $requestedService : '';
$pageTitle = 'Rezervace termínu - ' . app_business_name();
$pageDescription = 'Online rezervace termínu v pánském kadeřnictví Hair By ReneNeme v Brně.';
?>
<!DOCTYPE html>
<html lang="cs" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<?= app_head_assets() ?>
    <meta name="description" content="<?= htmlspecialchars($pageDescription, ENT_QUOTES, 'UTF-8') ?>">
    <style>
        :root {
            --page: #0D0D0B;
            --ink: #F7F3EA;
            --surface: #171613;
            --surface-soft: #24221E;
            --muted: #C8C1B4;
            --line: #302D27;
            --cream: #F7F3EA;
            --cream-soft: #DCD3C2;
            --field: #171613;
            --field-border: #5B554B;
            --field-text: #F7F3EA;
            --accent: #C8AD63;
            --accent-dark: #A98A42;
            --gold: #D8BF7A;
            --gold-soft: #F0DFA9;
        }

        html,
        body {
            margin: 0;
            max-width: 100%;
            overflow-x: hidden;
        }

        * {
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            background:
                linear-gradient(90deg, rgba(13, 13, 11, 0.98), rgba(13, 13, 11, 0.86)),
                url("assets/homepage-hero-wide.png") center / cover fixed,
                radial-gradient(circle at top left, rgba(216, 191, 122, 0.12), transparent 34rem),
                linear-gradient(180deg, #141310 0%, var(--page) 48%, #080807 100%);
            color: var(--cream);
            font-family: Arial, Helvetica, sans-serif;
        }

        body::before {
            position: fixed;
            inset: 0;
            z-index: -1;
            background:
                radial-gradient(circle at 78% 18%, rgba(216, 191, 122, 0.16), transparent 24rem),
                radial-gradient(circle at 12% 88%, rgba(169, 138, 66, 0.14), transparent 28rem);
            content: "";
            pointer-events: none;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        button,
        input,
        select,
        textarea {
            font: inherit;
        }

        .hidden {
            display: none !important;
        }

        .inline-flex {
            display: inline-flex !important;
        }

        .overflow-hidden {
            overflow: hidden !important;
        }

        .booking-shell {
            display: flex;
            align-items: center;
            min-height: 100vh;
            padding: 1rem;
        }

        .booking-layout {
            display: grid;
            width: min(100%, 72rem);
            margin: 0 auto;
            gap: 1.25rem;
            animation: bookingPageIn 520ms cubic-bezier(0.16, 1, 0.3, 1);
        }

        .booking-card {
            display: grid;
            gap: 1.25rem;
            border: 1px solid var(--line);
            border-radius: 1.25rem;
            background: linear-gradient(180deg, rgba(31, 29, 25, 0.96), rgba(14, 13, 11, 0.96));
            box-shadow: 0 30px 70px rgba(0, 0, 0, 0.38);
            padding: 1rem;
        }

        .booking-aside {
            border: 1px solid rgba(216, 191, 122, 0.18);
            border-radius: 1.25rem;
            background: linear-gradient(145deg, rgba(8, 8, 7, 0.96), rgba(36, 34, 30, 0.94));
            padding: 1.25rem;
            box-shadow: 0 26px 60px rgba(0, 0, 0, 0.34);
        }

        .booking-back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            color: var(--gold-soft);
            font-size: 0.9rem;
            font-weight: 800;
            transition: color 180ms ease, transform 180ms ease;
        }

        .booking-back-link:hover {
            color: var(--cream);
            transform: translateX(-2px);
        }

        .booking-main-title {
            margin: 1.65rem 0 0;
            font-size: clamp(2rem, 7vw, 3.6rem);
            font-weight: 950;
            line-height: 0.98;
            letter-spacing: 0;
        }

        .booking-aside-copy {
            max-width: 30rem;
            margin: 1.1rem 0 0;
            color: var(--cream-soft);
            font-size: 0.98rem;
            font-weight: 600;
            line-height: 1.7;
        }

        .booking-info-box {
            display: grid;
            gap: 0.8rem;
            margin-top: 1.5rem;
            border: 1px solid rgba(216, 191, 122, 0.16);
            border-radius: 1rem;
            background: rgba(255, 255, 255, 0.045);
            padding: 1rem;
            color: var(--cream-soft);
            font-size: 0.9rem;
            line-height: 1.5;
        }

        .booking-info-box p {
            margin: 0;
        }

        .booking-info-box strong {
            color: var(--cream);
        }

        .booking-info-box a {
            color: var(--gold-soft);
            font-weight: 800;
        }

        .booking-aside > .booking-eyebrow {
            margin-top: 1.75rem;
        }

        .booking-form-header {
            display: grid;
            gap: 1rem;
            border-bottom: 1px solid rgba(216, 191, 122, 0.14);
            padding-bottom: 1rem;
        }

        .booking-form-title {
            margin: 0.35rem 0 0;
            color: var(--cream);
            font-size: clamp(1.35rem, 4vw, 2rem);
            font-weight: 950;
            line-height: 1.15;
        }

        .booking-stepper {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 0.45rem;
        }

        .booking-stepper__item {
            min-width: 0;
            border: 1px solid rgba(216, 191, 122, 0.16);
            border-radius: 0.75rem;
            background: rgba(255, 255, 255, 0.04);
            padding: 0.55rem;
            color: var(--muted);
            text-align: left;
            transition: border-color 220ms ease, background 220ms ease, color 220ms ease, transform 220ms ease;
        }

        .booking-stepper__item.is-active {
            border-color: rgba(216, 191, 122, 0.72);
            background: rgba(216, 191, 122, 0.13);
            color: var(--cream);
            transform: translateY(-1px);
        }

        .booking-stepper__item.is-complete {
            border-color: rgba(216, 191, 122, 0.36);
            color: var(--gold-soft);
        }

        .booking-stepper__number {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 1.35rem;
            height: 1.35rem;
            border-radius: 999px;
            background: rgba(216, 191, 122, 0.14);
            color: var(--gold-soft);
            font-size: 0.72rem;
            font-weight: 900;
        }

        .booking-stepper__label {
            display: block;
            margin-top: 0.35rem;
            overflow: hidden;
            font-size: 0.72rem;
            font-weight: 800;
            line-height: 1.15;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .booking-step {
            display: none;
            opacity: 0;
            transform: translate3d(0, 10px, 0);
        }

        .booking-step.is-active {
            display: block;
            opacity: 1;
            transform: translate3d(0, 0, 0);
            animation: bookingStepIn 280ms cubic-bezier(0.16, 1, 0.3, 1);
        }

        .booking-eyebrow {
            color: var(--gold-soft);
            font-size: 0.7rem;
            font-weight: 900;
            letter-spacing: 0.22em;
            text-transform: uppercase;
        }

        .booking-title {
            margin: 0.35rem 0 0;
            color: var(--cream);
            font-size: 1.35rem;
            font-weight: 900;
            line-height: 1.18;
        }

        .booking-copy {
            margin: 0.45rem 0 0;
            color: var(--cream-soft);
            font-size: 0.9rem;
            line-height: 1.6;
        }

        .booking-step {
            min-height: 18rem;
        }

        .booking-field {
            margin-top: 1rem;
        }

        .booking-field label,
        .booking-consent label {
            display: block;
            margin-bottom: 0.4rem;
            font-size: 0.9rem;
            font-weight: 800;
        }

        .booking-field-grid {
            display: grid;
            gap: 1rem;
            margin-top: 1rem;
        }

        .booking-control,
        .booking-input,
        .booking-textarea {
            box-sizing: border-box;
            display: block;
            width: 100%;
            max-width: 100%;
            min-width: 0;
            border: 1px solid var(--field-border);
            border-radius: 0.65rem;
            background-color: var(--field);
            color: var(--field-text);
            font-size: 1rem;
            transition: border-color 180ms ease, box-shadow 180ms ease, background-color 180ms ease;
        }

        .booking-control,
        .booking-input {
            height: 3rem;
            padding: 0 0.9rem;
        }

        .booking-textarea {
            min-height: 6rem;
            padding: 0.8rem 0.9rem;
            resize: vertical;
        }

        .booking-control:focus,
        .booking-input:focus,
        .booking-textarea:focus {
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

        .booking-control--select {
            appearance: none;
            -webkit-appearance: none;
            padding-right: 2.35rem;
            background-image:
                linear-gradient(45deg, transparent 50%, var(--gold-soft) 50%),
                linear-gradient(135deg, var(--gold-soft) 50%, transparent 50%);
            background-position:
                calc(100% - 1rem) 50%,
                calc(100% - 0.68rem) 50%;
            background-repeat: no-repeat;
            background-size: 0.32rem 0.32rem, 0.32rem 0.32rem;
        }

        .booking-date-shell {
            position: relative;
        }

        .booking-date-shell input[type="date"] {
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
            pointer-events: none;
        }

        .booking-date-shell input[type="date"]::-webkit-calendar-picker-indicator {
            opacity: 0.7;
            padding: 0.35rem;
            cursor: pointer;
            filter: invert(0.92) sepia(0.2) saturate(1.3);
        }

        .selected-service-card,
        .booking-summary-card {
            border: 1px solid rgba(216, 191, 122, 0.16);
            border-radius: 0.9rem;
            background: rgba(255, 255, 255, 0.045);
            padding: 0.9rem;
        }

        .selected-service-card {
            display: none;
            gap: 1rem;
            margin-top: 0.85rem;
        }

        .selected-service-card.is-visible {
            display: grid;
        }

        .selected-service-card #selectedServicePrice {
            color: var(--gold-soft);
        }

        .booking-summary-grid {
            display: grid;
            gap: 0.7rem;
            margin-top: 1rem;
        }

        .booking-summary-card span {
            display: block;
            color: var(--gold-soft);
            font-size: 0.68rem;
            font-weight: 900;
            letter-spacing: 0.18em;
            text-transform: uppercase;
        }

        .booking-summary-card strong {
            display: block;
            margin-top: 0.35rem;
            color: var(--cream);
            font-size: 0.98rem;
            line-height: 1.35;
            overflow-wrap: anywhere;
        }

        .booking-step-actions {
            display: flex;
            flex-direction: column-reverse;
            gap: 0.75rem;
            border-top: 1px solid rgba(216, 191, 122, 0.14);
            padding-top: 1rem;
        }

        .booking-error {
            margin: 0;
            color: #F4B8B0;
            font-size: 0.82rem;
            font-weight: 700;
        }

        .booking-loading-inline {
            margin: 0;
            border: 1px solid var(--surface-soft);
            border-radius: 0.75rem;
            background: rgba(255, 255, 255, 0.06);
            padding: 0.75rem 0.9rem;
            color: var(--cream-soft);
            font-size: 0.8rem;
            font-weight: 700;
        }

        .booking-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            min-height: 2.95rem;
            border-radius: 0.75rem;
            padding: 0.75rem 1rem;
            font-size: 0.9rem;
            font-weight: 800;
            transition: transform 220ms ease, border-color 220ms ease, background 220ms ease, color 220ms ease, opacity 220ms ease;
        }

        .booking-button:hover {
            transform: translateY(-1px);
        }

        .booking-button--ghost {
            border: 1px solid rgba(216, 191, 122, 0.22);
            color: var(--cream);
            background: rgba(255, 255, 255, 0.04);
        }

        .booking-button--ghost:hover {
            border-color: rgba(216, 191, 122, 0.46);
            color: var(--gold-soft);
            background: rgba(216, 191, 122, 0.08);
        }

        .booking-button--primary {
            border: 1px solid rgba(216, 191, 122, 0.56);
            background: linear-gradient(180deg, var(--accent), var(--accent-dark));
            color: #080807;
            box-shadow: 0 14px 26px rgba(200, 173, 99, 0.18);
        }

        .booking-button:disabled {
            cursor: wait;
            opacity: 0.8;
        }

        .booking-submit__spinner {
            display: inline-block;
            width: 1rem;
            height: 1rem;
            border: 2px solid rgba(8, 8, 7, 0.42);
            border-top-color: #080807;
            border-radius: 999px;
        }

        .booking-consent {
            display: flex;
            gap: 0.65rem;
            margin-top: 1rem;
            color: var(--cream-soft);
            font-size: 0.8rem;
            line-height: 1.5;
        }

        .booking-consent input {
            flex: 0 0 auto;
            margin-top: 0.2rem;
            accent-color: var(--accent);
        }

        .booking-consent label {
            margin: 0;
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

        .booking-submit__spinner {
            animation: spin 780ms linear infinite;
        }

        .booking-loading-progress {
            width: 100%;
            height: 100%;
            border-radius: 999px;
            background: var(--accent);
            animation: bookingProgress 2100ms cubic-bezier(0.2, 0.8, 0.2, 1) infinite;
            transform-origin: left center;
        }

        .booking-loading-overlay.is-slow .booking-loading-fallback {
            display: block;
        }

        .booking-loading-card {
            width: min(100%, 26rem);
            max-height: calc(100vh - 2rem);
            overflow-y: auto;
            border: 1px solid var(--surface-soft);
            border-radius: 1rem;
            background: var(--surface);
            padding: 1.4rem;
            text-align: center;
            box-shadow: 0 30px 70px rgba(0, 0, 0, 0.48);
        }

        .booking-loading-overlay {
            position: fixed;
            inset: 0;
            z-index: 120;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(0, 0, 0, 0.76);
            padding: 1rem;
            backdrop-filter: blur(14px);
        }

        .booking-loading-title {
            margin: 0.5rem 0 0;
            font-size: 1.55rem;
            font-weight: 950;
        }

        .booking-loading-step {
            margin: 0.8rem 0 0;
            color: var(--muted);
            font-size: 0.9rem;
            line-height: 1.6;
        }

        .booking-loading-track {
            height: 0.5rem;
            margin-top: 1.2rem;
            overflow: hidden;
            border-radius: 999px;
            background: var(--line);
        }

        .booking-loading-fallback {
            margin: 1rem 0 0;
            color: var(--muted);
            font-size: 0.78rem;
            line-height: 1.45;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        @keyframes bookingProgress {
            0% { transform: scaleX(0.12); }
            45% { transform: scaleX(0.72); }
            100% { transform: scaleX(1); }
        }

        @keyframes bookingStepIn {
            from {
                opacity: 0;
                transform: translate3d(0, 10px, 0);
            }
            to {
                opacity: 1;
                transform: translate3d(0, 0, 0);
            }
        }

        @keyframes bookingPageIn {
            from {
                opacity: 0;
                transform: translate3d(0, 18px, 0) scale(0.985);
            }
            to {
                opacity: 1;
                transform: translate3d(0, 0, 0) scale(1);
            }
        }

        .booking-native-field {
            position: absolute;
            width: 1px;
            height: 1px;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
        }

        .booking-choice-grid,
        .booking-date-grid,
        .booking-time-grid {
            display: grid;
            gap: 0.75rem;
            margin-top: 1rem;
        }

        .booking-choice-card,
        .booking-date-card,
        .booking-time-card {
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(216, 191, 122, 0.18);
            border-radius: 0.95rem;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.055), rgba(255, 255, 255, 0.025));
            color: var(--cream);
            text-align: left;
            transition: transform 220ms ease, border-color 220ms ease, background 220ms ease, box-shadow 220ms ease, opacity 220ms ease;
        }

        .booking-choice-card {
            padding: 1rem;
        }

        .booking-date-card,
        .booking-time-card {
            padding: 0.85rem;
        }

        .booking-choice-card::before,
        .booking-date-card::before,
        .booking-time-card::before,
        .booking-button::before,
        .booking-back-link::before {
            position: absolute;
            inset: 0;
            background: linear-gradient(110deg, transparent 0%, rgba(255, 255, 255, 0.22) 42%, transparent 68%);
            content: "";
            opacity: 0;
            transform: translateX(-115%);
            transition: transform 620ms cubic-bezier(0.16, 1, 0.3, 1), opacity 220ms ease;
            pointer-events: none;
        }

        .booking-choice-card:hover::before,
        .booking-choice-card:focus-visible::before,
        .booking-date-card:hover::before,
        .booking-date-card:focus-visible::before,
        .booking-time-card:hover::before,
        .booking-time-card:focus-visible::before,
        .booking-button:hover::before,
        .booking-button:focus-visible::before,
        .booking-back-link:hover::before,
        .booking-back-link:focus-visible::before {
            opacity: 1;
            transform: translateX(115%);
        }

        .booking-choice-card:hover,
        .booking-date-card:hover,
        .booking-time-card:hover {
            border-color: rgba(216, 191, 122, 0.48);
            transform: translateY(-2px);
            box-shadow: 0 16px 28px rgba(0, 0, 0, 0.2);
        }

        .booking-choice-card.is-selected,
        .booking-date-card.is-selected,
        .booking-time-card.is-selected {
            border-color: rgba(216, 191, 122, 0.78);
            background: linear-gradient(180deg, rgba(216, 191, 122, 0.22), rgba(216, 191, 122, 0.08));
            box-shadow: 0 16px 32px rgba(200, 173, 99, 0.14);
        }

        .booking-choice-card__top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1rem;
        }

        .booking-choice-card__name,
        .booking-time-card__time {
            display: block;
            font-size: 1rem;
            font-weight: 950;
            line-height: 1.2;
        }

        .booking-choice-card__price {
            flex: 0 0 auto;
            color: var(--gold-soft);
            font-weight: 950;
        }

        .booking-choice-card__copy,
        .booking-choice-card__meta,
        .booking-time-card__state {
            display: block;
            margin-top: 0.55rem;
            color: var(--cream-soft);
            font-size: 0.82rem;
            line-height: 1.45;
        }

        .booking-choice-card__meta,
        .booking-date-card__day {
            color: var(--gold-soft);
            font-weight: 900;
        }

        .booking-date-card {
            min-height: 5rem;
        }

        .booking-date-card__day {
            display: block;
            font-size: 0.72rem;
            letter-spacing: 0.16em;
            text-transform: uppercase;
        }

        .booking-date-card__date {
            display: block;
            margin-top: 0.4rem;
            font-size: 1.1rem;
            font-weight: 950;
        }

        .booking-date-card__hint {
            display: block;
            margin-top: 0.25rem;
            color: var(--cream-soft);
            font-size: 0.75rem;
            font-weight: 700;
        }

        .booking-time-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .booking-time-card {
            text-align: center;
        }

        .booking-time-card.is-unavailable {
            cursor: not-allowed;
            opacity: 0.42;
            filter: grayscale(0.35);
        }

        .booking-date-card.is-closed,
        .booking-date-card.is-loading {
            cursor: not-allowed;
            opacity: 0.42;
            filter: grayscale(0.35);
        }

        .booking-time-card.is-unavailable:hover,
        .booking-date-card.is-closed:hover,
        .booking-date-card.is-loading:hover {
            border-color: rgba(216, 191, 122, 0.18);
            transform: none;
            box-shadow: none;
        }

        .booking-time-card.is-unavailable::before,
        .booking-date-card.is-closed::before,
        .booking-date-card.is-loading::before {
            display: none;
        }

        .booking-empty-state {
            margin: 1rem 0 0;
            border: 1px solid rgba(216, 191, 122, 0.16);
            border-radius: 0.9rem;
            background: rgba(255, 255, 255, 0.04);
            padding: 1rem;
            color: var(--cream-soft);
            font-size: 0.9rem;
            line-height: 1.55;
        }

        .booking-button,
        .booking-back-link {
            position: relative;
            overflow: hidden;
        }

        @media (min-width: 768px) {
            .booking-card {
                padding: 1.4rem;
            }

            .booking-aside {
                padding: 1.5rem;
            }

            .booking-field-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .booking-field--wide {
                grid-column: 1 / -1;
            }

            .booking-choice-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .booking-date-grid {
                grid-template-columns: repeat(4, minmax(0, 1fr));
            }

            .booking-time-grid {
                grid-template-columns: repeat(4, minmax(0, 1fr));
            }
        }

        @media (min-width: 640px) {
            .booking-step-actions {
                flex-direction: row;
                justify-content: space-between;
                align-items: center;
            }

            .booking-step-actions .booking-button {
                width: auto;
                min-width: 9.5rem;
            }

            .booking-summary-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .selected-service-card.is-visible {
                grid-template-columns: 1fr auto;
                align-items: start;
            }
        }

        @media (min-width: 1024px) {
            .booking-layout {
                grid-template-columns: 0.72fr 1.28fr;
                align-items: start;
            }

            .booking-aside {
                position: sticky;
                top: 1.25rem;
            }
        }

        @media (max-width: 639px) {
            .booking-shell {
                padding: 0.75rem;
            }

            .booking-stepper {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }

            .booking-stepper__item {
                padding: 0.5rem;
            }

            .booking-stepper__label {
                font-size: 0.68rem;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .booking-step.is-active,
            .booking-submit__spinner,
            .booking-loading-progress {
                animation: none;
            }

            .booking-loading-overlay,
            .booking-loading-card {
                transition: none;
            }
        }
    </style>
</head>
<body>
    <main class="booking-shell">
        <div class="booking-layout">
            <aside class="booking-aside">
                <a href="index.php" class="booking-back-link">
                    Zpět na hlavní stránku
                </a>
                <p class="booking-eyebrow">Hair By ReneNeme</p>
                <h1 class="booking-main-title">Rezervace termínu</h1>
                <p class="booking-aside-copy">
                    Vyber službu, den a čas. Kontaktní údaje doplníš až na konci před odesláním žádosti.
                </p>
                <div class="booking-info-box">
                    <p><strong>Otevírací doba:</strong> <?= htmlspecialchars($businessOpeningHoursLabel, ENT_QUOTES, 'UTF-8') ?></p>
                    <p><strong>Adresa:</strong> <?= htmlspecialchars($businessAddress, ENT_QUOTES, 'UTF-8') ?></p>
                    <p><strong>Telefon:</strong> <a href="<?= htmlspecialchars($businessPhoneHref, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($businessPhone, ENT_QUOTES, 'UTF-8') ?></a></p>
                </div>
            </aside>

            <form id="bookingForm" action="save_reservation.php" method="POST" class="booking-card">
                <?= app_csrf_field() ?>
                <input type="hidden" name="form_started_at" value="<?= app_booking_form_started_at() ?>">
                <div class="hidden" aria-hidden="true">
                    <label for="website">Web</label>
                    <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
                </div>

                <div class="booking-form-header">
                    <div>
                        <p class="booking-eyebrow">Rezervace krok za krokem</p>
                        <h2 class="booking-form-title">Služba, datum, čas a až potom tvoje údaje.</h2>
                    </div>
                    <div class="booking-stepper" aria-label="Postup rezervace">
                        <button type="button" class="booking-stepper__item is-active" data-booking-step-target="0" aria-current="step">
                            <span class="booking-stepper__number">1</span>
                            <span class="booking-stepper__label">Služba</span>
                        </button>
                        <button type="button" class="booking-stepper__item" data-booking-step-target="1">
                            <span class="booking-stepper__number">2</span>
                            <span class="booking-stepper__label">Datum</span>
                        </button>
                        <button type="button" class="booking-stepper__item" data-booking-step-target="2">
                            <span class="booking-stepper__number">3</span>
                            <span class="booking-stepper__label">Čas</span>
                        </button>
                        <button type="button" class="booking-stepper__item" data-booking-step-target="3">
                            <span class="booking-stepper__number">4</span>
                            <span class="booking-stepper__label">Údaje</span>
                        </button>
                        <button type="button" class="booking-stepper__item" data-booking-step-target="4">
                            <span class="booking-stepper__number">5</span>
                            <span class="booking-stepper__label">Kontrola</span>
                        </button>
                    </div>
                </div>

                <div class="booking-step is-active" data-booking-step="0">
                    <div>
                        <p class="booking-eyebrow">Služba</p>
                        <h3 class="booking-title">Co budeme stříhat?</h3>
                        <p class="booking-copy">Podle služby se rovnou spočítá délka návštěvy a nabídnou se správné volné časy.</p>
                    </div>
                    <div class="booking-field">
                        <label for="service">Vyber službu *</label>
                        <select id="service" name="service" required class="booking-control booking-control--select booking-native-field" aria-label="Vyber službu">
                            <option value="">Vyber službu...</option>
                            <?php foreach ($services as $serviceName => $service): ?>
                                <option value="<?= htmlspecialchars($serviceName, ENT_QUOTES, 'UTF-8') ?>" <?= $initialService === $serviceName ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($serviceName . ' (' . (string) ($service['price_label'] ?? '') . ')', ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="booking-choice-grid" id="serviceChoiceGrid">
                            <?php foreach ($services as $serviceName => $service): ?>
                                <button
                                    type="button"
                                    class="booking-choice-card"
                                    data-service-choice="<?= htmlspecialchars($serviceName, ENT_QUOTES, 'UTF-8') ?>"
                                >
                                    <span class="booking-choice-card__top">
                                        <span class="booking-choice-card__name"><?= htmlspecialchars($serviceName, ENT_QUOTES, 'UTF-8') ?></span>
                                        <span class="booking-choice-card__price"><?= htmlspecialchars((string) ($service['price_label'] ?? ''), ENT_QUOTES, 'UTF-8') ?></span>
                                    </span>
                                    <span class="booking-choice-card__copy"><?= htmlspecialchars((string) ($service['service_copy'] ?? $service['description'] ?? ''), ENT_QUOTES, 'UTF-8') ?></span>
                                    <span class="booking-choice-card__meta">cca <?= htmlspecialchars((string) ($service['duration'] ?? ''), ENT_QUOTES, 'UTF-8') ?> minut</span>
                                </button>
                            <?php endforeach; ?>
                        </div>
                        <p id="priceInfo" class="booking-copy hidden">
                            Vybraná služba: <span id="priceValue"></span>
                        </p>
                        <div id="selectedServiceCard" class="selected-service-card" aria-live="polite">
                            <div>
                                <p class="booking-eyebrow">Vybraná služba</p>
                                <h3 id="selectedServiceName" class="booking-title"></h3>
                                <p id="selectedServiceCopy" class="booking-copy"></p>
                            </div>
                            <div>
                                <p id="selectedServicePrice" class="booking-title"></p>
                                <p id="selectedServiceDuration" class="booking-copy"></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="booking-step" data-booking-step="1">
                    <div>
                        <p class="booking-eyebrow">Datum</p>
                        <h3 class="booking-title">Který den se ti hodí?</h3>
                        <p class="booking-copy">Online jde vybrat termín nejvýše <?= (int) app_booking_max_advance_days() ?> dní dopředu.</p>
                    </div>
                    <div class="booking-field">
                        <label for="date">Datum návštěvy *</label>
                        <div class="booking-date-shell">
                            <input type="date" id="date" name="date" required class="booking-control booking-native-field">
                            <span id="dateDisplay" class="booking-date-display" aria-hidden="true">Vyber datum</span>
                        </div>
                        <div id="bookingDateGrid" class="booking-date-grid" aria-label="Vyber datum"></div>
                    </div>
                </div>

                <div class="booking-step" data-booking-step="2">
                    <div>
                        <p class="booking-eyebrow">Čas</p>
                        <h3 class="booking-title">Vyber volný čas.</h3>
                        <p class="booking-copy">Časy se načtou podle vybrané služby a dne, aby návštěva měla správnou délku.</p>
                    </div>
                    <div class="booking-field">
                        <label for="time">Volný čas *</label>
                        <select id="time" name="time" required class="booking-control booking-control--select booking-native-field" aria-label="Vyber čas">
                            <option value="">Nejprve vyber datum</option>
                        </select>
                        <div id="bookingTimeGrid" class="booking-time-grid" aria-label="Vyber čas"></div>
                        <p id="bookingTimeEmpty" class="booking-empty-state hidden">Nejprve vyber službu a datum. Potom se ukážou dostupné i obsazené časy.</p>
                    </div>
                </div>

                <div class="booking-step" data-booking-step="3">
                    <div>
                        <p class="booking-eyebrow">Údaje</p>
                        <h3 class="booking-title">Na koho termín podržíme?</h3>
                        <p class="booking-copy">Stačí kontakt pro potvrzení rezervace a rychlou domluvu.</p>
                    </div>
                    <div class="booking-field-grid">
                        <div class="booking-field booking-field--wide">
                            <label for="name">Jak se jmenuješ *</label>
                            <input type="text" id="name" name="name" required autocomplete="name" class="booking-input" placeholder="Např. Jan Novák">
                        </div>
                        <div class="booking-field">
                            <label for="email">E-mail *</label>
                            <input type="email" id="email" name="email" required autocomplete="email" class="booking-input" placeholder="jan.novak@email.cz">
                        </div>
                        <div class="booking-field">
                            <label for="phone">Telefon *</label>
                            <input type="tel" id="phone" name="phone" required inputmode="tel" autocomplete="tel" pattern="^[+0-9 ().\/-]{9,25}$" title="Zadej telefonní číslo, například +420 777 123 456" class="booking-input" placeholder="+420 777 123 456">
                        </div>
                    </div>
                </div>

                <div class="booking-step" data-booking-step="4">
                    <div>
                        <p class="booking-eyebrow">Kontrola</p>
                        <h3 class="booking-title">Mrkni, jestli všechno sedí.</h3>
                        <p class="booking-copy">Po odeslání se termín uloží jako žádost a přijde potvrzení po schválení.</p>
                    </div>
                    <div class="booking-summary-grid" aria-live="polite">
                        <div class="booking-summary-card">
                            <span>Služba</span>
                            <strong id="bookingSummaryService">Vyber službu</strong>
                        </div>
                        <div class="booking-summary-card">
                            <span>Termín</span>
                            <strong id="bookingSummarySlot">Vyber datum a čas</strong>
                        </div>
                        <div class="booking-summary-card">
                            <span>Kontakt</span>
                            <strong id="bookingSummaryContact">Doplníme z formuláře</strong>
                        </div>
                        <div class="booking-summary-card">
                            <span>Poznámka</span>
                            <strong id="bookingSummaryNote">Bez poznámky</strong>
                        </div>
                    </div>
                    <div class="booking-field">
                        <label for="note">Přání ke střihu (nepovinné)</label>
                        <textarea id="note" name="note" rows="3" class="booking-textarea" placeholder="Např. delší vlasy, speciální přání..."></textarea>
                    </div>
                    <div class="booking-consent">
                        <input type="checkbox" id="gdpr" name="gdpr" required>
                        <label for="gdpr">
                            Souhlasím se zpracováním osobních údajů pro účely rezervace. Údaje slouží jen k domluvě a potvrzení termínu.
                        </label>
                    </div>
                </div>

                <p id="errorMsg" class="booking-error hidden"></p>
                <p id="bookingLoadingMsg" class="booking-loading-inline hidden">
                    Žádost právě odesíláme. Chvilku vydrž, kontrolujeme termín a připravujeme shrnutí.
                </p>

                <div class="booking-step-actions">
                    <button type="button" id="bookingPrevButton" class="booking-button booking-button--ghost hidden">Zpět</button>
                    <button type="button" id="bookingNextButton" class="booking-button booking-button--primary">Pokračovat</button>
                    <button type="submit" id="bookingSubmitButton" class="booking-button booking-button--primary hidden">
                        <span id="bookingSubmitText">Požádat o termín</span>
                        <span id="bookingSubmitLoading" class="hidden">
                            <span class="booking-submit__spinner"></span>
                            Odesíláme žádost...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </main>

    <div id="bookingLoadingOverlay" class="booking-loading-overlay" aria-hidden="true">
        <div class="booking-loading-card">
            <p class="booking-eyebrow">Rezervace se odesílá</p>
            <h2 class="booking-loading-title">Držíme ti termín</h2>
            <p id="bookingLoadingStep" class="booking-loading-step">
                Kontrolujeme dostupnost vybraného času...
            </p>
            <div class="booking-loading-track">
                <div class="booking-loading-progress"></div>
            </div>
            <p class="booking-loading-fallback hidden">
                Pokud to trvá déle, nech stránku ještě chvíli otevřenou. Rezervace se zpracovává na serveru.
            </p>
        </div>
    </div>

<script>
(() => {
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
    const serviceChoiceButtons = Array.from(document.querySelectorAll('[data-service-choice]'));
    const bookingDateGrid = document.getElementById('bookingDateGrid');
    const bookingTimeGrid = document.getElementById('bookingTimeGrid');
    const bookingTimeEmpty = document.getElementById('bookingTimeEmpty');
    const errorMsg = document.getElementById('errorMsg');
    const bookingForm = document.getElementById('bookingForm');
    const bookingSubmitButton = document.getElementById('bookingSubmitButton');
    const bookingSubmitText = document.getElementById('bookingSubmitText');
    const bookingSubmitLoading = document.getElementById('bookingSubmitLoading');
    const bookingLoadingMsg = document.getElementById('bookingLoadingMsg');
    const bookingLoadingOverlay = document.getElementById('bookingLoadingOverlay');
    const bookingLoadingStep = document.getElementById('bookingLoadingStep');
    const bookingSteps = Array.from(document.querySelectorAll('[data-booking-step]'));
    const bookingStepTriggers = Array.from(document.querySelectorAll('[data-booking-step-target]'));
    const bookingPrevButton = document.getElementById('bookingPrevButton');
    const bookingNextButton = document.getElementById('bookingNextButton');
    const bookingSummaryContact = document.getElementById('bookingSummaryContact');
    const bookingSummaryService = document.getElementById('bookingSummaryService');
    const bookingSummarySlot = document.getElementById('bookingSummarySlot');
    const bookingSummaryNote = document.getElementById('bookingSummaryNote');
    const noteInput = document.getElementById('note');
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    const services = <?= json_encode($services, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;
    let currentStep = 0;
    let isSubmitting = false;
    let loadingStepTimer = null;
    let slowTimer = null;
    let dateAvailabilityRequest = 0;

    if (!serviceSelect || !dateInput || !timeInput || !bookingForm) return;

    const today = new Date();
    const todayDateStr = formatLocalDate(today);
    const maxBookingDate = new Date(today);
    maxBookingDate.setDate(maxBookingDate.getDate() + <?= (int) app_booking_max_advance_days() ?>);
    const maxBookingDateStr = formatLocalDate(maxBookingDate);
    dateInput.min = todayDateStr;
    dateInput.max = maxBookingDateStr;

    function formatLocalDate(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    function formatDisplayDate(value) {
        const parts = value.split('-');
        if (parts.length !== 3) return 'Vyber datum';
        return `${parts[2]}.${parts[1]}.${parts[0]}`;
    }

    function updateDateDisplay() {
        dateDisplay.textContent = dateInput.value ? formatDisplayDate(dateInput.value) : 'Vyber datum';
        updateDateChoiceState();
        updateSummary();
    }

    function renderDateChoices() {
        if (!bookingDateGrid) return;

        bookingDateGrid.innerHTML = '';
        const formatterDay = new Intl.DateTimeFormat('cs-CZ', { weekday: 'short' });
        const formatterDate = new Intl.DateTimeFormat('cs-CZ', { day: 'numeric', month: 'numeric' });

        for (let offset = 0; offset <= <?= (int) app_booking_max_advance_days() ?>; offset += 1) {
            const date = new Date(today);
            date.setDate(today.getDate() + offset);
            const value = formatLocalDate(date);
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'booking-date-card is-loading';
            button.dataset.dateChoice = value;
            button.innerHTML = `
                <span class="booking-date-card__day">${offset === 0 ? 'Dnes' : formatterDay.format(date)}</span>
                <span class="booking-date-card__date">${formatterDate.format(date)}</span>
                <span class="booking-date-card__hint">ověřujeme...</span>
            `;
            button.addEventListener('click', () => {
                if (button.classList.contains('is-closed')) return;
                dateInput.value = value;
                dateInput.dispatchEvent(new Event('change', { bubbles: true }));
            });
            bookingDateGrid.appendChild(button);
        }

        updateDateChoiceState();
        refreshDateAvailability();
    }

    function updateDateChoiceState() {
        document.querySelectorAll('[data-date-choice]').forEach(button => {
            button.classList.toggle('is-selected', button.dataset.dateChoice === dateInput.value);
        });
    }

    async function refreshDateAvailability() {
        const requestId = ++dateAvailabilityRequest;
        const selectedService = serviceSelect.value;

        await Promise.all(Array.from(document.querySelectorAll('[data-date-choice]')).map(async button => {
            const date = button.dataset.dateChoice || '';
            const hint = button.querySelector('.booking-date-card__hint');
            button.classList.add('is-loading');
            button.classList.remove('is-closed');
            button.disabled = true;
            if (hint) hint.textContent = 'ověřujeme...';

            try {
                const params = new URLSearchParams({ date });
                if (selectedService) {
                    params.set('service', selectedService);
                }
                const res = await fetch('load_times.php?' + params.toString());
                const data = await res.json();
                if (requestId !== dateAvailabilityRequest) return;

                const allTimes = data.all || [];
                const availableTimes = data.available || [];
                const isClosed = Boolean(data.closed || data.calendar_error || allTimes.length === 0 || availableTimes.length === 0);
                button.classList.toggle('is-closed', isClosed);
                button.disabled = isClosed;
                button.classList.remove('is-loading');
                if (hint) {
                    hint.textContent = isClosed ? 'zavřeno / obsazeno' : `${availableTimes.length} volných časů`;
                }
            } catch (error) {
                if (requestId !== dateAvailabilityRequest) return;
                button.classList.add('is-closed');
                button.classList.remove('is-loading');
                button.disabled = true;
                if (hint) hint.textContent = 'nelze ověřit';
            }
        }));

        updateDateChoiceState();
    }

    function setError(message) {
        errorMsg.textContent = message;
        errorMsg.classList.toggle('hidden', !message);
    }

    function updateSelectedServiceInfo() {
        const selected = serviceSelect.value;
        serviceChoiceButtons.forEach(button => {
            button.classList.toggle('is-selected', button.dataset.serviceChoice === selected);
        });
        if (services[selected]) {
            priceValue.textContent = `${services[selected].price_label} · cca ${services[selected].duration} min`;
            priceInfo.classList.remove('hidden');
            selectedServiceName.textContent = services[selected].service_title || selected;
            selectedServiceCopy.textContent = services[selected].service_copy || services[selected].description || '';
            selectedServicePrice.textContent = services[selected].price_label || '';
            selectedServiceDuration.textContent = `cca ${services[selected].duration} minut`;
            selectedServiceCard.classList.add('is-visible');
        } else {
            priceInfo.classList.add('hidden');
            selectedServiceCard.classList.remove('is-visible');
        }
        updateSummary();
    }

    function updateSummary() {
        const name = document.getElementById('name')?.value.trim() || '';
        const email = document.getElementById('email')?.value.trim() || '';
        const phone = document.getElementById('phone')?.value.trim() || '';
        const contactParts = [name, phone, email].filter(Boolean);
        const selected = serviceSelect.value;
        const service = services[selected];
        const date = dateInput.value ? formatDisplayDate(dateInput.value) : '';
        const time = timeInput.value || '';
        const note = noteInput.value.trim() || '';

        bookingSummaryService.textContent = service
            ? `${service.service_title || selected} · ${service.price_label || ''} · cca ${service.duration} min`
            : 'Vyber službu';
        bookingSummarySlot.textContent = date && time ? `${date} v ${time}` : 'Vyber datum a čas';
        bookingSummaryContact.textContent = contactParts.length ? contactParts.join(' · ') : 'Doplníme z formuláře';
        bookingSummaryNote.textContent = note || 'Bez poznámky';
    }

    function getStepFields(stepIndex) {
        return Array.from(bookingSteps[stepIndex]?.querySelectorAll('input, select, textarea') || [])
            .filter(field => !field.disabled && field.type !== 'hidden');
    }

    function validateStep(stepIndex, shouldFocus = true) {
        for (const field of getStepFields(stepIndex)) {
            if (!field.checkValidity()) {
                if (shouldFocus) {
                    field.reportValidity();
                    field.focus({ preventScroll: true });
                }
                setError(field.id === 'phone'
                    ? 'Mrkni prosím na telefon. Může být třeba +420 777 123 456.'
                    : 'Ještě prosím doplň zvýrazněné pole.');
                return false;
            }
        }
        setError('');
        return true;
    }

    function setStep(nextStep, shouldScroll = true) {
        currentStep = Math.max(0, Math.min(nextStep, bookingSteps.length - 1));
        bookingSteps.forEach((step, index) => step.classList.toggle('is-active', index === currentStep));
        bookingStepTriggers.forEach((trigger, index) => {
            trigger.classList.toggle('is-active', index === currentStep);
            trigger.classList.toggle('is-complete', index < currentStep);
            if (index === currentStep) {
                trigger.setAttribute('aria-current', 'step');
            } else {
                trigger.removeAttribute('aria-current');
            }
        });
        bookingPrevButton.classList.toggle('hidden', currentStep === 0);
        bookingNextButton.classList.toggle('hidden', currentStep === bookingSteps.length - 1);
        bookingSubmitButton.classList.toggle('hidden', currentStep !== bookingSteps.length - 1);
        updateSummary();

        if (shouldScroll) {
            bookingForm.scrollIntoView({
                behavior: prefersReducedMotion.matches ? 'auto' : 'smooth',
                block: 'start',
            });
        }
    }

    async function loadAvailableTimes() {
        const date = dateInput.value;
        const previouslySelectedTime = timeInput.value;

        if (!date) {
            setTimePlaceholder('Nejprve vyber datum');
            return;
        }

        try {
            const params = new URLSearchParams({ date });
            if (serviceSelect.value) {
                params.set('service', serviceSelect.value);
            }

            const res = await fetch('load_times.php?' + params.toString());
            const data = await res.json();
            const allTimes = data.all || data.available || [];
            const freeTimes = data.available || [];

            if (data.calendar_error) {
                setTimePlaceholder('Kalendář teď nejde ověřit');
                return;
            }

            if (data.closed) {
                setTimePlaceholder('V tento den je zavřeno');
                return;
            }

            if (allTimes.length === 0) {
                setTimePlaceholder('Žádné volné časy');
                return;
            }

            renderTimeChoices(allTimes, freeTimes, previouslySelectedTime);
            updateSummary();
        } catch (err) {
            console.error('Chyba při načítání časů:', err);
            setTimePlaceholder('Časy se teď nepodařilo načíst');
            setError('Volné časy se teď nepodařilo načíst. Zkus to prosím znovu za chvilku.');
        }
    }

    function setTimePlaceholder(text) {
        timeInput.innerHTML = '';
        const opt = document.createElement('option');
        opt.value = '';
        opt.textContent = text;
        opt.disabled = true;
        opt.selected = true;
        timeInput.appendChild(opt);
        timeInput.disabled = true;
        if (bookingTimeGrid) {
            bookingTimeGrid.innerHTML = '';
        }
        if (bookingTimeEmpty) {
            bookingTimeEmpty.textContent = text;
            bookingTimeEmpty.classList.remove('hidden');
        }
        updateSummary();
    }

    function renderTimeChoices(allTimes, freeTimes, previouslySelectedTime = '') {
        timeInput.innerHTML = '';
        bookingTimeGrid.innerHTML = '';
        bookingTimeEmpty.classList.add('hidden');
        timeInput.disabled = false;

        const placeholder = document.createElement('option');
        placeholder.value = '';
        placeholder.textContent = 'Vyber čas';
        placeholder.disabled = true;
        placeholder.selected = !freeTimes.includes(previouslySelectedTime);
        timeInput.appendChild(placeholder);

        if (!allTimes.length) {
            setTimePlaceholder('Žádné časy pro vybrané datum.');
            return;
        }

        allTimes.forEach(time => {
            const isAvailable = freeTimes.includes(time);
            const opt = document.createElement('option');
            opt.value = time;
            opt.textContent = time;
            opt.disabled = !isAvailable;
            opt.selected = isAvailable && time === previouslySelectedTime;
            timeInput.appendChild(opt);

            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'booking-time-card' + (isAvailable ? '' : ' is-unavailable');
            button.disabled = !isAvailable;
            button.dataset.timeChoice = time;
            button.innerHTML = `
                <span class="booking-time-card__time">${time}</span>
                <span class="booking-time-card__state">${isAvailable ? 'volno' : 'obsazeno'}</span>
            `;
            button.addEventListener('click', () => {
                if (!isAvailable) return;
                timeInput.value = time;
                updateTimeChoiceState();
                updateSummary();
            });
            bookingTimeGrid.appendChild(button);
        });

        updateTimeChoiceState();
    }

    function updateTimeChoiceState() {
        document.querySelectorAll('[data-time-choice]').forEach(button => {
            button.classList.toggle('is-selected', button.dataset.timeChoice === timeInput.value);
        });
    }

    bookingNextButton.addEventListener('click', () => {
        if (!validateStep(currentStep)) return;
        setStep(currentStep + 1);
    });

    bookingPrevButton.addEventListener('click', () => setStep(currentStep - 1));

    bookingStepTriggers.forEach(trigger => {
        trigger.addEventListener('click', () => {
            const requestedStep = Number(trigger.dataset.bookingStepTarget || 0);
            if (requestedStep <= currentStep) {
                setStep(requestedStep);
                return;
            }

            for (let stepIndex = currentStep; stepIndex < requestedStep; stepIndex += 1) {
                if (!validateStep(stepIndex)) {
                    setStep(stepIndex);
                    return;
                }
            }
            setStep(requestedStep);
        });
    });

    bookingForm.querySelectorAll('input, select, textarea').forEach(field => {
        field.addEventListener('input', updateSummary);
        field.addEventListener('change', updateSummary);
        field.addEventListener('invalid', () => {
            setError(field.id === 'phone'
                ? 'Mrkni prosím na telefon. Může být třeba +420 777 123 456.'
                : 'Ještě prosím doplň zvýrazněné pole.');
        });
    });

    serviceSelect.addEventListener('change', () => {
        updateSelectedServiceInfo();
        refreshDateAvailability();
        if (dateInput.value) {
            loadAvailableTimes();
        }
    });
    serviceChoiceButtons.forEach(button => {
        button.addEventListener('click', () => {
            serviceSelect.value = button.dataset.serviceChoice || '';
            serviceSelect.dispatchEvent(new Event('change', { bubbles: true }));
        });
    });
    dateInput.addEventListener('input', updateDateDisplay);
    dateInput.addEventListener('change', () => {
        updateDateDisplay();
        loadAvailableTimes();
    });
    timeInput.addEventListener('change', updateSummary);
    timeInput.addEventListener('change', updateTimeChoiceState);

    bookingForm.addEventListener('submit', event => {
        if (isSubmitting) {
            event.preventDefault();
            return;
        }

        setError('');
        bookingLoadingMsg.classList.add('hidden');

        for (let stepIndex = 0; stepIndex < bookingSteps.length; stepIndex += 1) {
            if (!validateStep(stepIndex, false)) {
                event.preventDefault();
                setStep(stepIndex);
                window.setTimeout(() => validateStep(stepIndex), 120);
                return;
            }
        }

        const chosenDate = dateInput.value;
        const chosenTime = timeInput.value;
        const now = new Date();
        const todayStr = formatLocalDate(now);
        const nowTimeStr = now.toTimeString().slice(0, 5);

        if (chosenDate < todayStr) {
            event.preventDefault();
            setError('Tohle datum už je za námi. Vyber prosím některý z dalších dnů.');
            setStep(1);
            return;
        }

        if (chosenDate > maxBookingDateStr) {
            event.preventDefault();
            setError('Online jde vybrat termín nejvýše <?= (int) app_booking_max_advance_days() ?> dní dopředu.');
            setStep(1);
            return;
        }

        if (chosenDate === todayStr && chosenTime < nowTimeStr) {
            event.preventDefault();
            setError('Tenhle čas už dnes proběhl. Vyber prosím pozdější termín.');
            setStep(2);
            return;
        }

        isSubmitting = true;
        bookingSubmitButton.disabled = true;
        bookingSubmitText.classList.add('hidden');
        bookingSubmitLoading.classList.remove('hidden');
        bookingSubmitLoading.classList.add('inline-flex');
        bookingLoadingMsg.classList.remove('hidden');
        bookingLoadingOverlay.classList.add('is-visible');
        bookingLoadingOverlay.classList.remove('is-slow');
        bookingLoadingOverlay.setAttribute('aria-hidden', 'false');
        document.body.classList.add('overflow-hidden');

        const loadingSteps = [
            'Kontrolujeme, jestli je čas volný...',
            'Ukládáme tvoji žádost...',
            'Připravujeme shrnutí rezervace...'
        ];
        let loadingStepIndex = 0;
        bookingLoadingStep.textContent = loadingSteps[loadingStepIndex];
        window.clearInterval(loadingStepTimer);
        loadingStepTimer = window.setInterval(() => {
            loadingStepIndex = Math.min(loadingStepIndex + 1, loadingSteps.length - 1);
            bookingLoadingStep.textContent = loadingSteps[loadingStepIndex];
        }, 700);

        slowTimer = window.setTimeout(() => {
            bookingLoadingOverlay.classList.add('is-slow');
        }, 6500);

        event.preventDefault();
        window.requestAnimationFrame(() => {
            window.setTimeout(() => {
                HTMLFormElement.prototype.submit.call(bookingForm);
            }, 320);
        });
    });

    window.addEventListener('pageshow', () => {
        window.clearTimeout(slowTimer);
        window.clearInterval(loadingStepTimer);
        isSubmitting = false;
        bookingSubmitButton.disabled = false;
        bookingSubmitText.classList.remove('hidden');
        bookingSubmitLoading.classList.add('hidden');
        bookingSubmitLoading.classList.remove('inline-flex');
        bookingLoadingMsg.classList.add('hidden');
        bookingLoadingOverlay.classList.remove('is-visible', 'is-slow');
        bookingLoadingOverlay.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('overflow-hidden');
    });

    setTimePlaceholder('Nejprve vyber datum');
    renderDateChoices();
    updateDateDisplay();
    updateSelectedServiceInfo();
    setStep(0, false);
})();
</script>
</body>
</html>
