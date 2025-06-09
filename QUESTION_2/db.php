<?php
// Cambia estos datos por los de tu servidor Siteground
$host = '34.174.88.255';
$user = 'uhfgyb3ng2h74';
$pass = 'ywdp6iyev2i6';
$dbname = 'dbhk1sgsk1ceke';

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
?>
