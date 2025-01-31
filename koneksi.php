<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "spk_pemilihan_diet";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Koneksi ke database gagal: " . $conn->connect_error);
}
?>
