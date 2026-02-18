<?php
header('Content-Type: application/json; charset=utf-8');
mysqli_report(MYSQLI_REPORT_OFF);

$host = 'db';
$user = 'root';
$pass = 'password';
$db   = 'vieraskirja';

function getIncomingJson(): string {
    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
    $raw = file_get_contents('php://input');

    if (stripos($contentType, 'application/json') !== false) {
        return $raw ?: '';
    }
   
    if (!empty($_POST['comment'])) {
        return $_POST['comment'];
    }

    parse_str($raw, $parsed);
    return $parsed['comment'] ?? '';
}


function tarkistaJson(string $json) {
    if (trim($json) === '') return false;
    $obj = json_decode($json, false);
    if (!is_object($obj)) return false;

    
    $name = $obj->name ?? $obj->nimi ?? null;
    $message = $obj->message ?? $obj->kommentti ?? null;

    if (empty($name) || empty($message)) return false;

    
    $obj->name = mb_substr((string)$name, 0, 25);
    $obj->message = mb_substr((string)$message, 0, 500);

    return $obj;
}


$json = getIncomingJson();
$inputObj = tarkistaJson($json);

if ($inputObj === false) {
    http_response_code(400);
    echo json_encode(['error' => 'Täytä nimi ja palaute (JSON puuttuu tai kentät tyhjiä).']);
    exit;
}

$mysqli = mysqli_connect($host, $user, $pass, $db);
if (!$mysqli) {
    http_response_code(500);
    echo json_encode(['error' => 'Tietokantayhteys epäonnistui.']);
    exit;
}
$mysqli->set_charset('utf8mb4');

$sql = 'INSERT INTO kommentti (nimi, kommentti) VALUES (?, ?)';
$stmt = mysqli_prepare($mysqli, $sql);
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['error' => 'Tietokantakyselyn valmistelu epäonnistui.']);
    $mysqli->close();
    exit;
}


$nimi = $inputObj->name;
$kommentti = $inputObj->message;
mysqli_stmt_bind_param($stmt, 'ss', $nimi, $kommentti);

$ok = mysqli_stmt_execute($stmt);
if ($ok) {
    $insertId = mysqli_insert_id($mysqli);
    http_response_code(201);
    echo json_encode(['success' => true, 'id' => $insertId]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Tallennus epäonnistui.']);
}

mysqli_stmt_close($stmt);
$mysqli->close();