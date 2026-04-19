<?php
// Napojení na databázi
require __DIR__ . '/db.php';

// 1) Data z formuláře – názvy MUSÍ sedět s name="" v index.php
$name    = trim($_POST['name']    ?? '');
$email   = trim($_POST['email']   ?? '');
$phone   = trim($_POST['phone']   ?? '');
$date    = trim($_POST['date']    ?? '');
$time    = trim($_POST['time']    ?? '');
$service = trim($_POST['service'] ?? '');
$note    = trim($_POST['note']    ?? '');

// checkbox – podle toho jak se jmenuje v tvém formuláři
$gdpr    = isset($_POST['gdpr']);   // jestli máš name="gdpr"

// 2) Ceník – uprav podle toho, co máš na webu
$prices = [
    'Klasický střih'        => 350,
    'Skin fade + styling'   => 450,
    'Střih + vousy'         => 550,
    'Úprava vousů'          => 250,
];

$price = $prices[$service] ?? null;

// 3) Validace
$errors = [];

if ($name === '')   $errors[] = 'Jméno je povinné.';
if ($email === '')  $errors[] = 'E-mail je povinný.';
if ($phone === '')  $errors[] = 'Telefon je povinný.';
if ($date === '')   $errors[] = 'Datum je povinné.';
if ($time === '')   $errors[] = 'Čas je povinný.';
if ($service === '' || $price === null) $errors[] = 'Musíš vybrat platnou službu.';
if (!$gdpr)         $errors[] = 'Musíš souhlasit se zpracováním osobních údajů.';

// kontrola minulého data
$today = (new DateTime('today'))->format('Y-m-d');
if ($date < $today) {
    $errors[] = 'Nemůžeš si rezervovat termín v minulosti.';
}

if (!empty($errors)) {
    // jednoduchá error stránka
    ?>
    <!DOCTYPE html>
    <html lang="cs">
    <head>
        <meta charset="UTF-8">
        <title>Chyba rezervace</title>
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="bg-[#1F1B18] text-[#F5EDE1] min-h-screen flex items-center justify-center">
        <div class="bg-[#3F332A] border border-[#6A654E] rounded-2xl px-8 py-6 max-w-md w-full shadow-xl">
            <h1 class="text-2xl font-bold mb-4">Něco se nepovedlo 😕</h1>
            <ul class="list-disc list-inside space-y-1 text-sm">
                <?php foreach ($errors as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
            <a href="index.php#reservation" class="inline-block mt-6 text-sm px-4 py-2 rounded-lg bg-[#C9BFA7] text-[#1F1B18] font-semibold hover:bg-[#E0D6BD]">
                Zpět na formulář
            </a>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// 4) Uložení do databáze
$stmt = $pdo->prepare("
    INSERT INTO reservations (name, email, phone, date, time, service, price, note)
    VALUES (:name, :email, :phone, :date, :time, :service, :price, :note)
");

$stmt->execute([
    ':name'    => $name,
    ':email'   => $email,
    ':phone'   => $phone,
    ':date'    => $date,
    ':time'    => $time,
    ':service' => $service,
    ':price'   => $price,
    ':note'    => $note === '' ? null : $note,
]);

// 5) Thank you stránka
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Potvrzení rezervace</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#1F1B18] text-[#F5EDE1] min-h-screen flex items-center justify-center">
    <div class="bg-[#3F332A] border border-[#6A654E] rounded-2xl px-8 py-6 max-w-md w-full shadow-xl">
        <h1 class="text-2xl font-bold mb-4">Rezervace odeslána! 💈</h1>
        <p class="mb-2">Díky, <strong><?= htmlspecialchars($name) ?></strong>. Tvoje rezervace byla úspěšně zaznamenána.</p>

        <h2 class="text-lg font-semibold mt-4 mb-2">Shrnutí rezervace:</h2>
        <ul class="text-sm space-y-1">
            <li><strong>Služba:</strong> <?= htmlspecialchars($service) ?> (<?= $price ?> Kč)</li>
            <li><strong>Datum:</strong> <?= htmlspecialchars($date) ?></li>
            <li><strong>Čas:</strong> <?= htmlspecialchars($time) ?></li>
            <li><strong>Telefon:</strong> <?= htmlspecialchars($phone) ?></li>
            <li><strong>E-mail:</strong> <?= htmlspecialchars($email) ?></li>
            <?php if ($note): ?>
                <li><strong>Poznámka:</strong> <?= nl2br(htmlspecialchars($note)) ?></li>
            <?php endif; ?>
        </ul>

        <a href="index.php" class="inline-block mt-6 text-sm px-4 py-2 rounded-lg border border-[#6A654E] hover:bg-[#2A231E]">
            Zpět na web
        </a>
    </div>
</body>
</html>
