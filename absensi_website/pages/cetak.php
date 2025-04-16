<?php
session_start();
require_once '../includes/db.php';

// Check authentication
if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'guru') {
    header("Location: ../login.php");
    exit;
}

// Get filter parameters
$kelas = $_GET['kelas'] ?? '';
$jurusan = $_GET['jurusan'] ?? '';
$tahun = $_GET['tahun'] ?? '';
$tanggal = date('Y-m-d');

// Get the teacher information
$user_id = $_SESSION['user'];
$stmtUser = $conn->prepare("SELECT nama FROM users WHERE id = ?");
$stmtUser->bind_param("i", $user_id);
$stmtUser->execute();
$resultUser = $stmtUser->get_result();
$guru = $resultUser->fetch_assoc();

// Query to get student data with filters
$sql = "SELECT * FROM users WHERE role = 'siswa'";
$filter = [];
if ($kelas) $filter[] = "kelas = '$kelas'";
if ($jurusan) $filter[] = "jurusan = '$jurusan'";
if ($tahun) $filter[] = "tahun_masuk = '$tahun'";
if (!empty($filter)) $sql .= " AND " . implode(" AND ", $filter);
$siswa = $conn->query($sql);

// Query to get attendance data for today
$dataAbsensi = [];
$absensiQuery = "SELECT user_id, status FROM absensi WHERE tanggal = '$tanggal'";
$absensiResult = $conn->query($absensiQuery);
while ($absen = $absensiResult->fetch_assoc()) {
    $dataAbsensi[$absen['user_id']] = $absen['status'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Absensi - <?= date('d-m-Y') ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: #333;
        }
        h1, h2 {
            text-align: center;
        }
        .header {
            margin-bottom: 30px;
        }
        .info {
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
        .filter-info {
            margin-bottom: 15px;
            font-style: italic;
        }
        .signature {
            margin-top: 50px;
            float: right;
            width: 200px;
            text-align: center;
        }
        .signature-line {
            margin-top: 70px;
            border-top: 1px solid #000;
        }
        .print-button {
            text-align: center;
            margin: 20px;
        }
        .print-button button {
            padding: 10px 20px;
            background-color: #4a90e2;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .no-data {
            text-align: center;
            padding: 20px;
            font-style: italic;
        }
        @media print {
            .print-button {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="print-button">
        <button onclick="window.print()">Cetak Laporan</button>
    </div>

    <div class="header">
        <h1>LAPORAN ABSENSI SISWA</h1>
        <h2>Tanggal: <?= date('d-m-Y') ?></h2>
    </div>

    <div class="info">
        <p><strong>Guru:</strong> <?= htmlspecialchars($guru['nama']) ?></p>
        <div class="filter-info">
            <p>
                <?php if ($kelas): ?>Kelas: <?= $kelas ?><?php endif; ?>
                <?php if ($jurusan): ?> | Jurusan: <?= $jurusan ?><?php endif; ?>
                <?php if ($tahun): ?> | Tahun Masuk: <?= $tahun ?><?php endif; ?>
                <?php if (!$kelas && !$jurusan && !$tahun): ?>Semua Siswa<?php endif; ?>
            </p>
        </div>
    </div>

    <?php if ($siswa->num_rows > 0): ?>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Siswa</th>
                <th>Kelas</th>
                <th>Jurusan</th>
                <th>Tahun Masuk</th>
                <th>Status Kehadiran</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            while ($row = $siswa->fetch_assoc()): 
            ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= htmlspecialchars($row['nama']) ?></td>
                <td><?= $row['kelas'] ?></td>
                <td><?= $row['jurusan'] ?></td>
                <td><?= $row['tahun_masuk'] ?></td>
                <td>
                    <?php 
                    if (isset($dataAbsensi[$row['id']])) {
                        echo ucfirst($dataAbsensi[$row['id']]);
                    } else {
                        echo "Belum Absen";
                    }
                    ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <?php else: ?>
    <div class="no-data">
        <p>Tidak ada data siswa yang sesuai dengan filter</p>
    </div>
    <?php endif; ?>

    <div class="signature">
        <p><?= date('d F Y') ?></p>
        <p>Guru Pengampu</p>
        <div class="signature-line"></div>
        <p><?= htmlspecialchars($guru['nama']) ?></p>
    </div>
</body>
</html>