
<?php
$dbhost = "localhost";
$dbuser = "root";
$dbpass = "";
$dbname = "my_campus";

$connection = mysqli_connect(
    $dbhost,
    $dbuser,
    $dbpass,
    $dbname
);

if (!$connection) {
    die(
        "Koneksi gagal: " .
        mysqli_connect_errno() . " - " .
        mysqli_connect_error()
    );
}
?>
