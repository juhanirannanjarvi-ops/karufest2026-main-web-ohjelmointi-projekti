<?php
mysqli_report(MYSQLI_REPORT_ALL ^ MYSQLI_REPORT_INDEX);

try {
    
    $yhteys = mysqli_connect("db", "root", "password", "vieraskirja");
}
catch (Exception $e) {
    header("Location:../html/yhteysvirhe.html");
    exit;
}

$poistettava = isset($_GET["poistettava"]) ? $_GET["poistettava"] : "";


if (!empty($poistettava)) {

    $sql = "DELETE FROM kommentti WHERE id=?";
    $stmt = mysqli_prepare($yhteys, $sql);

    mysqli_stmt_bind_param($stmt, "i", $poistettava);

    mysqli_stmt_execute($stmt);
}

mysqli_close($yhteys);

header("Location:./luekommentit.php");
exit;
?>


mysqli_stmt_close($stmt);
$mysqli->close();
