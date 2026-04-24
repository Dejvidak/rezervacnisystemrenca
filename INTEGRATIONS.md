# E-mail a Google Kalendář

Rezervace se vždy nejdřív ověří proti lokální SQLite databázi i Google Kalendáři. Nová rezervace se uloží jako čekající a majiteli přijde e-mail. Teprve po přijetí v administraci se vytvoří událost v Google Kalendáři a zákazníkovi odejde potvrzovací e-mail.

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

Když je Google Kalendář nastavený, systém:

- při načítání časů schová sloty, které se překrývají s událostmi v Google Kalendáři
- při odeslání rezervace Google Kalendář zkontroluje znovu, aby se nezapsal nově obsazený čas
- po přijetí rezervace v adminu vytvoří v kalendáři událost
- při smazání rezervace z adminu smaže i událost, kterou systém vytvořil

## Kde vidět výsledek

V `admin.php` je sloupec `Integrace`. Ukazuje:

- jestli odešel e-mail majiteli
- jestli odešel potvrzovací e-mail zákazníkovi po přijetí
- jestli vznikla událost v Google Kalendáři po přijetí
- případnou chybovou hlášku

Potvrzovací e-mail zákazníkovi se posílá jako HTML karta s přehledem termínu, služby, ceny, adresou, telefonem, e-mailem a odkazem na mapu. Součástí e-mailu je i textová fallback verze pro jednodušší e-mailové klienty.

Nad tabulkou rezervací je také vlastní týdenní kalendářový přehled. Ten nepoužívá Google iframe, takže nezávisí na přihlášení do Google účtu ani na cookies v prohlížeči.

V tabulce rezervací jde čekající rezervaci přijmout nebo smazat. Smazání odstraní i událost v Google Kalendáři, pokud ji systém předtím vytvořil.
