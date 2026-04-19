<?php
require __DIR__ . '/db.php';

// Získáme datum z GET parametru, pokud existuje
$date = $_GET['date'] ?? null;

if ($date) {
    // Rezervace jen pro vybraný den
    $stmt = $pdo->prepare("SELECT * FROM reservations WHERE date = :date ORDER BY time ASC");
    $stmt->execute([':date' => $date]);
} else {
    // Všechny rezervace od dneška
    $today = (new DateTime('today'))->format('Y-m-d');
    $stmt = $pdo->prepare("SELECT * FROM reservations WHERE date >= :today ORDER BY date ASC, time ASC");
    $stmt->execute([':today' => $today]);
}

$reservations = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Admin – Rezervace</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#1F1B18] text-[#F5EDE1] min-h-screen">

    <div class="max-w-5xl mx-auto py-10 px-4">
        <h1 class="text-3xl font-bold mb-6">Admin panel – Rezervace 💈</h1>

        <!-- Filtrování podle data -->
        <form method="get" class="mb-6 flex gap-3 items-center">
            <label class="text-sm">Vybrat datum:</label>
            <input type="date" name="date" value="<?= htmlspecialchars($date ?? '') ?>" class="px-3 py-2 rounded bg-[#3F332A] border border-[#6A654E]">
            <button class="px-4 py-2 rounded bg-[#C9BFA7] text-[#1F1B18] font-semibold">Filtrovat</button>
            <a href="admin.php" class="text-sm underline ml-2">Zobrazit vše</a>
        </form>

        <?php if (empty($reservations)): ?>
            <p class="text-lg">Žádné rezervace nenalezeny.</p>
        <?php else: ?>
        
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm border border-[#6A654E]">
                <thead class="bg-[#3F332A]">
                    <tr>
                        <th class="px-3 py-2 border-b border-[#6A654E] text-left">Datum</th>
                        <th class="px-3 py-2 border-b border-[#6A654E] text-left">Čas</th>
                        <th class="px-3 py-2 border-b border-[#6A654E] text-left">Jméno</th>
                        <th class="px-3 py-2 border-b border-[#6A654E] text-left">Telefon</th>
                        <th class="px-3 py-2 border-b border-[#6A654E] text-left">Služba</th>
                        <th class="px-3 py-2 border-b border-[#6A654E] text-left">Cena</th>
                        <th class="px-3 py-2 border-b border-[#6A654E] text-left">Poznámka</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reservations as $r): ?>
                        <tr class="odd:bg-[#2A231E] even:bg-[#241E1A]">
                            <td class="px-3 py-2 border-b border-[#3F332A]"><?= htmlspecialchars($r['date']) ?></td>
                            <td class="px-3 py-2 border-b border-[#3F332A]"><?= htmlspecialchars($r['time']) ?></td>
                            <td class="px-3 py-2 border-b border-[#3F332A]"><?= htmlspecialchars($r['name']) ?></td>
                            <td class="px-3 py-2 border-b border-[#3F332A]"><?= htmlspecialchars($r['phone']) ?></td>
                            <td class="px-3 py-2 border-b border-[#3F332A]"><?= htmlspecialchars($r['service']) ?></td>
                            <td class="px-3 py-2 border-b border-[#3F332A]"><?= htmlspecialchars($r['price']) ?> Kč</td>
                            <td class="px-3 py-2 border-b border-[#3F332A]"><?= nl2br(htmlspecialchars($r['note'] ?? '')) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php endif; ?>

    </div>

</body>
</html>
