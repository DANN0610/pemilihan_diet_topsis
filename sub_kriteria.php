<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

include 'koneksi.php';

// Query untuk masing-masing tabel sub_kriteria
$sql_protein = "SELECT * FROM sub_kriteria_protein";
$result_protein = $conn->query($sql_protein);

$sql_kalori = "SELECT * FROM sub_kriteria_kalori";
$result_kalori = $conn->query($sql_kalori);

$sql_natrium = "SELECT * FROM sub_kriteria_natrium";
$result_natrium = $conn->query($sql_natrium);

$sql_kalium = "SELECT * FROM sub_kriteria_kalium";
$result_kalium = $conn->query($sql_kalium);

$sql_lemak = "SELECT * FROM sub_kriteria_lemak";
$result_lemak = $conn->query($sql_lemak);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Data Sub Kriteria</title>
    <style>
        /* Styling tetap sama seperti sebelumnya */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #eaf0e9; /* Warna latar belakang hijau muda */
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

        .container {
            margin-left: 250px;
            width: calc(100% - 350px);
            padding: 20px;
            background-color: white;
            min-height: 100vh;
            box-sizing: border-box;
        }

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
    
    <div class="container">
        <h3>Data Sub Kriteria Protein</h3>
        <table>
            <thead>
                <tr>
                    <th>ID Sub Kriteria</th>
                    <th>Kode Sub Kriteria</th>
                    <th>Nama Sub Kriteria</th>
                    <th>Nilai Asupan</th>
                    <th>Bobot</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result_protein->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id_sub_kriteria']; ?></td>
                        <td><?php echo $row['kode_sub_kriteria']; ?></td>
                        <td><?php echo $row['nama_sub_kriteria']; ?></td>
                        <td><?php echo $row['nilai_asupan']; ?></td>
                        <td><?php echo $row['bobot']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <h3>Data Sub Kriteria Kalori</h3>
        <table>
            <thead>
                <tr>
                    <th>ID Sub Kriteria</th>
                    <th>Kode Sub Kriteria</th>
                    <th>Nama Sub Kriteria</th>
                    <th>Nilai Asupan</th>
                    <th>Bobot</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result_kalori->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id_sub_kriteria']; ?></td>
                        <td><?php echo $row['kode_sub_kriteria']; ?></td>
                        <td><?php echo $row['nama_sub_kriteria']; ?></td>
                        <td><?php echo $row['nilai_asupan']; ?></td>
                        <td><?php echo $row['bobot']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <h3>Data Sub Kriteria Natrium</h3>
        <table>
            <thead>
                <tr>
                    <th>ID Sub Kriteria</th>
                    <th>Kode Sub Kriteria</th>
                    <th>Nama Sub Kriteria</th>
                    <th>Nilai Asupan</th>
                    <th>Bobot</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result_natrium->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id_sub_kriteria']; ?></td>
                        <td><?php echo $row['kode_sub_kriteria']; ?></td>
                        <td><?php echo $row['nama_sub_kriteria']; ?></td>
                        <td><?php echo $row['nilai_asupan']; ?></td>
                        <td><?php echo $row['bobot']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <h3>Data Sub Kriteria Kalium</h3>
        <table>
            <thead>
                <tr>
                    <th>ID Sub Kriteria</th>
                    <th>Kode Sub Kriteria</th>
                    <th>Nama Sub Kriteria</th>
                    <th>Nilai Asupan</th>
                    <th>Bobot</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result_kalium->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id_sub_kriteria']; ?></td>
                        <td><?php echo $row['kode_sub_kriteria']; ?></td>
                        <td><?php echo $row['nama_sub_kriteria']; ?></td>
                        <td><?php echo $row['nilai_asupan']; ?></td>
                        <td><?php echo $row['bobot']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <h3>Data Sub Kriteria Lemak</h3>
        <table>
            <thead>
                <tr>
                    <th>ID Sub Kriteria</th>
                    <th>Kode Sub Kriteria</th>
                    <th>Nama Sub Kriteria</th>
                    <th>Nilai Asupan</th>
                    <th>Bobot</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result_lemak->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id_sub_kriteria']; ?></td>
                        <td><?php echo $row['kode_sub_kriteria']; ?></td>
                        <td><?php echo $row['nama_sub_kriteria']; ?></td>
                        <td><?php echo $row['nilai_asupan']; ?></td>
                        <td><?php echo $row['bobot']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
