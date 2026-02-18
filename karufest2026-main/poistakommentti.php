<?php
header('Content-Type: application/json; charset=utf-8');
mysqli_report(MYSQLI_REPORT_OFF);

$host = 'db';
$user = 'root';
$pass = 'password';
$db   = 'vieraskirja';

$raw = file_get_contents('php://input');
$contentType = $_SERVER['CONTENT_TYPE'] ?? '';

$id = null;
if (stripos($contentType, 'application/json') !== false) {
    $body = json_decode($raw, true);
    $id = isset($body['id']) ? $body['id'] : null;
} else {
    if (isset($_POST['id'])) {
        $id = $_POST['id'];
    } else {
        parse_str($raw, $parsed);
        $id = $parsed['id'] ?? null;
    }
}

if ($id === null || !is_numeric($id) || intval($id) <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Virheellinen id']);
    exit;
}
$id = intval($id);

$mysqli = mysqli_connect($host, $user, $pass, $db);
if (!$mysqli) {
    http_response_code(500);
    echo json_encode(['error' => 'Tietokantayhteys epäonnistui']);
    exit;
}
$mysqli->set_charset('utf8mb4');

$sql = 'DELETE FROM kommentti WHERE id = ?';
$stmt = mysqli_prepare($mysqli, $sql);
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['error' => 'Kyselyn valmistelu epäonnistui']);
    $mysqli->close();
    exit;
}
mysqli_stmt_bind_param($stmt, 'i', $id);
$ok = mysqli_stmt_execute($stmt);

if ($ok) {
    if (mysqli_stmt_affected_rows($stmt) > 0) {
        http_response_code(200);
        echo json_encode(['success' => true, 'id' => $id]);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Kommenttia ei löytynyt']);
    }
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Poisto epäonnistui']);
}

mysqli_stmt_close($stmt);
$mysqli->close();