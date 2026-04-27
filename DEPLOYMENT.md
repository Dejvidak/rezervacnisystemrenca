# Nasazení na hosting

Tento soubor je checklist pro produkční spuštění webu Hair By ReneNeme.

## 1. Co neposílat do Gitu

Tyto soubory musí zůstat jen lokálně nebo na hostingu:

- `local_config.php`
- `rezervacnisystemrenca-*.json`
- `storage/*.sqlite`
- `reservations/*.csv`
- `.env`

V repozitáři má být pouze `local_config.example.php` bez reálných hesel.

## 2. Produkční konfigurace

Na hostingu vytvoř soubor `local_config.php` podle `local_config.example.php` a nastav:

```php
putenv('SITE_URL=https://tvoje-domena.cz');
putenv('ADMIN_USER=...');
putenv('ADMIN_PASSWORD=...');
putenv('BOOKING_NOTIFY_EMAIL=...');
putenv('BOOKING_FROM_EMAIL=...');
putenv('SMTP_HOST=smtp.seznam.cz');
putenv('SMTP_PORT=465');
putenv('SMTP_ENCRYPTION=ssl');
putenv('SMTP_USERNAME=...');
putenv('SMTP_PASSWORD=...');
putenv('SMTP_FROM=...');
putenv('SMTP_FROM_NAME=Hair By ReneNeme');
putenv('GOOGLE_CALENDAR_ID=...');
putenv('GOOGLE_SERVICE_ACCOUNT_JSON=/absolute/path/rezervacnisystemrenca.json');
```

## 3. Práva souborů

Web server musí umět zapisovat do složky `storage/`, protože SQLite databáze je v:

```text
storage/reservations.sqlite
```

Složky `storage/` a `reservations/` musí zůstat neveřejné. Na Apache to řeší `.htaccess`.

## 4. Kontrola po nahrání

Po nasazení ověř:

- `https://tvoje-domena.cz/` vrací homepage.
- `https://tvoje-domena.cz/sitemap.xml` vrací XML sitemap.
- `https://tvoje-domena.cz/admin.php` chce přihlášení.
- `https://tvoje-domena.cz/local_config.php` není veřejně dostupný.
- `https://tvoje-domena.cz/storage/reservations.sqlite` není veřejně dostupný.
- `https://tvoje-domena.cz/rezervacnisystemrenca-....json` není veřejně dostupný.

## 5. Reálný test rezervace

1. Vytvoř testovací rezervaci jako zákazník.
2. Ověř, že přijde e-mail majiteli.
3. Ověř, že přijde e-mail zákazníkovi.
4. Otevři admin a zkontroluj, že rezervace čeká na přijetí.
5. Klikni na `Přijmout`.
6. Ověř, že se termín zapsal do Google Kalendáře.
7. Smaž událost v Google Kalendáři.
8. Znovu otevři admin a ověř, že se lokální rezervace synchronizací odstraní.

