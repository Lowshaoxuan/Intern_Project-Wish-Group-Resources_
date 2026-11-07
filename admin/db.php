<?php
// db.php
$host = "localhost";
$user = "root"; // change to your DB user
$pass = "";     // your DB password
$dbname = "wgw";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
