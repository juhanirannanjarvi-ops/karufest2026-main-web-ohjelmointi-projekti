<?php
// Ota käyttöön MySQLi-virheraportointi (poikkeukset) paremman virheidenhallinnan vuoksi
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Yhdistä tietokantaan
$yhteys = mysqli_connect("db", "root", "password", "vieraskirja");

// Jos yhteys epäonnistuu, lopetetaan ja palautetaan JSON-virhe
if (!$yhteys) die(json_encode(["error" => "Tietokantayhteys epäonnistui"]));

// Luetaan POST-pyynnön sisältö (JSON)
$data = json_decode(file_get_contents("php://input"), true);

// Tarkistetaan, että tarvittavat kentät on lähetetty
if (!$data || empty($data['name']) || empty($data['message'])) {
    http_response_code(400); // Huono pyyntö
    echo json_encode(["error" => "Täytä kaikki kentät"]);
    exit;
}

// Rajataan syöte enimmäispituuksiin
$nimi = substr($data['name'], 0, 50);           // Nimi max 50 merkkiä
$kommentti = substr($data['message'], 0, 1000); // Kommentti max 1000 merkkiä

// Valmistellaan SQL-lause turvallisesti (estää SQL-injektioita)
$stmt = mysqli_prepare(
    $yhteys,
    "INSERT INTO kommentti (nimi, kommentti) VALUES (?, ?)"
);

// Liitetään muuttujat ja suoritetaan kysely
mysqli_stmt_bind_param($stmt, "ss", $nimi, $kommentti);
mysqli_stmt_execute($stmt);

// Suljetaan kysely ja yhteys tietokantaan
mysqli_stmt_close($stmt);
mysqli_close($yhteys);

// Palautetaan onnistumisviesti JSON-muodossa
header("Content-Type: application/json");
echo json_encode(["status" => "success"]);
?>