# E-mail a Google Kalendář

Rezervace se vždy nejdřív uloží do lokální SQLite databáze. E-mail a Google Kalendář jsou navazující integrace: když nejsou nastavené nebo selžou, rezervace zůstane uložená a chyba se zobrazí v adminu.

## Lokální nastavení

1. Zkopíruj `local_config.example.php` jako `local_config.php`.
2. V `local_config.php` doplň reálné hodnoty.
3. `local_config.php` neposílej do Gitu, je v `.gitignore`.

## E-mail

Nastav:

```php
putenv('BOOKING_NOTIFY_EMAIL=majitel@example.com');
putenv('BOOKING_FROM_EMAIL=rezervace@example.com');
putenv('SITE_URL=https://tvoje-domena.cz');
```

`BOOKING_NOTIFY_EMAIL` je adresa majitele, kam přijde nová rezervace.
`BOOKING_FROM_EMAIL` musí být ideálně adresa z domény webu, aby ji hosting neblokoval.

Poznámka: používá se PHP `mail()`. Na běžném hostingu často funguje, lokálně na počítači většinou ne.

Spolehlivější varianta je SMTP. Pokud nastavíš `SMTP_HOST`, systém použije SMTP místo `mail()`:

```php
putenv('SMTP_HOST=smtp.seznam.cz');
putenv('SMTP_PORT=465');
putenv('SMTP_ENCRYPTION=ssl');
putenv('SMTP_USERNAME=majitel@example.com');
putenv('SMTP_PASSWORD=heslo-k-emailu');
putenv('SMTP_FROM=majitel@example.com');
putenv('SMTP_FROM_NAME=Hair By ReneNeme');
```

## Google Kalendář

1. V Google Cloud vytvoř projekt.
2. Zapni Google Calendar API.
3. Vytvoř Service Account a stáhni JSON klíč.
4. V Google Kalendáři nasdílej cílový kalendář na `client_email` ze staženého JSON souboru.
5. Dej service accountu právo upravovat události.
6. Nastav:

```php
putenv('GOOGLE_CALENDAR_ID=tvoje-calendar-id@group.calendar.google.com');
putenv('GOOGLE_SERVICE_ACCOUNT_JSON=/absolute/path/google-service-account.json');
```

`GOOGLE_SERVICE_ACCOUNT_JSON` může být cesta k JSON souboru nebo přímo celý JSON obsah.

## Kde vidět výsledek

V `admin.php` je sloupec `Integrace`. Ukazuje:

- jestli odešel e-mail majiteli
- jestli odešel e-mail zákazníkovi
- jestli vznikla událost v Google Kalendáři
- případnou chybovou hlášku
