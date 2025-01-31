<?php
// Mengimpor koneksi database
include('koneksi.php');

// Mengambil data alternatif
$queryAlternatif = "SELECT * FROM alternatif ORDER BY kode_alternatif"; // Mengurutkan berdasarkan kode_alternatif
$stmtAlternatif = $conn->query($queryAlternatif);
$alternatifData = $stmtAlternatif->fetch_all(MYSQLI_ASSOC);

// Mengambil bobot kriteria dari tabel nilai_kriteria
$queryBobotKriteria = "SELECT * FROM nilai_kriteria"; // Ambil data dari tabel nilai_kriteria
$stmtBobotKriteria = $conn->query($queryBobotKriteria);
$kriteriaData = $stmtBobotKriteria->fetch_all(MYSQLI_ASSOC);

// Bobot ROC (dari hasil ROC yang sudah dihitung sebelumnya)
$w = [0.30, 0.25, 0.15, 0.15, 0.15];  // Sesuaikan dengan hasil ROC yang Anda miliki

// Validasi data kriteria
if (empty($kriteriaData)) {
    die("Data kriteria tidak ditemukan.");
}

// Normalisasi Matriks
function normalisasi($data, $kriteria) {
    $normalized = [];

    foreach ($data as $alternatif) {
        $row = [];
        foreach ($kriteria as $k) {
            $kode = $k['kode_kriteria'];
            $denominator = sqrt(array_sum(array_map(function ($item) use ($kode) {
                return pow($item[$kode] ?? 0, 2);
            }, $data)));

            $normValue = $denominator > 0 ? ($alternatif[$kode] ?? 0) / $denominator : 0;
            $row[] = $normValue;
        }
        $normalized[] = $row;
    }

    return $normalized;
}

// Matriks Terbobot menggunakan bobot ROC
function matrikTerbobot($normalisasi, $w) {
    $terbobot = [];
    foreach ($normalisasi as $row) {
        $rowBobot = [];
        foreach ($row as $colIndex => $value) {
            $rowBobot[] = $w[$colIndex] * $value;
        }
        $terbobot[] = $rowBobot;
    }
    return $terbobot;
}

$normalisasi = normalisasi($alternatifData, $kriteriaData);
$terbobot = matrikTerbobot($normalisasi, $w);

// Hitung Solusi Ideal Positif dan Negatif
function hitungIdeal($terbobot, $kriteria) {
    $idealPlus = [];
    $idealMinus = [];

    foreach ($kriteria as $index => $k) {
        $kode = $k['kode_kriteria'];
        $sifat = strtolower($k['sifat']); // Mengambil sifat kriteria (COST atau BENEFIT)

        $columnValues = array_column($terbobot, $index);

        if ($sifat === 'benefit') {
            $idealPlus[$kode] = max($columnValues); // A+ untuk BENEFIT adalah nilai maksimum
            $idealMinus[$kode] = min($columnValues); // A- untuk BENEFIT adalah nilai minimum
        } elseif ($sifat === 'cost') {
            $idealPlus[$kode] = min($columnValues); // A+ untuk COST adalah nilai minimum
            $idealMinus[$kode] = max($columnValues); // A- untuk COST adalah nilai maksimum
        }
    }

    return [$idealPlus, $idealMinus];
}

list($idealPlus, $idealMinus) = hitungIdeal($terbobot, $kriteriaData);

// Hitung D+ dan D-
function hitungDPlusDMinus($terbobot, $idealPlus, $idealMinus) {
    $D_plus = [];
    $D_minus = [];

    foreach ($terbobot as $row) {
        $D_plus_value = 0;
        $D_minus_value = 0;

        foreach ($row as $colIndex => $value) {
            $kodeKriteria = array_keys($idealPlus)[$colIndex] ?? null;

            if ($kodeKriteria) {
                $D_plus_value += pow($value - $idealPlus[$kodeKriteria], 2);
                $D_minus_value += pow($value - $idealMinus[$kodeKriteria], 2);
            }
        }

        $D_plus[] = sqrt($D_plus_value);
        $D_minus[] = sqrt($D_minus_value);
    }

    return [$D_plus, $D_minus];
}

list($D_plus, $D_minus) = hitungDPlusDMinus($terbobot, $idealPlus, $idealMinus);

// Hitung Nilai Preferensi
function hitungPreferensi($D_plus, $D_minus) {
    $preferensi = [];
    foreach ($D_plus as $index => $D) {
        $denominator = $D_plus[$index] + $D_minus[$index];
        $preferensi[] = $denominator > 0 ? $D_minus[$index] / $denominator : 0;
    }
    return $preferensi;
}

$preferensi = hitungPreferensi($D_plus, $D_minus);

// Mengurutkan berdasarkan kode alternatif A01 hingga A10
array_multisort(
    array_column($alternatifData, 'kode_alternatif'), SORT_ASC,
    $alternatifData, $D_plus, $D_minus, $preferensi
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.18/jspdf.plugin.autotable.min.js"></script>

    <title>TOPSIS Analysis</title>
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
            margin-left: 200px; /* Sesuai dengan lebar sidebar */
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

        .container {
            margin-left: 250px; /* Sesuai dengan lebar sidebar */
            width: calc(100% - 350px); /* Menggunakan sisa ruang layar setelah sidebar */
            padding: 20px;
            background-color: white;
            min-height: 100vh;
            box-sizing: border-box;
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
    <h1>TOPSIS Analysis</h1>
    <h2>Hasil Perhitungan TOPSIS</h2>

    <!-- Tombol cetak laporan ditempatkan di sini -->
    <div style="margin-bottom: 20px;">
        <button onclick="generatePDF()">Cetak Laporan</button>
    </div>

    <h3>Kriteria dan Alternatif</h3>
    <table>
        <tr>
            <th>Kode Kriteria</th>
            <th>Nama Kriteria</th>
            <th>Bobot</th>
            <th>Sifat</th>
        </tr>
        <?php foreach ($kriteriaData as $kriteria): ?>
            <tr>
                <td><?= $kriteria['kode_kriteria'] ?></td>
                <td><?= $kriteria['nama_kriteria'] ?></td>
                <td><?= number_format($kriteria['bobot'], 2) ?></td>
                <td><?= $kriteria['sifat'] ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
    <h3>Matrix Normalisasi</h3>
    <table>
        <tr>
            <th>Alternatif</th>
            <?php foreach ($kriteriaData as $kriteria): ?>
                <th><?= $kriteria['nama_kriteria'] ?></th>
            <?php endforeach; ?>
        </tr>
        <?php foreach ($normalisasi as $index => $row): ?>
            <tr>
                <td><?= $alternatifData[$index]['kode_alternatif'] ?></td>
                <?php foreach ($row as $nilai): ?>
                    <td><?= number_format($nilai, 2) ?></td>
                <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>
    </table>

    <h3>Matrix Normalisasi Bobot</h3>
    <table>
        <tr>
            <th>Alternatif</th>
            <?php foreach ($kriteriaData as $kriteria): ?>
                <th><?= $kriteria['nama_kriteria'] ?></th>
            <?php endforeach; ?>
        </tr>
        <?php foreach ($terbobot as $index => $row): ?>
            <tr>
                <td><?= $alternatifData[$index]['kode_alternatif'] ?></td>
                <?php foreach ($row as $nilai): ?>
                    <td><?= number_format($nilai, 2) ?></td>
                <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>
    </table>

    <h3>Solusi Ideal Positif (A+) dan Negatif (A-)</h3>
<table>
    <tr>
        <th>Kode Kriteria</th>
        <th>Solusi Ideal Positif (A+)</th>
        <th>Solusi Ideal Negatif (A-)</th>
    </tr>
    <?php 
    // Menampilkan solusi ideal positif dan negatif
    foreach ($kriteriaData as $kriteria): 
        // Mengambil kode kriteria
        $kodeKriteria = $kriteria['kode_kriteria'];
        $solusiIdealPositif = isset($idealPlus[$kodeKriteria]) ? number_format($idealPlus[$kodeKriteria], 2) : '-';
        $solusiIdealNegatif = isset($idealMinus[$kodeKriteria]) ? number_format($idealMinus[$kodeKriteria], 2) : '-';
    ?>
        <tr>
            <td><?= $kodeKriteria ?></td>
            <td><?= $solusiIdealPositif ?></td>
            <td><?= $solusiIdealNegatif ?></td>
        </tr>
    <?php endforeach; ?>
</table>
<h3>Ranking Nilai Preferensi</h3>
<table>
    <tr>
        <th>Urutan</th>
        <th>Kode Alternatif</th>
        <th>Nama Makanan</th>
        <th>Pasien</th> <!-- Kolom Pasien ditambahkan -->
        <th>D+</th>
        <th>D-</th>
        <th>Nilai Preferensi</th>
    </tr>
    <?php 
    // Mapping kode alternatif dengan nama pasien
    $pasienMapping = [
        'A01' => 'Pasien A',
        'A02' => 'Pasien B',
        'A03' => 'Pasien C',
        'A04' => 'Pasien D',
        'A05' => 'Pasien E',
        'A06' => 'Pasien F',
        'A07' => 'Pasien G',
        'A08' => 'Pasien H',
        'A09' => 'Pasien I',
        'A10' => 'Pasien J'
    ];

    // Gabungkan alternatif dengan nilai preferensi
    $rankingData = [];
    foreach ($alternatifData as $index => $alternatif) {
        $rankingData[] = [
            'kode_alternatif' => $alternatif['kode_alternatif'],
            'nama_makanan' => $alternatif['nama_makanan'] ?? '-',
            'pasien' => $pasienMapping[$alternatif['kode_alternatif']] ?? 'Tidak Diketahui',
            'D_plus' => $D_plus[$index],
            'D_minus' => $D_minus[$index],
            'preferensi' => $preferensi[$index]
        ];
    }

    // Kelompokkan data berdasarkan pasien
    $groupedByPasien = [];
    foreach ($rankingData as $data) {
        $groupedByPasien[$data['pasien']][] = $data;
    }

    // Tampilkan hasil dengan urutan per pasien
    foreach ($groupedByPasien as $pasien => $pasienData) {
        // Urutkan data berdasarkan nilai preferensi per pasien
        usort($pasienData, function ($a, $b) {
            return $b['preferensi'] <=> $a['preferensi']; // Urutkan dari yang terbesar ke terkecil
        });

        // Set urutan untuk setiap pasien dimulai dari 1
        $urutan = 1;
        
        // Tampilkan data per pasien
        foreach ($pasienData as $data): ?>
            <tr>
                <td style="font-weight: bold;"><?= $urutan++ ?></td>
                <td><?= $data['kode_alternatif'] ?></td>
                <td><?= $data['nama_makanan'] ?></td>
                <td><?= $data['pasien'] ?></td>
                <td><?= number_format($data['D_plus'], 2) ?></td>
                <td><?= number_format($data['D_minus'], 2) ?></td>
                <td><?= number_format($data['preferensi'], 2) ?></td>
            </tr>
        <?php endforeach; ?>
    <?php } ?>
</table>
</div>
<script>
   function generatePDF() {
    const { jsPDF } = window.jspdf; // Import jsPDF
    const doc = new jsPDF();

    // Tambahkan judul untuk laporan
    doc.setFontSize(16);
    doc.text("Laporan Analisis TOPSIS", 14, 10);
    doc.setFontSize(12);

    // Ambil semua tabel dari halaman
    const tables = document.querySelectorAll('table');

    let yPosition = 20; // Posisi awal untuk setiap tabel
    
    tables.forEach((table, index) => {
        // Tambahkan nama tabel sebelum setiap tabel
        const tableTitle = table.previousElementSibling?.innerText || `Tabel ${index + 1}`;
        doc.text(tableTitle, 14, yPosition);

        // Tambahkan tabel ke PDF
        doc.autoTable({
            html: table,
            startY: yPosition + 5, // Tambahkan sedikit jarak sebelum tabel
            theme: 'grid', // Tema grid
            styles: { fontSize: 10 }, // Ukuran font tabel
        });

        // Update posisi Y untuk tabel berikutnya
        yPosition = doc.lastAutoTable.finalY + 10;
    });

    // Simpan file PDF
    doc.save('laporan_topsis.pdf');
}
</script>
</body>
</html>