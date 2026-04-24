<?php

require_once __DIR__ . '/config.php';

$storageDir = __DIR__ . '/storage';
if (!is_dir($storageDir)) {
    mkdir($storageDir, 0755, true);
}

$pdo = new PDO('sqlite:' . $storageDir . '/reservations.sqlite');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

$pdo->exec('
    CREATE TABLE IF NOT EXISTS reservations (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        email TEXT NOT NULL,
        phone TEXT NOT NULL,
        date TEXT NOT NULL,
        time TEXT NOT NULL,
        service TEXT NOT NULL,
        price INTEGER NOT NULL,
        duration INTEGER NOT NULL DEFAULT 30,
        note TEXT,
        gdpr_accepted INTEGER NOT NULL DEFAULT 1,
        owner_email_sent INTEGER NOT NULL DEFAULT 0,
        customer_email_sent INTEGER NOT NULL DEFAULT 0,
        calendar_event_id TEXT,
        integration_errors TEXT,
        status TEXT NOT NULL DEFAULT "pending",
        accepted_at TEXT,
        created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
    )
');

$columns = array_column($pdo->query('PRAGMA table_info(reservations)')->fetchAll(), 'name');
if (!in_array('duration', $columns, true)) {
    $pdo->exec('ALTER TABLE reservations ADD COLUMN duration INTEGER NOT NULL DEFAULT 30');
}
if (!in_array('owner_email_sent', $columns, true)) {
    $pdo->exec('ALTER TABLE reservations ADD COLUMN owner_email_sent INTEGER NOT NULL DEFAULT 0');
}
if (!in_array('customer_email_sent', $columns, true)) {
    $pdo->exec('ALTER TABLE reservations ADD COLUMN customer_email_sent INTEGER NOT NULL DEFAULT 0');
}
if (!in_array('calendar_event_id', $columns, true)) {
    $pdo->exec('ALTER TABLE reservations ADD COLUMN calendar_event_id TEXT');
}
if (!in_array('integration_errors', $columns, true)) {
    $pdo->exec('ALTER TABLE reservations ADD COLUMN integration_errors TEXT');
}
if (!in_array('status', $columns, true)) {
    $pdo->exec("ALTER TABLE reservations ADD COLUMN status TEXT NOT NULL DEFAULT 'accepted'");
}
if (!in_array('accepted_at', $columns, true)) {
    $pdo->exec('ALTER TABLE reservations ADD COLUMN accepted_at TEXT');
}

$pdo->exec('
    CREATE UNIQUE INDEX IF NOT EXISTS reservations_unique_slot
    ON reservations (date, time)
');

$pdo->exec('
    CREATE INDEX IF NOT EXISTS reservations_date_time_idx
    ON reservations (date, time)
');
