<?php
include 'koneksi.php';

if (isset($_GET['id_menu'])) {
    $id_menu = $_GET['id_menu'];
    $query = "SELECT protein, kalori, natrium, kalium, lemak FROM data_menu WHERE id_menu = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id_menu);
    $stmt->execute();
    $result = $stmt->get_result();
    $nutrient = $result->fetch_assoc();
    echo json_encode($nutrient);
}
?>
