<?php
include 'koneksi.php';

if (isset($_GET['id_menu'])) {
    $id_menu = $_GET['id_menu'];
    $query = "SELECT protein, kalori, natrium, kalium, lemak FROM data_menu WHERE id_menu = '$id_menu'";
    $result = $conn->query($query);
    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        echo json_encode($data);
    } else {
        echo json_encode(null); // Return null if no data is found
    }
}

// Ambil data menu untuk dropdown
$query_menu = "SELECT * FROM data_menu";
$result_menu = $conn->query($query_menu);

// Fungsi untuk mendapatkan kode alternatif terakhir
function getLastKodeAlternatif($table) {
    global $conn;
    $query = "SELECT MAX(kode_alternatif) AS last_code FROM $table";
    $result = $conn->query($query);
    $row = $result->fetch_assoc();
    return $row['last_code'];
}

// Proses data pasien makanan dan alternatif
if (isset($_POST['submit'])) {
    // Ambil data pasien dari form
    $nama_pasien = $_POST['nama_pasien'];
    $umur = $_POST['umur'];
    $diagnosa = $_POST['diagnosa'];

    // Ambil ID menu makanan dari form
    $id_menu1 = $_POST['id_menu1'];
    $id_menu2 = $_POST['id_menu2'];
    $id_menu3 = $_POST['id_menu3'];
    $id_menu4 = $_POST['id_menu4'];
    $id_menu5 = $_POST['id_menu5'];

    // Ambil data makanan dan bobot untuk setiap menu
    $makanan = [];
    $bobot = [];
    for ($i = 1; $i <= 5; $i++) {
        $id_menu = $_POST["id_menu$i"];
        $makanan[$i] = $conn->query("SELECT * FROM data_menu WHERE id_menu = '$id_menu'")->fetch_assoc();
        $bobot[$i] = $conn->query("SELECT * FROM data_menu_bobot WHERE id_menu = '$id_menu'")->fetch_assoc();
    }

    // Mendapatkan kode alternatif terakhir dari tabel pasien_makanan
    $last_code_pasien = getLastKodeAlternatif('pasien_makanan');
    $last_number_pasien = (int) substr($last_code_pasien, 1); // Ambil angka setelah 'A'
    $new_code_pasien = 'A' . ($last_number_pasien + 1); // Menambahkan 1 ke angka terakhir

    // Menyimpan data makanan untuk pasien ke tabel pasien_makanan
    foreach ($makanan as $i => $menu) {
        $query_insert_pasien_makanan = "INSERT INTO pasien_makanan (nama_pasien, umur, diagnosa, data_makanan, protein, kalori, natrium, kalium, lemak, kode_alternatif)
                                        VALUES ('$nama_pasien', '$umur', '$diagnosa', '{$menu['nama_menu']}', '{$menu['protein']}', '{$menu['kalori']}', '{$menu['natrium']}', '{$menu['kalium']}', '{$menu['lemak']}', '$new_code_pasien')";
        $conn->query($query_insert_pasien_makanan);
    }

    // Ambil id_pasien yang baru saja disimpan
    $id_pasien = $conn->insert_id;

    // Kode alternatif untuk memasukkan data ke tabel alternatif
    $new_code_alternatif = 'A' . ($last_number_pasien + 1); // Kode untuk alternatif selanjutnya

    // Menyimpan data alternatif ke tabel alternatif
    foreach ($makanan as $i => $menu) {
        $query_insert_alternatif = "INSERT INTO alternatif (kode_alternatif, nama_makanan, C01, C02, C03, C04, C05) 
                                   VALUES ('$new_code_alternatif', '{$menu['nama_menu']}', '{$bobot[$i]['C01']}', '{$bobot[$i]['C02']}', '{$bobot[$i]['C03']}', '{$bobot[$i]['C04']}', '{$bobot[$i]['C05']}')";
        $conn->query($query_insert_alternatif);
    }

    // Redirect setelah berhasil
    header('Location: tampil_pasien.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Data Pasien Makanan</title>
    <style>
  /* Global styles */
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #eaf0e9;
    display: flex;
    height: 100vh;
    overflow: hidden;
}

.sidebar {
    width: 150px; /* Menyesuaikan lebar sidebar */
    height: 100vh;
    background-color: #4caf50;
    color: white;
    position: fixed;
    top: 0;
    left: 0;
    display: flex;
    flex-direction: column;
    padding: 20px;
}

.sidebar a {
    color: white;
    text-decoration: none;
    margin: 10px 0;
    padding: 10px;
    border-radius: 4px;
}

.sidebar a:hover {
    background-color: #388e3c;
}

.content {
    margin-left: 250px; /* Menyesuaikan dengan lebar sidebar */
    width: calc(100% - 250px); /* Agar konten tidak melebihi lebar layar */
    padding: 20px;
    background-color: white;
    box-sizing: border-box;
    height: 100vh;
    overflow-y: auto;
}

.content h3 {
    margin-top: 0;
    text-align: left;
    font-size: 24px;
    color: #333;
}

/* Form styles */
form {
    background-color: #fff9e3;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    width: 100%; /* Form mengisi seluruh lebar yang tersedia */
    max-width: 100%;
    box-sizing: border-box;
    overflow-x: hidden;
    margin-left: 200px;
}

label {
    display: block;
    margin-bottom: 8px;
    font-weight: bold;
    font-size: 16px;
    color: #333;
}

input[type="text"], input[type="number"], select {
    width: 100%; /* Menjamin field mengisi lebar form */
    padding: 10px;
    margin-bottom: 20px;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
    font-size: 14px;
}

button {
    background-color: #4caf50;
    color: white;
    padding: 10px 15px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: background 0.3s;
    font-size: 16px;
}

button:hover {
    background-color: #388e3c;
}

/* Responsive styling */
@media (max-width: 768px) {
    .sidebar {
        width: 200px; /* Sidebar lebih kecil untuk layar lebih kecil */
    }

    .content {
        margin-left: 200px; /* Mengatur konten dengan sidebar yang lebih kecil */
        width: calc(100% - 200px); /* Menyesuaikan lebar konten */
    }

    form {
        width: 100%;
        padding: 15px;
    }
}
    </style>
</head>
<body>
<div class="sidebar">
        <a href="index.php">Dasboard</a>
        <a href="alternatif.php">Data Alternatif</a>
        <a href="kriteria.php">Data Kriteria</a>
        <a href="sub_kriteria.php">Data Sub Kriteria</a>
        <a href="data_menu.php">Data Menu</a>
		<a href="tampil_pasien.php">Pasien</a>
        <a href="proses.php">Proses</a>
        <a href="logout.php">Logout</a>
    </div>
</head>
<body>
    <form method="POST">
        <label>Nama Pasien:</label>
        <input type="text" name="nama_pasien" required>

        <label>Umur:</label>
        <input type="number" name="umur" required>

        <label>Diagnosa:</label>
        <select name="diagnosa" required>
            <option value="CKD ON HD">CKD ON HD</option>
            <option value="CKD ON HD + CAP">CKD ON HD + CAP</option>
            <option value="CKD + HIPERTENSI">CKD + HIPERTENSI</option>
            <option value="CKD">CKD</option>
            <option value="CKD + DIABETES">CKD + DIABETES</option>
        </select>

        <!-- Dropdown menu makanan -->
        <label>Data Makanan 1:</label>
        <select name="id_menu1" required>
            <?php while ($menu = $result_menu->fetch_assoc()) { ?>
                <option value="<?= $menu['id_menu']; ?>"><?= $menu['nama_menu']; ?></option>
            <?php } ?>
        </select>
 <!-- Nutrient info for Menu 1 -->
 <div id="nutrient-info-1" class="nutrient-info"></div>
        <!-- Repeat the same for the other 4 menus -->
        <label>Data Makanan 2:</label>
        <select name="id_menu2" required>
            <?php $result_menu->data_seek(0); ?>
            <?php while ($menu = $result_menu->fetch_assoc()) { ?>
                <option value="<?= $menu['id_menu']; ?>"><?= $menu['nama_menu']; ?></option>
            <?php } ?>
        </select>
         <!-- Nutrient info for Menu 2 -->
         <div id="nutrient-info-2" class="nutrient-info"></div>
        <label>Data Makanan 3:</label><br>
        <select name="id_menu3" required>
            <?php $result_menu->data_seek(0); ?>
            <?php while ($menu = $result_menu->fetch_assoc()) { ?>
                <option value="<?= $menu['id_menu']; ?>"><?= $menu['nama_menu']; ?></option>
            <?php } ?>
        </select><br><br>
 <!-- Nutrient info for Menu 3 -->
 <div id="nutrient-info-3" class="nutrient-info"></div>
        <label>Data Makanan 4:</label><br>
        <select name="id_menu4" required>
            <?php $result_menu->data_seek(0); ?>
            <?php while ($menu = $result_menu->fetch_assoc()) { ?>
                <option value="<?= $menu['id_menu']; ?>"><?= $menu['nama_menu']; ?></option>
            <?php } ?>
        </select><br><br>
 <!-- Nutrient info for Menu 4 -->
 <div id="nutrient-info-4" class="nutrient-info"></div>
        <label>Data Makanan 5:</label><br>
        <select name="id_menu5" required>
            <?php $result_menu->data_seek(0); ?>
            <?php while ($menu = $result_menu->fetch_assoc()) { ?>
                <option value="<?= $menu['id_menu']; ?>"><?= $menu['nama_menu']; ?></option>
            <?php } ?>
        </select><br><br>
         <!-- Nutrient info for Menu 5 -->
         <div id="nutrient-info-5" class="nutrient-info"></div>
        <script>
        // JavaScript to display nutrient info based on selected menu
        document.querySelectorAll('select').forEach(select => {
            select.addEventListener('change', function () {
                const menuId = this.value;
                const infoDiv = document.querySelector(`#nutrient-info-${this.name.replace('id_menu', '')}`);
                
                fetch(`get_nutrient_info.php?id_menu=${menuId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data) {
                            infoDiv.innerHTML = `
                                <p><strong>Protein:</strong> ${data.protein}</p>
                                <p><strong>Kalori:</strong> ${data.kalori}</p>
                                <p><strong>Natrium:</strong> ${data.natrium}</p>
                                <p><strong>Kalium:</strong> ${data.kalium}</p>
                                <p><strong>Lemak:</strong> ${data.lemak}</p>
                            `;
                        }
                    });
            });
        });
    </script>
        <button type="submit" name="submit">Tambah</button>
    </form>
</body>
</html>