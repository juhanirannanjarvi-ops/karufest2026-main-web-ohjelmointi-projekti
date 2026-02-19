<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$yhteys = mysqli_connect("db", "root", "password", "vieraskirja");
if (!$yhteys) die(json_encode(["error" => "Tietokantayhteys epäonnistui"]));

$data = json_decode(file_get_contents("php://input"), true);
if (!$data || empty($data['name']) || empty($data['message'])) {
    http_response_code(400);
    echo json_encode(["error" => "Täytä kaikki kentät"]);
    exit;
}

$nimi = substr($data['name'], 0, 50);       // max 50 chars
$kommentti = substr($data['message'], 0, 1000); // max 1000 chars

$stmt = mysqli_prepare($yhteys,
    "INSERT INTO kommentti (nimi, kommentti) VALUES (?, ?)"
);
mysqli_stmt_bind_param($stmt, "ss", $nimi, $kommentti);
mysqli_stmt_execute($stmt);

mysqli_close($yhteys);

echo json_encode(["status" => "success"]);
?>