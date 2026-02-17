<?php

header('Content-Type: text/html; charset=utf-8');

$file = __DIR__ . '/comments.json';
if (!file_exists($file)) {
    echo '<p>Ei vielä kommentteja.</p>';
    exit;
}

$contents = file_get_contents($file);
$comments = json_decode($contents, true);
if (!is_array($comments) || count($comments) === 0) {
    echo '<p>Ei vielä kommentteja.</p>';
    exit;
}

$comments = array_reverse($comments);

function esc($s) {
    return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

echo '<div class="comments">';
foreach ($comments as $c) {
    $name = esc($c['name'] ?? 'Tuntematon');
    $message = nl2br(esc($c['message'] ?? ''));
    $time = esc($c['createdAt'] ?? '');
    echo "<article class=\"comment\">";
    echo "<p class=\"comment-meta\"><strong>{$name}</strong> — <time datetime=\"{$time}\">{$time}</time></p>";
    echo "<div class=\"comment-body\">{$message}</div>";
    echo "</article>";
}
echo '</div>';
?>