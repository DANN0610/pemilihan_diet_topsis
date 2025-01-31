<?php
include 'koneksi.php';
ob_start(); // Mulai output buffering

// Ambil kode alternatif dari parameter GET
$kode_alternatif = isset($_GET['kode_alternatif']) ? $_GET['kode_alternatif'] : null;

if (!$kode_alternatif) {
    echo "Kode alternatif tidak ditemukan.";
    exit;
}

// Ambil data pasien berdasarkan kode alternatif
$query_pasien = "SELECT * FROM pasien_makanan WHERE kode_alternatif = '$kode_alternatif' ORDER BY id_pasien";
$result_pasien = $conn->query($query_pasien);

if ($result_pasien->num_rows > 0) {
    $data_pasien = [];
    while ($row = $result_pasien->fetch_assoc()) {
        $data_pasien[] = $row;
    }

    // Ambil data alternatif berdasarkan kode alternatif
    $query_alternatif = "SELECT * FROM alternatif WHERE kode_alternatif = '$kode_alternatif' ORDER BY id_alternatif";
    $result_alternatif = $conn->query($query_alternatif);

    $data_alternatif = [];
    while ($row = $result_alternatif->fetch_assoc()) {
        $data_alternatif[] = $row;
    }
} else {
    echo "Data tidak ditemukan.";
    exit;
}

// Proses update ketika form disubmit
if (isset($_POST['submit'])) {
    // Ambil data pasien dari form
    $nama_pasien = $_POST['nama_pasien'];
    $umur = $_POST['umur'];
    $diagnosa = $_POST['diagnosa'];

    // Update data pasien_makanan dan alternatif
    for ($i = 0; $i < 5; $i++) {
        $id_menu = $_POST["id_menu" . ($i + 1)];

        // Ambil data makanan dari data_menu
        $menu = $conn->query("SELECT * FROM data_menu WHERE id_menu = '$id_menu'")->fetch_assoc();

        // Ambil data bobot dari data_menu_bobot
        $bobot = $conn->query("SELECT * FROM data_menu_bobot WHERE id_menu = '$id_menu'")->fetch_assoc();

        // Update tabel pasien_makanan
        $query_update_pasien = "UPDATE pasien_makanan 
                                SET nama_pasien = '$nama_pasien', 
                                    umur = '$umur', 
                                    diagnosa = '$diagnosa', 
                                    data_makanan = '{$menu['nama_menu']}', 
                                    protein = '{$menu['protein']}', 
                                    kalori = '{$menu['kalori']}', 
                                    natrium = '{$menu['natrium']}', 
                                    kalium = '{$menu['kalium']}', 
                                    lemak = '{$menu['lemak']}'
                                WHERE kode_alternatif = '$kode_alternatif' AND id_pasien = '{$data_pasien[$i]['id_pasien']}'";
        $conn->query($query_update_pasien);

        // Update tabel alternatif
        $query_update_alternatif = "UPDATE alternatif 
                                    SET nama_makanan = '{$menu['nama_menu']}', 
                                        C01 = '{$bobot['C01']}', 
                                        C02 = '{$bobot['C02']}', 
                                        C03 = '{$bobot['C03']}', 
                                        C04 = '{$bobot['C04']}', 
                                        C05 = '{$bobot['C05']}'
                                    WHERE kode_alternatif = '$kode_alternatif' AND id_alternatif = '{$data_alternatif[$i]['id_alternatif']}'";
        $conn->query($query_update_alternatif);
    }

    // Redirect ke halaman tampil pasien setelah berhasil
    header('Location: tampil_pasien.php');
    exit;
}

// Ambil semua data menu untuk dropdown
$query_menu = "SELECT * FROM data_menu";
$result_menu = $conn->query($query_menu);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data Pasien</title>
</head>
<body>
    <h1>Edit Data Pasien</h1>
    <form action="" method="post">
        <label for="nama_pasien">Nama Pasien:</label><br>
        <input type="text" id="nama_pasien" name="nama_pasien" value="<?= $data_pasien[0]['nama_pasien']; ?>" required><br><br>

        <label for="umur">Umur:</label><br>
        <input type="number" id="umur" name="umur" value="<?= $data_pasien[0]['umur']; ?>" required><br><br>

        <label for="diagnosa">Diagnosa:</label><br>
        <textarea id="diagnosa" name="diagnosa" required><?= $data_pasien[0]['diagnosa']; ?></textarea><br><br>

        <h3>Menu Makanan</h3>
        <?php for ($i = 0; $i < 5; $i++): ?>
            <label for="id_menu<?= $i + 1; ?>">Pilih Menu <?= $i + 1; ?>:</label><br>
            <select id="id_menu<?= $i + 1; ?>" name="id_menu<?= $i + 1; ?>" required>
                <?php foreach ($result_menu as $menu): ?>
                    <option value="<?= $menu['id_menu']; ?>" <?= $menu['id_menu'] == $data_alternatif[$i]['id_menu'] ? 'selected' : ''; ?>>
                        <?= $menu['nama_menu']; ?>
                    </option>
                <?php endforeach; ?>
            </select><br><br>
        <?php endfor; ?>

        <button type="submit" name="submit">Update Data</button>
    </form>
</body>
</html>

<?php ob_end_flush(); // Akhiri output buffering ?>
