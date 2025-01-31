<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

include 'koneksi.php';

// Proses form untuk menambah data menu
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_menu = htmlspecialchars(trim($_POST['nama_menu']));
    $asupan = floatval($_POST['berat']); // Input Asupan
    $protein = floatval($_POST['protein']);
    $kalori = floatval($_POST['kalori']);
    $natrium = floatval($_POST['natrium']);
    $kalium = floatval($_POST['kalium']);
    $lemak = floatval($_POST['lemak']);

    if (!empty($nama_menu) && $protein >= 0 && $kalori >= 0 && $natrium >= 0 && $kalium >= 0 && $lemak >= 0 && $asupan >= 0) {
        // Ambil ID terakhir dari tabel data_menu
        $result = $conn->query("SELECT MAX(id_menu) AS last_id FROM data_menu");
        $row = $result->fetch_assoc();
        $last_id = isset($row['last_id']) ? intval($row['last_id']) : 0;

        // Tentukan ID baru (ID terakhir + 1)
        $new_id = $last_id + 1;

        // Gunakan prepared statement untuk memasukkan data menu
        $sql = $conn->prepare(
            "INSERT INTO data_menu (id_menu, nama_menu, berat, kalori, natrium, kalium, lemak,protein ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $sql->bind_param("isddddd", $new_id, $nama_menu, $asupan, $kalori, $natrium, $kalium, $lemak, $protein);

        if ($sql->execute()) {
            header("Location: data_menu.php");
            exit;
        } else {
            $error = "Terjadi kesalahan saat menyimpan data.";
        }
        $sql->close();
    } else {
        $error = "Semua kolom wajib diisi dengan nilai yang valid!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah Menu</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #eaf0e9;
            display: flex;
        }

        .sidebar {
            width: 150px;
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
            margin-left: 250px;
            width: calc(100% - 250px);
            padding: 20px;
            background-color: white;
        }

        .content h3 {
            margin-top: 0;
            text-align: left;
            font-size: 24px;
            color: #333;
        }

        form {
            background-color: #fff9e3;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        input[type="text"], input[type="number"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        button {
            background-color: #4caf50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background 0.3s;
        }

        button:hover {
            background-color: #388e3c;
        }

        a {
            text-decoration: none;
            color: #4caf50;
            margin-left: 10px;
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
<div class="content">
    <h3>Tambah Menu</h3>
    <?php if (!empty($error)): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>
    <form method="POST">
        <label>Nama Menu:</label>
        <input type="text" name="nama_menu" required>

        <label>Asupan (g/ml):</label>
        <input type="number" name="berat" step="0.01" required>
        
        <label>Protein (g):</label>
        <input type="number" name="protein" step="0.01" required>

        <label>Kalori (kkal):</label>
        <input type="number" name="kalori" step="0.01" required>

        <label>Natrium (mg):</label>
        <input type="number" name="natrium" step="0.01" required>

        <label>Kalium (mg):</label>
        <input type="number" name="kalium" step="0.01" required>

        <label>Lemak (mg):</label>
        <input type="number" name="lemak" step="0.01" required>

        <button type="submit">Tambah</button>
        <a href="data_menu.php">Kembali</a>
    </form>
</div>
</body>
</html>
