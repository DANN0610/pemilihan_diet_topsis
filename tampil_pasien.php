<?php
// Sertakan file koneksi
include 'koneksi.php';

// Ambil data dari tabel pasien_makanan
$query = "SELECT * FROM pasien_makanan";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pasien Makanan</title>
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
            width: 150px; /* Lebar sidebar */
            height: 100vh;
            background-color: #4caf50; /* Warna hijau untuk sidebar */
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
            background-color: #388e3c; /* Warna hijau lebih gelap saat hover */
        }

        /* Konten utama */
        .content {
            margin-left: 180px; /* Sesuai dengan lebar sidebar */
            width: calc(100% - 250px); /* Menggunakan sisa ruang layar setelah sidebar */
            padding: 20px;
            background-color: white;
            min-height: 100vh; /* Memastikan konten setinggi layar */
            box-sizing: border-box; /* Termasuk padding dalam perhitungan lebar */
        }

        /* Heading di konten */
        .content h3 {
            margin-top: 0;
            text-align: left; /* Teks heading rata kiri */
            font-size: 24px;
            color: #333; /* Warna teks heading */
        }

        /* Tabel */
        table {
            width: 100%; /* Tabel memenuhi seluruh ruang konten */
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 16px;
            background-color: #fff9e3; /* Warna kuning muda untuk tabel */
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        table th, table td {
            border: 1px solid #ffeb3b; /* Warna kuning untuk garis tabel */
            padding: 12px;
            text-align: left;
        }

        table th {
            background-color: #fbc02d; /* Warna kuning lebih gelap untuk header tabel */
            color: white;
        }

        table tr:nth-child(even) {
            background-color: #fffde7; /* Warna kuning lebih terang untuk baris genap */
        }

        table tr:hover {
            background-color: #ffecb3; /* Warna kuning lebih terang saat hover */
        }

        /* Tombol */
        button, .add-btn {
            background-color: #f44336; /* Warna merah untuk tombol */
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
            background-color: #d32f2f; /* Warna merah lebih gelap saat hover */
        }

        /* Tautan tindakan di tabel */
        .btn-action {
            text-decoration: none;
            color: white;
            padding: 5px 10px;
            margin-right: 5px;
            border-radius: 4px;
            transition: background 0.3s;
        }

        .btn-action.edit {
            background-color: #4caf50; /* Warna hijau untuk edit */
        }

        .btn-action.edit:hover {
            background-color: #388e3c; /* Warna hijau lebih gelap saat hover */
        }

        .btn-action.delete {
            background-color: #f44336; /* Warna merah untuk hapus */
        }

        .btn-action.delete:hover {
            background-color: #d32f2f; /* Warna merah lebih gelap saat hover */
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
        <h3>Data Pasien Makanan</h3>
        <a href="tambah_pasien.php" class="add-btn">+ Tambah Pasien Makanan</a>
        <table>
            <thead>
                <tr>
                    <th>ID Pasien</th>
                    <th>Nama Pasien</th>
                    <th>Umur(Tahun)</th>
                    <th>Diagnosa</th>
                    <th>Data Makanan(gr)</th>
                    <th>Protein(g)</th>
                    <th>Kalori(kkal)</th>
                    <th>Natrium(mg)</th>
                    <th>Kalium(mg)</th>
                    <th>Lemak(mg)</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id_pasien']); ?></td>
                        <td><?= htmlspecialchars($row['nama_pasien']); ?></td>
                        <td><?= htmlspecialchars($row['umur']); ?></td>
                        <td><?= htmlspecialchars($row['diagnosa']); ?></td>
                        <td><?= htmlspecialchars($row['data_makanan']); ?></td>
                        <td><?= htmlspecialchars($row['protein']); ?></td>
                        <td><?= htmlspecialchars($row['kalori']); ?></td>
                        <td><?= htmlspecialchars($row['natrium']); ?></td>
                        <td><?= htmlspecialchars($row['kalium']); ?></td>
                        <td><?= htmlspecialchars($row['lemak']); ?></td>
                        <td class="aksi">
                            <a href="hapus_pasien.php?id_pasien=<?= $row['id_pasien']; ?>" class="btn-action delete" onclick="return confirm('Apakah Anda yakin ingin menghapus data pasien ini?')">Hapus</a>

                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>
