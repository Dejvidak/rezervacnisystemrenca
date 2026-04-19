<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// Pomocná funkce na očištění vstupu
function safe($key) {
    return htmlspecialchars(trim($_POST[$key] ?? ''), ENT_QUOTES, 'UTF-8');
}

// Načtení dat z formuláře
$name    = safe('name');
$email   = safe('email');
$phone   = safe('phone');
$date    = safe('date');
$time    = safe('time');
$service = safe('service');
$note    = safe('note');

// Mapování ceny podle služby
$prices = [
    "Klasický střih"       => 350,
    "Skin fade + styling"  => 450,
    "Střih + vousy"        => 550,
    "Úprava vousů"         => 250
];

$price = $prices[$service] ?? null;

// Základní validace
if (!$name || !$email || !$phone || !$date || !$time || !$service) {
    $error = "Některé povinné údaje chybí. Zkuste to prosím znovu.";
} else {
    $error = '';
}

// Uložení do CSV
if (!$error) {
    $folder = __DIR__ . "/reservations";
    if (!is_dir($folder)) {
        mkdir($folder, 0777, true);
    }

    $file = $folder . "/reservations.csv";

    $dataRow = [
        date("Y-m-d H:i:s"),
        $name,
        $email,
        $phone,
        $date,
        $time,
        $service,
        $price,
        $note
    ];

    $f = fopen($file, 'a');
if ($f) {
    
    if (filesize($file) === 0) {
        fputcsv(
            $f,
            [
                'Vytvořeno',
                'Jméno',
                'E-mail',
                'Telefon',
                'Datum',
                'Čas',
                'Služba',
                'Cena',
                'Poznámka'
            ],
            ';',
            '"',
            '\\'
        );
    }


    fputcsv(
        $f,
        $dataRow,
        ';',
        '"',
        '\\'
    );

    fclose($f);
} else {
    $error = "Nepodařilo se uložit data do souboru.";
}

}
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Potvrzení rezervace</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen bg-[#F5EDE1] text-[#231814] flex items-center justify-center p-4">
    <div class="max-w-lg w-full bg-[#6B5947] rounded-3xl p-6 border border-[#3F332A] shadow-lg text-[#F5EDE1]">
        
        <?php if ($error): ?>
            <h1 class="text-2xl font-bold text-red-300">Chyba!</h1>
            <p class="mt-3 text-sm"><?php echo $error; ?></p>
        <?php else: ?>
            <h1 class="text-2xl font-bold text-[#F5EDE1]">Rezervace odeslána!</h1>
            <p class="mt-3 text-sm">
                Díky, <span class="font-semibold"><?php echo $name; ?></span>.
                Vaše rezervace byla úspěšně zaznamenána.
            </p>

            <div class="mt-4 bg-[#4F4036] p-4 rounded-2xl border border-[#3F332A] text-sm">
                <h2 class="text-lg font-semibold mb-2">Shrnutí rezervace:</h2>
                <p><strong>Služba:</strong> <?php echo $service; ?> (<?php echo $price; ?> Kč)</p>
                <p><strong>Datum:</strong> <?php echo $date; ?></p>
                <p><strong>Čas:</strong> <?php echo $time; ?></p>
                <p><strong>Telefon:</strong> <?php echo $phone; ?></p>
                <p><strong>E-mail:</strong> <?php echo $email; ?></p>
                <?php if ($note): ?>
                    <p><strong>Poznámka:</strong> <?php echo nl2br($note); ?></p>
                <?php endif; ?>
            </div>

            <p class="text-xs mt-3 text-[#F0E7DB]">
                Data jsou uložena v: <code>reservations/reservations.csv</code>
            </p>
        <?php endif; ?>

        <a href="index.php#booking"
           class="block mt-6 text-center bg-[#2E7D5A] hover:bg-[#245F44] text-[#F5EDE1] font-semibold py-2 rounded-full transition">
            Zpět na formulář
        </a>
    </div>
</body>
</html>

