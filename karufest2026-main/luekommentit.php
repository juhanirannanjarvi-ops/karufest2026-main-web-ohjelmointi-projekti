<?php
$yhteys = mysqli_connect("db", "root", "password", "vieraskirja");
if (!$yhteys) die(json_encode([]));

$tulos = mysqli_query($yhteys, "SELECT id, nimi, kommentti FROM kommentti ORDER BY id DESC");

$kommentit = [];
while ($rivi = mysqli_fetch_assoc($tulos)) {
    $kommentit[] = $rivi;
}

mysqli_close($yhteys);

header("Content-Type: application/json");
echo json_encode($kommentit);
?>