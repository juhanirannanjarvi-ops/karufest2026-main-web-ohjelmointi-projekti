<?php
// Asetetaan otsikko JSON-muodossa
header("Content-Type: application/json; charset=utf-8");

// Poistetaan MySQLi-varoitukset, jotta ei tulostu ylimääräistä
mysqli_report(MYSQLI_REPORT_OFF);

// Tietokantayhteyden tiedot
$host = 'db';
$user = 'root';
$pass = 'password';
$db   = 'vieraskirja';

// Yhdistetään MySQL-tietokantaan
$yhteys = mysqli_connect($host, $user, $pass, $db);

// Jos yhteys epäonnistuu, palautetaan tyhjä taulukko
if (!$yhteys) {
    echo json_encode([]);
    exit;
}

// Asetetaan merkistö UTF-8 (erikoismerkkien tuki)
$yhteys->set_charset('utf8mb4');

// Haetaan kaikki kommentit, uusimmat ensin
$tulos = mysqli_query($yhteys, "SELECT id, nimi, kommentti FROM kommentti ORDER BY id DESC");

// Luodaan taulukko kommentteja varten
$kommentit = [];

// Käydään tulokset läpi ja lisätään taulukkoon
while ($rivi = mysqli_fetch_assoc($tulos)) {
    $kommentit[] = $rivi;
}

// Suljetaan tietokantayhteys
mysqli_close($yhteys);

// Muutetaan taulukko JSON-muotoon ja tulostetaan
echo json_encode($kommentit);
?>