<?php
include 'koneksi.php';

$id_pasien = $_GET['id_pasien'];

// Ambil kode_alternatif berdasarkan id_pasien dari tabel alternatif
$query_kode_alternatif = "SELECT kode_alternatif FROM pasien_makanan WHERE id_pasien = '$id_pasien'";
$result = $conn->query($query_kode_alternatif);

if ($result->num_rows > 0) {
    // Ambil kode_alternatif
    $row = $result->fetch_assoc();
    $kode_alternatif = $row['kode_alternatif'];

    // Hapus semua data yang memiliki kode_alternatif yang sama dari tabel pasien_makanan
    $query_delete_pasien_makanan = "DELETE FROM pasien_makanan WHERE kode_alternatif = '$kode_alternatif'";

    if ($conn->query($query_delete_pasien_makanan)) {
        // Hapus semua data yang memiliki kode_alternatif yang sama dari tabel alternatif
        $query_delete_alternatif = "DELETE FROM alternatif WHERE kode_alternatif = '$kode_alternatif'";

        if ($conn->query($query_delete_alternatif)) {
            // Redirect setelah berhasil menghapus
            header('Location: tampil_pasien.php');
            exit();
        } else {
            echo "Gagal menghapus data dari tabel alternatif: " . $conn->error;
        }
    } else {
        echo "Gagal menghapus data dari tabel pasien_makanan: " . $conn->error;
    }
} else {
    echo "Kode alternatif tidak ditemukan untuk pasien dengan ID: $id_pasien";
}
?>
