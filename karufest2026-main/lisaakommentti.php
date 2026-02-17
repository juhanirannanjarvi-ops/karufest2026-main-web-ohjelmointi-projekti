<?php
header('Content-Type: application/json; charset=utf-8');

$file = __DIR__ . '/comments.json';

$json = isset($_POST['comment']) ? $_POST['comment'] : '';

if (!($comment = tarkistaJson($json))) {
    http_response_code(400);
    echo json_encode(['error' => 'Täytä kaikki kentät']);
    exit;
}


$entry = [
    'id' => (string)round(microtime(true) * 1000),
    'name' => mb_substr($comment->name, 0, 25),
    'message' => mb_substr($comment->message, 0, 500),
    'createdAt' => gmdate('c')
];

if (!file_exists($file)) {
    file_put_contents($file, json_encode([]));
}

$fp = fopen($file, 'c+');
if (!$fp) {
    http_response_code(500);
    echo json_encode(['error' => 'Tallennus epäonnistui']);
    exit;
}

flock($fp, LOCK_EX);
$contents = stream_get_contents($fp);
$comments = json_decode($contents, true);
if (!is_array($comments)) $comments = [];
$comments[] = $entry;
ftruncate($fp, 0);
rewind($fp);
fwrite($fp, json_encode($comments, JSON_UNESCAPED_UNICODE));
fflush($fp);
flock($fp, LOCK_UN);
fclose($fp);

http_response_code(201);
echo json_encode($entry, JSON_UNESCAPED_UNICODE);
exit;

function tarkistaJson($json) {
    if (empty($json)) return false;
    $obj = json_decode($json);
    if (!is_object($obj)) return false;
    $name = trim((string)($obj->name ?? ''));
    $message = trim((string)($obj->message ?? ''));
    if ($name === '' || $message === '') return false;
    return $obj;
}
?>