<?php

// Zkopíruj tento soubor jako local_config.php a doplň reálné hodnoty.
// local_config.php je v .gitignore, aby se tajné údaje neposlaly do Gitu.

putenv('BOOKING_NOTIFY_EMAIL=majitel@example.com');
putenv('BOOKING_FROM_EMAIL=rezervace@example.com');
putenv('SITE_URL=https://tvoje-domena.cz');

// SMTP je spolehlivější než PHP mail().
// Pro Seznam se běžně používá smtp.seznam.cz, port 465 a SSL.
putenv('SMTP_HOST=smtp.seznam.cz');
putenv('SMTP_PORT=465');
putenv('SMTP_ENCRYPTION=ssl');
putenv('SMTP_USERNAME=majitel@example.com');
putenv('SMTP_PASSWORD=sem-dopln-heslo');
putenv('SMTP_FROM=majitel@example.com');
putenv('SMTP_FROM_NAME=Hair By ReneNeme');

// Google Calendar:
// 1) V Google Cloud vytvoř Service Account a stáhni JSON klíč.
// 2) Google kalendář nasdílej na client_email ze service accountu s právem upravovat události.
// 3) Sem vlož ID kalendáře a absolutní cestu k JSON souboru.
putenv('GOOGLE_CALENDAR_ID=tvoje-calendar-id@group.calendar.google.com');
putenv('GOOGLE_SERVICE_ACCOUNT_JSON=/absolute/path/google-service-account.json');
