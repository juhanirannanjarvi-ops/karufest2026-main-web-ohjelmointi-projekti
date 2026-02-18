<?php
mysqli_report(MYSQLI_REPORT_ALL ^ MYSQLI_REPORT_INDEX);
try{
    $yhteys=mysqli_connect("db", "root", "password", "vieraskirja");
}
catch(Exception $e){
    header("Location:../html/yhteysvirhe.html");
    exit;
}
$tulos=mysqli_query($yhteys, "select * from kommentti");

while ($rivi=mysqli_fetch_object($tulos)){
    $kommentti=new class{};
    $kommentti->id=$rivi->id;
    $kommentti->laji=$rivi->laji;
    $kommentti->paino=$rivi->paino;
    $kommentti[]=$kommentti;
}
mysqli_close($yhteys);
print json_encode($kommentti);
?>