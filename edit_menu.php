<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

include 'koneksi.php';

// Cek apakah ID menu tersedia di URL
if (!isset($_GET['id'])) {
    header("Location: data_menu.php");
    exit;
}

$id_menu = intval($_GET['id']); // Pastikan ID adalah integer untuk mencegah SQL Injection

// Ambil data menu berdasarkan ID
$sql = $conn->prepare("SELECT * FROM data_menu WHERE id_menu = ?");
$sql->bind_param("i", $id_menu);
$sql->execute();
$result = $sql->get_result();
$data = $result->fetch_assoc();

if (!$data) {
    header("Location: data_menu.php");
    exit;
}

// Proses form update data menu
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_menu = htmlspecialchars(trim($_POST['nama_menu']));
    $protein = floatval($_POST['protein']);
    $kalori = floatval($_POST['kalori']);
    $natrium = floatval($_POST['natrium']);
    $kalium = floatval($_POST['kalium']);
    $lemak = floatval($_POST['lemak']);

    // Validasi input
    if (!empty($nama_menu) && $protein >= 0 && $kalori >= 0 && $natrium >= 0 && $kalium >= 0 && $lemak >= 0) {
        $update_sql = $conn->prepare(
            "UPDATE data_menu SET nama_menu = ?, protein = ?, kalori = ?, natrium = ?, kalium = ?, lemak = ? WHERE id_menu = ?"
        );
        $update_sql->bind_param("sddddddi", $nama_menu, $, $protein, $kalori, $natrium, $kalium, $lemak, $id_menu);

        if ($update_sql->execute()) {
            header("Location: data_menu.php");
            exit;
        } else {
            $error = "Error: " . $update_sql->error;
        }
    } else {
        $error = "Semua kolom wajib diisi dengan nilai yang valid!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Menu</title>
    <style>
        /* Gaya sesuai dengan data_menu.php */
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
    <h3>Edit Menu</h3>
    <?php if (!empty($error)): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>
    <form method="POST">
        <label>Nama Menu:</label>
        <input type="text" name="nama_menu" value="<?php echo htmlspecialchars($data['nama_menu']); ?>" required>

        <label>Protein (g):</label>
        <input type="number" name="protein" value="<?php echo htmlspecialchars($data['protein']); ?>" required>

        <label>Kalori (kkal):</label>
        <input type="number" name="kalori" value="<?php echo htmlspecialchars($data['kalori']); ?>" required>

        <label>Natrium (mg):</label>
        <input type="number" name="natrium" value="<?php echo htmlspecialchars($data['natrium']); ?>" required>

        <label>Kalium (mg):</label>
        <input type="number" name="kalium" value="<?php echo htmlspecialchars($data['kalium']); ?>" required>

        <label>Lemak (mg):</label>
        <input type="number" name="lemak" value="<?php echo htmlspecialchars($data['lemak']); ?>" required>

        <button type="submit">Update</button>
        <a href="data_menu.php">Kembali</a>
    </form>
</div>
</body>
</html>
