<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

include 'koneksi.php';

// Fetch data dari tabel `data_menu`
$sql = "SELECT * FROM data_menu";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Data Menu</title>
    <style>
        /* Styling untuk halaman utama */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #eaf0e9; /* Warna latar belakang hijau muda */
            display: flex;
        }

        /* Sidebar */
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
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }

        .sidebar a {
            color: white;
            text-decoration: none;
            margin: 10px 0;
            padding: 10px;
            border-radius: 4px;
            transition: background 0.3s;
        }

        .sidebar a:hover {
            background-color: #388e3c;
        }

        /* Konten utama */
        .content {
            margin-left: 200px;
            width: calc(100% - 250px);
            padding: 20px;
            background-color: white;
            min-height: 100vh;
            box-sizing: border-box;
        }

        .content h3 {
            margin-top: 0;
            text-align: left;
            font-size: 24px;
            color: #333;
        }

        /* Tabel */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 16px;
            background-color: #fff9e3;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        table th, table td {
            border: 1px solid #ffeb3b;
            padding: 12px;
            text-align: left;
        }

        table th {
            background-color: #fbc02d;
            color: white;
        }

        table tr:nth-child(even) {
            background-color: #fffde7;
        }

        table tr:hover {
            background-color: #ffecb3;
        }

        /* Tombol */
        button, .add-btn {
            background-color: #f44336;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: background 0.3s;
        }

        button:hover, .add-btn:hover {
            background-color: #d32f2f;
        }

        .btn-action {
            text-decoration: none;
            color: white;
            padding: 5px 10px;
            margin-right: 5px;
            border-radius: 4px;
            transition: background 0.3s;
        }

        .btn-action.edit {
            background-color: #4caf50;
        }

        .btn-action.edit:hover {
            background-color: #388e3c;
        }

        .btn-action.delete {
            background-color: #f44336;
        }

        .btn-action.delete:hover {
            background-color: #d32f2f;
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
    <h3>Data Menu</h3>
    <a href="tambah_menu.php" class="add-btn">+ Tambah Menu</a>
    <table>
        <thead>
            <tr>
                <th>Nama Menu</th>
                <th>Protein (g)</th>
                <th>Kalori (kkal)</th>
                <th>Natrium (mg)</th>
                <th>Kalium (mg)</th>
                <th>Lemak (mg)</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['nama_menu']); ?></td>
                    <td><?php echo htmlspecialchars($row['protein']); ?></td>
                    <td><?php echo htmlspecialchars($row['kalori']); ?></td>
                    <td><?php echo htmlspecialchars($row['natrium']); ?></td>
                    <td><?php echo htmlspecialchars($row['kalium']); ?></td>
                    <td><?php echo htmlspecialchars($row['lemak']); ?></td>
                    <td>
                        <a href="edit_menu.php?id=<?php echo $row['id_menu']; ?>" class="btn-action edit">Edit</a>
                        <a href="hapus_menu.php?id_menu=<?php echo $row['id_menu']; ?>" class="btn-action delete" onclick="return confirm('Yakin ingin menghapus data ini?');">Hapus</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>
