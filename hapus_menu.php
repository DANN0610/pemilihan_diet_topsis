<?php
session_start();
if (!isset($_GET['id_menu']) || !is_numeric($_GET['id_menu'])) {
    header("Location: data_menu.php");
    exit;
}
include 'koneksi.php';

// Validasi keberadaan id_menu
if (!isset($_GET['id_menu']) || !is_numeric($_GET['id_menu'])) {
    header("Location: data_menu.php");
    exit;
}

$id_menu = intval($_GET['id_menu']); // Konversi ke integer untuk keamanan

// Gunakan prepared statement untuk menghapus data
$sql = $conn->prepare("DELETE FROM data_menu WHERE id_menu = ?");
$sql->bind_param("i", $id_menu);

if ($sql->execute()) {
    // Redirect ke halaman data_menu setelah berhasil
    header("Location: data_menu.php");
    exit;
} else {
    echo "Error: " . $sql->error;
}

$sql->close();
$conn->close();
?>
