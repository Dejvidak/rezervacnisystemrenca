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

    $ownerEmail = app_owner_email();
    $result['owner_email_sent'] = app_send_plain_email(
        $ownerEmail,
        'Nová rezervace: ' . $reservation['name'] . ' - ' . $reservation['date'] . ' ' . $reservation['time'],
        app_owner_email_body($reservation),
        $reservation['email']
    );

    if (!$result['owner_email_sent']) {
        $result['errors'][] = 'E-mail majiteli se nepodařilo odeslat.';
    }

    if (filter_var($reservation['email'] ?? '', FILTER_VALIDATE_EMAIL)) {
        $result['customer_email_sent'] = app_send_plain_email(
            $reservation['email'],
            'Potvrzení rezervace - Hair By ReneNeme',
            app_customer_email_body($reservation),
            $ownerEmail
        );

        if (!$result['customer_email_sent']) {
            $result['errors'][] = 'Potvrzovací e-mail zákazníkovi se nepodařilo odeslat.';
        }
    }

    return $result;
}

function app_send_plain_email(string $to, string $subject, string $body, ?string $replyTo = null): bool
{
    if (trim((string) getenv('SMTP_HOST')) !== '') {
        return app_send_smtp_email($to, $subject, $body, $replyTo);
    }

    $headers = [
        'MIME-Version: 1.0',
        'Content-Type: text/plain; charset=UTF-8',
        'From: Hair By ReneNeme <' . app_from_email() . '>',
    ];

    if ($replyTo !== null && filter_var($replyTo, FILTER_VALIDATE_EMAIL)) {
        $headers[] = 'Reply-To: ' . $replyTo;
    }

    return mail($to, '=?UTF-8?B?' . base64_encode($subject) . '?=', $body, implode("\r\n", $headers));
}

function app_send_smtp_email(string $to, string $subject, string $body, ?string $replyTo = null): bool
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

    $message = app_build_smtp_message($to, $subject, $body, $from, $fromName, $replyTo);
    fwrite($socket, app_dot_stuff($message) . "\r\n.\r\n");
    $ok = app_smtp_expect($socket, [250]);

    app_smtp_command($socket, 'QUIT', [221]);
    fclose($socket);

    return $ok;
}

function app_build_smtp_message(string $to, string $subject, string $body, string $from, string $fromName, ?string $replyTo): string
{
    $headers = [
        'Date: ' . date(DATE_RFC2822),
        'From: ' . app_encode_header($fromName) . ' <' . $from . '>',
        'To: <' . $to . '>',
        'Subject: ' . app_encode_header($subject),
        'MIME-Version: 1.0',
        'Content-Type: text/plain; charset=UTF-8',
        'Content-Transfer-Encoding: 8bit',
    ];

    if ($replyTo !== null && filter_var($replyTo, FILTER_VALIDATE_EMAIL)) {
        $headers[] = 'Reply-To: <' . $replyTo . '>';
    }

    return implode("\r\n", $headers) . "\r\n\r\n" . str_replace(["\r\n", "\r"], "\n", $body);
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
    return implode("\n", [
        'Nová rezervace',
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
        'Admin: ' . app_site_url() . '/admin.php',
    ]);
}

function app_customer_email_body(array $reservation): string
{
    return implode("\n", [
        'Dobrý den,',
        '',
        'děkujeme za rezervaci. Tady je její shrnutí:',
        '',
        'Datum: ' . $reservation['date'],
        'Čas: ' . $reservation['time'],
        'Služba: ' . $reservation['service'],
        'Délka: ' . $reservation['duration'] . ' min',
        'Cena: ' . app_price_label($reservation['service']),
        '',
        'Hair By ReneNeme',
        'Vackova 1064/39, 612 00 Brno-Královo Pole',
    ]);
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
        'scope' => 'https://www.googleapis.com/auth/calendar.events',
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
