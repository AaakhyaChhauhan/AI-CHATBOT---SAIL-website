<?php
$host = 'sql12.freesqldatabase.com';
$db   = 'sql12787890';
$user = 'sql12787890';
$pass = 'ZMLhL9LGUN';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
