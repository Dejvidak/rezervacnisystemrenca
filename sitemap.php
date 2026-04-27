<?php
require_once __DIR__ . '/config.php';

$pages = [
    [
        'loc' => app_absolute_url(''),
        'file' => __DIR__ . '/index.php',
        'changefreq' => 'weekly',
        'priority' => '1.0',
    ],
    [
        'loc' => app_absolute_url('cenik.php'),
        'file' => __DIR__ . '/cenik.php',
        'changefreq' => 'weekly',
        'priority' => '0.9',
    ],
    [
        'loc' => app_absolute_url('references.php'),
        'file' => __DIR__ . '/references.php',
        'changefreq' => 'monthly',
        'priority' => '0.8',
    ],
    [
        'loc' => app_absolute_url('contact.php'),
        'file' => __DIR__ . '/contact.php',
        'changefreq' => 'monthly',
        'priority' => '0.7',
    ],
];

header('Content-Type: application/xml; charset=UTF-8');

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach ($pages as $page): ?>
    <url>
        <loc><?= htmlspecialchars($page['loc'], ENT_XML1 | ENT_QUOTES, 'UTF-8') ?></loc>
        <lastmod><?= date('Y-m-d', is_file($page['file']) ? filemtime($page['file']) : time()) ?></lastmod>
        <changefreq><?= htmlspecialchars($page['changefreq'], ENT_XML1 | ENT_QUOTES, 'UTF-8') ?></changefreq>
        <priority><?= htmlspecialchars($page['priority'], ENT_XML1 | ENT_QUOTES, 'UTF-8') ?></priority>
    </url>
<?php endforeach; ?>
</urlset>
