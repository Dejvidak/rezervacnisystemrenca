<?php

require_once __DIR__ . '/config.php';

function app_run_reservation_integrations(array $reservation): array
{
    $result = [
        'owner_email_sent' => false,
        'customer_email_sent' => false,
        'calendar_event_id' => null,
        'errors' => [],
    ];

    $emailResult = app_send_reservation_emails($reservation);
    $result['owner_email_sent'] = $emailResult['owner_email_sent'];
    $result['customer_email_sent'] = $emailResult['customer_email_sent'];
    $result['errors'] = array_merge($result['errors'], $emailResult['errors']);

    $calendarResult = app_create_google_calendar_event($reservation);
    $result['calendar_event_id'] = $calendarResult['event_id'];
    $result['errors'] = array_merge($result['errors'], $calendarResult['errors']);

    return $result;
}

function app_run_reservation_request_integrations(array $reservation): array
{
    $result = [
        'owner_email_sent' => false,
        'customer_email_sent' => false,
        'calendar_event_id' => null,
        'errors' => [],
    ];

    $ownerResult = app_send_reservation_owner_email($reservation);
    $result['owner_email_sent'] = $ownerResult['sent'];
    $result['errors'] = array_merge($result['errors'], $ownerResult['errors']);

    return $result;
}

function app_run_reservation_acceptance_integrations(array $reservation): array
{
    $result = [
        'customer_email_sent' => false,
        'calendar_event_id' => $reservation['calendar_event_id'] ?? null,
        'errors' => [],
    ];

    if (empty($result['calendar_event_id'])) {
        $calendarResult = app_create_google_calendar_event($reservation);
        $result['calendar_event_id'] = $calendarResult['event_id'];
        $result['errors'] = array_merge($result['errors'], $calendarResult['errors']);

        if (empty($result['calendar_event_id'])) {
            return $result;
        }
    }

    $customerResult = app_send_reservation_customer_email($reservation);
    $result['customer_email_sent'] = $customerResult['sent'];
    $result['errors'] = array_merge($result['errors'], $customerResult['errors']);

    return $result;
}

function app_send_reservation_emails(array $reservation): array
{
    $result = [
        'owner_email_sent' => false,
        'customer_email_sent' => false,
        'errors' => [],
    ];

    if (!app_email_enabled()) {
        $result['errors'][] = 'BOOKING_NOTIFY_EMAIL není nastavený, e-maily se neposlaly.';
        return $result;
    }

    $configurationErrors = app_email_configuration_errors();
    if (!empty($configurationErrors)) {
        $result['errors'] = array_merge($result['errors'], $configurationErrors);
        return $result;
    }

    $ownerResult = app_send_reservation_owner_email($reservation);
    $result['owner_email_sent'] = $ownerResult['sent'];
    $result['errors'] = array_merge($result['errors'], $ownerResult['errors']);

    $customerResult = app_send_reservation_customer_email($reservation);
    $result['customer_email_sent'] = $customerResult['sent'];
    $result['errors'] = array_merge($result['errors'], $customerResult['errors']);

    return $result;
}

function app_send_reservation_owner_email(array $reservation): array
{
    $result = ['sent' => false, 'errors' => []];

    if (!app_email_enabled()) {
        $result['errors'][] = 'BOOKING_NOTIFY_EMAIL není nastavený, e-mail majiteli se neposlal.';
        return $result;
    }

    $configurationErrors = app_email_configuration_errors();
    if (!empty($configurationErrors)) {
        $result['errors'] = array_merge($result['errors'], $configurationErrors);
        return $result;
    }

    $result['sent'] = app_send_plain_email(
        app_owner_email(),
        'Nová rezervace čeká na schválení: ' . $reservation['name'] . ' - ' . $reservation['date'] . ' ' . $reservation['time'],
        app_owner_email_body($reservation),
        $reservation['email'],
        app_owner_email_html_body($reservation)
    );

    if (!$result['sent']) {
        $result['errors'][] = 'E-mail majiteli se nepodařilo odeslat.';
    }

    return $result;
}

function app_send_reservation_customer_email(array $reservation): array
{
    $result = ['sent' => false, 'errors' => []];

    if (!filter_var($reservation['email'] ?? '', FILTER_VALIDATE_EMAIL)) {
        return $result;
    }

    if (!app_email_enabled()) {
        $result['errors'][] = 'BOOKING_NOTIFY_EMAIL není nastavený, potvrzovací e-mail zákazníkovi se neposlal.';
        return $result;
    }

    $configurationErrors = app_email_configuration_errors();
    if (!empty($configurationErrors)) {
        $result['errors'] = array_merge($result['errors'], $configurationErrors);
        return $result;
    }

    $result['sent'] = app_send_plain_email(
        $reservation['email'],
        'Potvrzení rezervace - Hair By ReneNeme',
        app_customer_email_body($reservation),
        app_owner_email(),
        app_customer_email_html_body($reservation)
    );

    if (!$result['sent']) {
        $result['errors'][] = 'Potvrzovací e-mail zákazníkovi se nepodařilo odeslat.';
    }

    return $result;
}

function app_email_configuration_errors(): array
{
    $errors = [];

    if (trim((string) getenv('SMTP_HOST')) === '') {
        return $errors;
    }

    $port = (int) (getenv('SMTP_PORT') ?: 465);
    $username = trim((string) getenv('SMTP_USERNAME'));
    $password = (string) getenv('SMTP_PASSWORD');
    $encryption = strtolower(trim((string) (getenv('SMTP_ENCRYPTION') ?: 'ssl')));
    $from = trim((string) (getenv('SMTP_FROM') ?: app_from_email()));

    if ($port <= 0) {
        $errors[] = 'SMTP_PORT není platný.';
    }

    if (!in_array($encryption, ['ssl', 'tls', 'none'], true)) {
        $errors[] = 'SMTP_ENCRYPTION musí být ssl, tls nebo none.';
    }

    if ($username === '') {
        $errors[] = 'SMTP_USERNAME není nastavený, e-maily se neposlaly.';
    }

    if ($password === '') {
        $errors[] = 'SMTP_PASSWORD není nastavené, e-maily se neposlaly.';
    }

    if ($from === '' || !filter_var($from, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'SMTP_FROM není platná e-mailová adresa.';
    }

    return $errors;
}

function app_send_plain_email(string $to, string $subject, string $body, ?string $replyTo = null, ?string $htmlBody = null): bool
{
    if (trim((string) getenv('SMTP_HOST')) !== '') {
        return app_send_smtp_email($to, $subject, $body, $replyTo, $htmlBody);
    }

    $headers = [
        'MIME-Version: 1.0',
        'From: Hair By ReneNeme <' . app_from_email() . '>',
    ];

    if ($replyTo !== null && filter_var($replyTo, FILTER_VALIDATE_EMAIL)) {
        $headers[] = 'Reply-To: ' . $replyTo;
    }

    $message = app_build_mime_body($body, $htmlBody, $headers);

    return mail($to, '=?UTF-8?B?' . base64_encode($subject) . '?=', $message, implode("\r\n", $headers));
}

function app_send_smtp_email(string $to, string $subject, string $body, ?string $replyTo = null, ?string $htmlBody = null): bool
{
    $host = trim((string) getenv('SMTP_HOST'));
    $port = (int) (getenv('SMTP_PORT') ?: 465);
    $username = trim((string) getenv('SMTP_USERNAME'));
    $password = (string) getenv('SMTP_PASSWORD');
    $encryption = strtolower(trim((string) (getenv('SMTP_ENCRYPTION') ?: 'ssl')));
    $from = trim((string) (getenv('SMTP_FROM') ?: app_from_email()));
    $fromName = trim((string) (getenv('SMTP_FROM_NAME') ?: 'Hair By ReneNeme'));

    if ($host === '' || $from === '' || !filter_var($from, FILTER_VALIDATE_EMAIL)) {
        return false;
    }

    if ($username === '' || $password === '') {
        return false;
    }

    $remote = ($encryption === 'ssl' ? 'ssl://' : '') . $host . ':' . $port;
    $socket = stream_socket_client($remote, $errno, $errstr, 15, STREAM_CLIENT_CONNECT);
    if (!$socket) {
        return false;
    }

    stream_set_timeout($socket, 15);

    if (!app_smtp_expect($socket, [220])) {
        fclose($socket);
        return false;
    }

    if (!app_smtp_command($socket, 'EHLO localhost', [250])) {
        fclose($socket);
        return false;
    }

    if ($encryption === 'tls') {
        if (!app_smtp_command($socket, 'STARTTLS', [220])) {
            fclose($socket);
            return false;
        }

        if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
            fclose($socket);
            return false;
        }

        if (!app_smtp_command($socket, 'EHLO localhost', [250])) {
            fclose($socket);
            return false;
        }
    }

    if (!app_smtp_command($socket, 'AUTH LOGIN', [334])) {
        fclose($socket);
        return false;
    }

    if (!app_smtp_command($socket, base64_encode($username), [334])) {
        fclose($socket);
        return false;
    }

    if (!app_smtp_command($socket, base64_encode($password), [235])) {
        fclose($socket);
        return false;
    }

    if (!app_smtp_command($socket, 'MAIL FROM:<' . $from . '>', [250])) {
        fclose($socket);
        return false;
    }

    if (!app_smtp_command($socket, 'RCPT TO:<' . $to . '>', [250, 251])) {
        fclose($socket);
        return false;
    }

    if (!app_smtp_command($socket, 'DATA', [354])) {
        fclose($socket);
        return false;
    }

    $message = app_build_smtp_message($to, $subject, $body, $from, $fromName, $replyTo, $htmlBody);
    fwrite($socket, app_dot_stuff($message) . "\r\n.\r\n");
    $ok = app_smtp_expect($socket, [250]);

    app_smtp_command($socket, 'QUIT', [221]);
    fclose($socket);

    return $ok;
}

function app_build_smtp_message(string $to, string $subject, string $body, string $from, string $fromName, ?string $replyTo, ?string $htmlBody = null): string
{
    $headers = [
        'Date: ' . date(DATE_RFC2822),
        'From: ' . app_encode_header($fromName) . ' <' . $from . '>',
        'To: <' . $to . '>',
        'Subject: ' . app_encode_header($subject),
        'MIME-Version: 1.0',
    ];

    if ($replyTo !== null && filter_var($replyTo, FILTER_VALIDATE_EMAIL)) {
        $headers[] = 'Reply-To: <' . $replyTo . '>';
    }

    $message = app_build_mime_body($body, $htmlBody, $headers);

    return implode("\r\n", $headers) . "\r\n\r\n" . $message;
}

function app_build_mime_body(string $plainBody, ?string $htmlBody, array &$headers): string
{
    $plainBody = str_replace(["\r\n", "\r"], "\n", $plainBody);

    if ($htmlBody === null || trim($htmlBody) === '') {
        $headers[] = 'Content-Type: text/plain; charset=UTF-8';
        $headers[] = 'Content-Transfer-Encoding: 8bit';
        return $plainBody;
    }

    $boundary = '=_hairbyreneneme_' . bin2hex(random_bytes(12));
    $headers[] = 'Content-Type: multipart/alternative; boundary="' . $boundary . '"';

    return implode("\r\n", [
        '--' . $boundary,
        'Content-Type: text/plain; charset=UTF-8',
        'Content-Transfer-Encoding: 8bit',
        '',
        $plainBody,
        '--' . $boundary,
        'Content-Type: text/html; charset=UTF-8',
        'Content-Transfer-Encoding: 8bit',
        '',
        str_replace(["\r\n", "\r"], "\n", $htmlBody),
        '--' . $boundary . '--',
    ]);
}

function app_encode_header(string $value): string
{
    return '=?UTF-8?B?' . base64_encode($value) . '?=';
}

function app_dot_stuff(string $message): string
{
    $message = str_replace(["\r\n", "\r"], "\n", $message);
    $lines = explode("\n", $message);
    $lines = array_map(function (string $line): string {
        return str_starts_with($line, '.') ? '.' . $line : $line;
    }, $lines);

    return implode("\r\n", $lines);
}

function app_smtp_command($socket, string $command, array $expectedCodes): bool
{
    fwrite($socket, $command . "\r\n");
    return app_smtp_expect($socket, $expectedCodes);
}

function app_smtp_expect($socket, array $expectedCodes): bool
{
    $response = '';

    while (($line = fgets($socket, 515)) !== false) {
        $response .= $line;
        if (preg_match('/^(\d{3})\s/', $line, $match)) {
            return in_array((int) $match[1], $expectedCodes, true);
        }
    }

    return false;
}

function app_owner_email_body(array $reservation): string
{
    $adminUrl = app_site_url() . '/admin.php?date=' . rawurlencode((string) $reservation['date']);

    return implode("\n", [
        'Nová rezervace čeká na schválení',
        '',
        'Prosím zkontroluj termín a rezervaci potvrď nebo smaž v administraci.',
        '',
        'Jméno: ' . $reservation['name'],
        'E-mail: ' . $reservation['email'],
        'Telefon: ' . $reservation['phone'],
        'Datum: ' . $reservation['date'],
        'Čas: ' . $reservation['time'],
        'Služba: ' . $reservation['service'],
        'Délka: ' . $reservation['duration'] . ' min',
        'Cena: ' . app_price_label($reservation['service']),
        'Poznámka: ' . (($reservation['note'] ?? '') !== '' ? $reservation['note'] : '-'),
        '',
        'Administrace pro daný den: ' . $adminUrl,
        'Celá administrace: ' . app_site_url() . '/admin.php',
    ]);
}

function app_owner_email_html_body(array $reservation): string
{
    $date = DateTime::createFromFormat('!Y-m-d', (string) $reservation['date']);
    $displayDate = $date ? $date->format('d.m.Y') : (string) $reservation['date'];
    $adminUrl = app_site_url() . '/admin.php?date=' . rawurlencode((string) $reservation['date']);
    $allAdminUrl = app_site_url() . '/admin.php';
    $time = app_email_escape((string) $reservation['time']);
    $service = app_email_escape((string) $reservation['service']);
    $duration = app_email_escape((string) $reservation['duration']);
    $price = app_email_escape(app_price_label((string) $reservation['service']));
    $name = app_email_escape((string) $reservation['name']);
    $email = app_email_escape((string) $reservation['email']);
    $phone = app_email_escape((string) $reservation['phone']);
    $phoneHref = app_email_escape(preg_replace('/\s+/', '', (string) $reservation['phone']));
    $note = trim((string) ($reservation['note'] ?? ''));
    $displayNote = $note !== '' ? nl2br(app_email_escape($note)) : '-';

    return '<!doctype html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nová rezervace</title>
</head>
<body style="margin:0;background:#0D0D0B;color:#F7F3EA;font-family:Arial,Helvetica,sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#0D0D0B;padding:24px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:640px;overflow:hidden;border:1px solid #302D27;border-radius:16px;background:#171613;">
                    <tr>
                        <td style="background:#080807;padding:22px 28px;border-bottom:1px solid #302D27;color:#F7F3EA;">
                            <div style="font-size:18px;font-weight:800;letter-spacing:-0.2px;color:#F7F3EA;">Hair By <span style="color:#D8BF7A;">ReneNeme</span></div>
                            <div style="margin-top:6px;font-size:11px;font-weight:700;letter-spacing:2.6px;text-transform:uppercase;color:#D8BF7A;">Administrace rezervací</div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:26px 28px 24px;">
                            <div style="font-size:12px;font-weight:700;letter-spacing:2.8px;text-transform:uppercase;color:#D8BF7A;">Nová rezervace čeká na schválení</div>
                            <h1 style="margin:10px 0 0;font-size:28px;line-height:1.2;color:#F7F3EA;">' . $name . ' chce termín</h1>
                            <p style="margin:12px 0 18px;font-size:15px;line-height:1.7;color:#DCD3C2;">Zkontroluj detail rezervace a v administraci ji potvrď nebo smaž.</p>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td style="padding:18px;border:1px solid #3C3831;border-radius:14px;background:#080807;">
                                        <div style="font-size:11px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:#D8BF7A;">Termín ke kontrole</div>
                                        <div style="margin-top:7px;font-size:26px;line-height:1.2;font-weight:900;color:#F7F3EA;">' . app_email_escape($displayDate) . ' v ' . $time . '</div>
                                        <div style="margin-top:4px;font-size:14px;color:#C8C1B4;">cca ' . $duration . ' minut</div>
                                    </td>
                                </tr>
                            </table>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin-top:14px;">
                                <tr>
                                    <td style="padding:14px;border:1px solid #302D27;border-radius:14px;background:#1F1D19;">
                                        <div style="font-size:11px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:#D8BF7A;">Služba</div>
                                        <div style="margin-top:6px;font-size:18px;font-weight:800;color:#F7F3EA;">' . $service . '</div>
                                        <div style="margin-top:4px;font-size:14px;color:#C8C1B4;">Cena: ' . $price . '</div>
                                    </td>
                                </tr>
                            </table>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin-top:14px;">
                                <tr>
                                    <td style="padding:18px;border:1px solid #302D27;border-radius:14px;background:#080807;color:#F7F3EA;">
                                        <div style="font-size:11px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:#D8BF7A;">Kontakt na zákazníka</div>
                                        <p style="margin:10px 0 0;font-size:15px;line-height:1.7;color:#DCD3C2;">
                                            Jméno: ' . $name . '<br>
                                            Telefon: <a href="tel:' . $phoneHref . '" style="color:#F0DFA9;text-decoration:none;">' . $phone . '</a><br>
                                            E-mail: <a href="mailto:' . $email . '" style="color:#F0DFA9;text-decoration:none;">' . $email . '</a>
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin-top:14px;">
                                <tr>
                                    <td style="padding:14px;border:1px solid #302D27;border-radius:14px;background:#1F1D19;">
                                        <div style="font-size:11px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:#D8BF7A;">Poznámka</div>
                                        <div style="margin-top:6px;font-size:14px;line-height:1.7;color:#C8C1B4;">' . $displayNote . '</div>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:18px 0 0;">
                                <a href="' . app_email_escape($adminUrl) . '" style="display:inline-block;border-radius:12px;background:#C8AD63;padding:13px 18px;color:#080807;font-size:14px;font-weight:800;text-decoration:none;">Otevřít den v administraci</a>
                            </p>
                            <p style="margin:12px 0 0;font-size:13px;line-height:1.7;color:#C8C1B4;">
                                Celá administrace: <a href="' . app_email_escape($allAdminUrl) . '" style="color:#F0DFA9;font-weight:700;text-decoration:underline;">' . app_email_escape($allAdminUrl) . '</a>
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>';
}

function app_customer_email_body(array $reservation): string
{
    return implode("\n", [
        'Dobrý den,',
        '',
        'vaše rezervace byla potvrzena. Tady je její shrnutí:',
        '',
        'Datum: ' . $reservation['date'],
        'Čas: ' . $reservation['time'],
        'Služba: ' . $reservation['service'],
        'Délka: ' . $reservation['duration'] . ' min',
        'Cena: ' . app_price_label($reservation['service']),
        'Telefon: +420 608 419 610',
        'E-mail: renenemehair@seznam.cz',
        'Adresa: Vackova 1064/39, 612 00 Brno-Královo Pole',
        '',
        'Pokud potřebujete termín změnit nebo zrušit, dejte nám prosím vědět telefonicky nebo e-mailem.',
        '',
        'Hair By ReneNeme',
        'Vackova 1064/39, 612 00 Brno-Královo Pole',
    ]);
}

function app_customer_email_html_body(array $reservation): string
{
    $date = DateTime::createFromFormat('!Y-m-d', (string) $reservation['date']);
    $displayDate = $date ? $date->format('d.m.Y') : (string) $reservation['date'];
    $time = app_email_escape((string) $reservation['time']);
    $service = app_email_escape((string) $reservation['service']);
    $duration = app_email_escape((string) $reservation['duration']);
    $price = app_email_escape(app_price_label((string) $reservation['service']));
    $name = app_email_escape((string) $reservation['name']);
    $mapsUrl = 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode('Vackova 1064/39, 612 00 Brno-Královo Pole');

    return '<!doctype html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Potvrzení rezervace</title>
</head>
<body style="margin:0;background:#0D0D0B;color:#F7F3EA;font-family:Arial,Helvetica,sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#0D0D0B;padding:24px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:640px;overflow:hidden;border:1px solid #302D27;border-radius:16px;background:#171613;">
                    <tr>
                        <td style="background:#080807;padding:22px 28px;border-bottom:1px solid #302D27;color:#F7F3EA;">
                            <div style="font-size:18px;font-weight:800;letter-spacing:-0.2px;color:#F7F3EA;">Hair By <span style="color:#D8BF7A;">ReneNeme</span></div>
                            <div style="margin-top:6px;font-size:11px;font-weight:700;letter-spacing:2.6px;text-transform:uppercase;color:#D8BF7A;">Pánské kadeřnictví v Brně</div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:26px 28px 24px;">
                            <div style="font-size:12px;font-weight:700;letter-spacing:2.8px;text-transform:uppercase;color:#D8BF7A;">Rezervace potvrzena</div>
                            <h1 style="margin:10px 0 0;font-size:28px;line-height:1.2;color:#F7F3EA;">Těšíme se na vás, ' . $name . '</h1>
                            <p style="margin:12px 0 18px;font-size:15px;line-height:1.7;color:#DCD3C2;">Termín je potvrzený a uložený v kalendáři. Níže najdete všechny důležité informace k návštěvě.</p>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td style="padding:18px;border:1px solid #3C3831;border-radius:14px;background:#080807;">
                                        <div style="font-size:11px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:#D8BF7A;">Termín</div>
                                        <div style="margin-top:7px;font-size:26px;line-height:1.2;font-weight:900;color:#F7F3EA;">' . app_email_escape($displayDate) . ' v ' . $time . '</div>
                                        <div style="margin-top:4px;font-size:14px;color:#C8C1B4;">cca ' . $duration . ' minut</div>
                                    </td>
                                </tr>
                            </table>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin-top:14px;">
                                <tr>
                                    <td style="padding:14px;border:1px solid #302D27;border-radius:14px;background:#1F1D19;">
                                        <div style="font-size:11px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:#D8BF7A;">Služba</div>
                                        <div style="margin-top:6px;font-size:18px;font-weight:800;color:#F7F3EA;">' . $service . '</div>
                                        <div style="margin-top:4px;font-size:14px;color:#C8C1B4;">Cena: ' . $price . '</div>
                                    </td>
                                </tr>
                            </table>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin-top:18px;">
                                <tr>
                                    <td style="padding:18px;border:1px solid #302D27;border-radius:14px;background:#080807;color:#F7F3EA;">
                                        <div style="font-size:11px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:#D8BF7A;">Kontakt a místo</div>
                                        <p style="margin:10px 0 0;font-size:15px;line-height:1.7;color:#DCD3C2;">
                                            Hair By ReneNeme<br>
                                            Vackova 1064/39, 612 00 Brno-Královo Pole<br>
                                            Telefon: <a href="tel:+420608419610" style="color:#F0DFA9;text-decoration:none;">+420 608 419 610</a><br>
                                            E-mail: <a href="mailto:renenemehair@seznam.cz" style="color:#F0DFA9;text-decoration:none;">renenemehair@seznam.cz</a>
                                        </p>
                                        <p style="margin:16px 0 0;">
                                            <a href="' . app_email_escape($mapsUrl) . '" style="display:inline-block;border-radius:12px;background:#C8AD63;padding:13px 18px;color:#080807;font-size:14px;font-weight:800;text-decoration:none;">Otevřít mapu</a>
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:18px 0 0;font-size:14px;line-height:1.7;color:#C8C1B4;">Dorazte prosím na čas. Pokud potřebujete termín změnit nebo zrušit, dejte nám vědět telefonicky nebo e-mailem.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>';
}

function app_email_escape(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function app_create_google_calendar_event(array $reservation): array
{
    $result = [
        'event_id' => null,
        'errors' => [],
    ];

    $calendarId = trim((string) getenv('GOOGLE_CALENDAR_ID'));
    if ($calendarId === '') {
        $result['errors'][] = 'GOOGLE_CALENDAR_ID není nastavený, událost v kalendáři se nevytvořila.';
        return $result;
    }

    $serviceAccount = app_google_service_account();
    if ($serviceAccount === null) {
        $result['errors'][] = 'GOOGLE_SERVICE_ACCOUNT_JSON není nastavený nebo nejde přečíst.';
        return $result;
    }

    $token = app_google_access_token($serviceAccount);
    if ($token === null) {
        $result['errors'][] = 'Nepodařilo se získat Google access token.';
        return $result;
    }

    $start = DateTime::createFromFormat('Y-m-d H:i', $reservation['date'] . ' ' . $reservation['time'], new DateTimeZone(app_timezone()));
    if (!$start) {
        $result['errors'][] = 'Nepodařilo se vytvořit čas události pro Google Kalendář.';
        return $result;
    }

    $end = clone $start;
    $end->modify('+' . (int) $reservation['duration'] . ' minutes');

    $event = [
        'summary' => $reservation['service'] . ' - ' . $reservation['name'],
        'description' => app_calendar_description($reservation),
        'start' => [
            'dateTime' => $start->format(DateTimeInterface::ATOM),
            'timeZone' => app_timezone(),
        ],
        'end' => [
            'dateTime' => $end->format(DateTimeInterface::ATOM),
            'timeZone' => app_timezone(),
        ],
    ];

    $url = 'https://www.googleapis.com/calendar/v3/calendars/'
        . rawurlencode($calendarId)
        . '/events?sendUpdates=none';

    $response = app_http_json('POST', $url, $event, [
        'Authorization: Bearer ' . $token,
    ]);

    if ($response['status'] >= 200 && $response['status'] < 300 && isset($response['body']['id'])) {
        $result['event_id'] = (string) $response['body']['id'];
        return $result;
    }

    $message = is_array($response['body'])
        ? json_encode($response['body'], JSON_UNESCAPED_UNICODE)
        : (string) $response['raw'];
    $result['errors'][] = 'Google Kalendář vrátil chybu: HTTP ' . $response['status'] . ' ' . $message;

    return $result;
}

function app_delete_google_calendar_event(?string $eventId): void
{
    if ($eventId === null || $eventId === '') {
        return;
    }

    $calendarId = trim((string) getenv('GOOGLE_CALENDAR_ID'));
    $serviceAccount = app_google_service_account();
    if ($calendarId === '' || $serviceAccount === null) {
        return;
    }

    $token = app_google_access_token($serviceAccount);
    if ($token === null) {
        return;
    }

    $url = 'https://www.googleapis.com/calendar/v3/calendars/'
        . rawurlencode($calendarId)
        . '/events/'
        . rawurlencode($eventId);

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_CUSTOMREQUEST => 'DELETE',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $token],
        CURLOPT_TIMEOUT => 12,
    ]);
    curl_exec($ch);
}

function app_google_calendar_event_status(?string $eventId): array
{
    $result = [
        'configured' => false,
        'exists' => null,
        'missing' => false,
        'errors' => [],
    ];

    if ($eventId === null || $eventId === '') {
        $result['missing'] = true;
        $result['exists'] = false;
        return $result;
    }

    $calendarId = trim((string) getenv('GOOGLE_CALENDAR_ID'));
    if ($calendarId === '') {
        return $result;
    }

    $result['configured'] = true;

    $serviceAccount = app_google_service_account();
    if ($serviceAccount === null) {
        $result['errors'][] = 'GOOGLE_SERVICE_ACCOUNT_JSON není nastavený nebo nejde přečíst.';
        return $result;
    }

    $token = app_google_access_token($serviceAccount);
    if ($token === null) {
        $result['errors'][] = 'Nepodařilo se získat Google access token.';
        return $result;
    }

    $url = 'https://www.googleapis.com/calendar/v3/calendars/'
        . rawurlencode($calendarId)
        . '/events/'
        . rawurlencode($eventId);

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $token],
        CURLOPT_TIMEOUT => 12,
    ]);

    $raw = curl_exec($ch);
    $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    $body = json_decode((string) $raw, true);

    if ($status === 404 || $status === 410) {
        $result['exists'] = false;
        $result['missing'] = true;
        return $result;
    }

    if ($raw === false || $status < 200 || $status >= 300) {
        $message = is_array($body)
            ? json_encode($body, JSON_UNESCAPED_UNICODE)
            : (string) ($raw !== false ? $raw : $error);
        $result['errors'][] = 'Google Kalendář vrátil chybu při kontrole události: HTTP ' . $status . ' ' . $message;
        return $result;
    }

    if (is_array($body) && ($body['status'] ?? '') === 'cancelled') {
        $result['exists'] = false;
        $result['missing'] = true;
        return $result;
    }

    $result['exists'] = true;
    return $result;
}

function app_google_calendar_busy_reservations_for_date(string $date): array
{
    $result = [
        'configured' => false,
        'reservations' => [],
        'errors' => [],
    ];

    $calendarId = trim((string) getenv('GOOGLE_CALENDAR_ID'));
    if ($calendarId === '') {
        return $result;
    }

    $result['configured'] = true;
    $serviceAccount = app_google_service_account();
    if ($serviceAccount === null) {
        $result['errors'][] = 'GOOGLE_SERVICE_ACCOUNT_JSON není nastavený nebo nejde přečíst.';
        return $result;
    }

    $token = app_google_access_token($serviceAccount);
    if ($token === null) {
        $result['errors'][] = 'Nepodařilo se získat Google access token.';
        return $result;
    }

    $timezone = new DateTimeZone(app_timezone());
    $dayStart = DateTime::createFromFormat('!Y-m-d', $date, $timezone);
    if (!$dayStart || $dayStart->format('Y-m-d') !== $date) {
        $result['errors'][] = 'Neplatné datum pro kontrolu Google Kalendáře.';
        return $result;
    }

    $dayEnd = clone $dayStart;
    $dayEnd->modify('+1 day');

    $url = 'https://www.googleapis.com/calendar/v3/freeBusy';
    $response = app_http_json('POST', $url, [
        'timeMin' => $dayStart->format(DateTimeInterface::ATOM),
        'timeMax' => $dayEnd->format(DateTimeInterface::ATOM),
        'timeZone' => app_timezone(),
        'items' => [
            ['id' => $calendarId],
        ],
    ], [
        'Authorization: Bearer ' . $token,
    ]);

    if ($response['status'] < 200 || $response['status'] >= 300) {
        $message = is_array($response['body'])
            ? json_encode($response['body'], JSON_UNESCAPED_UNICODE)
            : (string) $response['raw'];
        $result['errors'][] = 'Google Kalendář vrátil chybu při kontrole obsazenosti: HTTP ' . $response['status'] . ' ' . $message;
        return $result;
    }

    $busyBlocks = $response['body']['calendars'][$calendarId]['busy'] ?? [];
    if (!is_array($busyBlocks)) {
        return $result;
    }

    foreach ($busyBlocks as $block) {
        if (empty($block['start']) || empty($block['end'])) {
            continue;
        }

        try {
            $start = new DateTime((string) $block['start']);
            $end = new DateTime((string) $block['end']);
        } catch (Exception $e) {
            continue;
        }

        $start->setTimezone($timezone);
        $end->setTimezone($timezone);

        if ($end <= $dayStart || $start >= $dayEnd) {
            continue;
        }

        $clampedStart = $start < $dayStart ? clone $dayStart : clone $start;
        $clampedEnd = $end > $dayEnd ? clone $dayEnd : clone $end;
        $duration = max(1, (int) ceil(($clampedEnd->getTimestamp() - $clampedStart->getTimestamp()) / 60));

        $result['reservations'][] = [
            'time' => $clampedStart->format('H:i'),
            'service' => 'Google Kalendář',
            'duration' => $duration,
        ];
    }

    return $result;
}

function app_google_calendar_overlaps(string $date, string $time, int $durationMinutes): array
{
    $busy = app_google_calendar_busy_reservations_for_date($date);
    $overlaps = false;

    if (empty($busy['errors'])) {
        $overlaps = app_reservations_overlap($busy['reservations'], $time, $durationMinutes);
    }

    return [
        'configured' => $busy['configured'],
        'overlaps' => $overlaps,
        'errors' => $busy['errors'],
    ];
}

function app_google_service_account(): ?array
{
    $value = trim((string) getenv('GOOGLE_SERVICE_ACCOUNT_JSON'));
    if ($value === '') {
        return null;
    }

    if (is_file($value)) {
        $value = (string) file_get_contents($value);
    }

    $json = json_decode($value, true);
    if (!is_array($json) || empty($json['client_email']) || empty($json['private_key'])) {
        return null;
    }

    return $json;
}

function app_google_access_token(array $serviceAccount): ?string
{
    $tokenUri = $serviceAccount['token_uri'] ?? 'https://oauth2.googleapis.com/token';
    $now = time();
    $header = ['alg' => 'RS256', 'typ' => 'JWT'];
    $claim = [
        'iss' => $serviceAccount['client_email'],
        'scope' => 'https://www.googleapis.com/auth/calendar',
        'aud' => $tokenUri,
        'iat' => $now,
        'exp' => $now + 3600,
    ];

    $unsigned = app_base64url_json($header) . '.' . app_base64url_json($claim);
    $signature = '';
    $signed = openssl_sign($unsigned, $signature, $serviceAccount['private_key'], OPENSSL_ALGO_SHA256);
    if (!$signed) {
        return null;
    }

    $assertion = $unsigned . '.' . app_base64url_encode($signature);
    $ch = curl_init($tokenUri);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
        CURLOPT_POSTFIELDS => http_build_query([
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $assertion,
        ]),
        CURLOPT_TIMEOUT => 12,
    ]);

    $raw = curl_exec($ch);
    $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ($raw === false || $status < 200 || $status >= 300) {
        return null;
    }

    $data = json_decode((string) $raw, true);
    return is_array($data) && isset($data['access_token']) ? (string) $data['access_token'] : null;
}

function app_http_json(string $method, string $url, array $payload, array $headers = []): array
{
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => array_merge(['Content-Type: application/json'], $headers),
        CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE),
        CURLOPT_TIMEOUT => 12,
    ]);

    $raw = curl_exec($ch);
    $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);

    $body = json_decode((string) $raw, true);

    return [
        'status' => $status,
        'body' => $body,
        'raw' => $raw !== false ? $raw : $error,
    ];
}

function app_calendar_description(array $reservation): string
{
    return implode("\n", [
        'Jméno: ' . $reservation['name'],
        'Telefon: ' . $reservation['phone'],
        'E-mail: ' . $reservation['email'],
        'Služba: ' . $reservation['service'],
        'Cena: ' . app_price_label($reservation['service']),
        'Poznámka: ' . (($reservation['note'] ?? '') !== '' ? $reservation['note'] : '-'),
        '',
        'Vytvořeno z rezervačního systému.',
    ]);
}

function app_base64url_json(array $data): string
{
    return app_base64url_encode(json_encode($data, JSON_UNESCAPED_SLASHES));
}

function app_base64url_encode(string $data): string
{
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}
