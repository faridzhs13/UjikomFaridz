<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'guru') {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user'];
$stmtUser = $conn->prepare("SELECT nama, foto FROM users WHERE id = ?");
$stmtUser->bind_param("i", $user_id);
$stmtUser->execute();
$resultUser = $stmtUser->get_result();
$guru = $resultUser->fetch_assoc();

// Filter
$kelas = $_GET['kelas'] ?? '';
$jurusan = $_GET['jurusan'] ?? '';
$tahun = $_GET['tahun'] ?? '';
$tanggal = date('Y-m-d');

$sql = "SELECT * FROM users WHERE role = 'siswa'";
$filter = [];
if ($kelas) $filter[] = "kelas = '$kelas'";
if ($jurusan) $filter[] = "jurusan = '$jurusan'";
if ($tahun) $filter[] = "tahun_masuk = '$tahun'";
if (!empty($filter)) $sql .= " AND " . implode(" AND ", $filter);
$siswa = $conn->query($sql);

// Input absen
if (isset($_POST['id_siswa']) && isset($_POST['status'])) {
    $id_siswa = $_POST['id_siswa'];
    $status = $_POST['status'];
    $tanggal = $_POST['tanggal'];

    $cek = $conn->prepare("SELECT id FROM absensi WHERE user_id = ? AND tanggal = ?");
    $cek->bind_param("is", $id_siswa, $tanggal);
    $cek->execute();
    $cek->store_result();

    if ($cek->num_rows == 0) {
        $insert = $conn->prepare("INSERT INTO absensi (user_id, tanggal, status) VALUES (?, ?, ?)");
        $insert->bind_param("iss", $id_siswa, $tanggal, $status);
        $insert->execute();
    }

    header("Location: dashboard_guru.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Guru</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
        body {
            background-color: #f0f2f5;
            font-family: Arial, sans-serif;
        }
        .container {
            margin-left: 250px;
            padding: 30px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            max-width: 1200px;
            margin-top: 0px;
        }
        .foto-profil {
            position: fixed;
            top: 20px;
            right: 30px;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #4a90e2;
            box-shadow: 0 0 5px rgba(0,0,0,0.3);
            background-color: #fff;
        }
        h2 {
            color: #333;
            text-align: center;
        }
        form {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            justify-content: center;
            margin-bottom: 20px;
        }
        input, select {
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #ccc;
            width: 200px;
            font-size: 14px;
        }
        .btn {
            background-color: #4a90e2;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            font-size: 14px;
        }
        .btn:hover {
            background-color: #357abd;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            margin-left: 20px
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: center;
        }
        th {
            background-color: #4a90e2;
            color: white;
        }
        td {
            background-color: #f9f9f9;
        }
        .status-message {
            font-size: 16px;
            font-weight: bold;
        }
        .status-hadir {
            color: green;
        }
        .status-absen {
            color: red;
        }
    </style>
</head>
<body>

<?php include '../includes/sidebarguru.php'; ?>

<?php if (!empty($guru['foto']) && file_exists("../uploads/{$guru['foto']}")): ?>
    <img src="../uploads/<?= $guru['foto'] ?>" alt="Foto Guru" class="foto-profil">
<?php else: ?>
    <img src="../assets/default.png" alt="Foto Default" class="foto-profil">
<?php endif; ?>

<div class="container">
    <h2><i class="fas fa-chalkboard-teacher"></i> Selamat Datang, <?= htmlspecialchars($guru['nama']) ?></h2>

    <!-- Filter -->
    <form method="GET">
        <select name="kelas">
            <option value="">Pilih Kelas</option>
            <option value="X" <?= $kelas == 'X' ? 'selected' : '' ?>>X</option>
            <option value="XI" <?= $kelas == 'XI' ? 'selected' : '' ?>>XI</option>
            <option value="XII" <?= $kelas == 'XII' ? 'selected' : '' ?>>XII</option>
        </select>
        <select name="jurusan">
            <option value="">Pilih Jurusan</option>
            <option value="RPL" <?= $jurusan == 'RPL' ? 'selected' : '' ?>>RPL</option>
            <option value="TKJ" <?= $jurusan == 'TKJ' ? 'selected' : '' ?>>TKJ</option>
        </select>
        <select name="tahun">
            <option value="">Tahun Masuk</option>
            <?php for ($i = 2020; $i <= date('Y'); $i++): ?>
                <option value="<?= $i ?>" <?= $tahun == $i ? 'selected' : '' ?>><?= $i ?></option>
            <?php endfor; ?>
        </select>
        <button type="submit" class="btn"><i class="fas fa-filter"></i> Filter</button>
        <a href="cetak.php?kelas=<?= $kelas ?>&jurusan=<?= $jurusan ?>&tahun=<?= $tahun ?>" class="btn" target="_blank"><i class="fas fa-print"></i> Cetak</a>

</form>

    <table>
        <thead>
            <tr>
                <th>Nama</th>
                <th>Kelas</th>
                <th>Jurusan</th>
                <th>Tahun Masuk</th>
                <th>Status Hari Ini (<?= date('d-m-Y') ?>)</th>
                <th>Input Absen</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = $siswa->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['nama']) ?></td>
                <td><?= $row['kelas'] ?></td>
                <td><?= $row['jurusan'] ?></td>
                <td><?= $row['tahun_masuk'] ?></td>
                <td>
                    <?php
                    $absen = $conn->query("SELECT id, status FROM absensi WHERE user_id = {$row['id']} AND tanggal = '$tanggal'")->fetch_assoc();
                    if ($absen) {
                        echo "<span class='status-message status-hadir'><i class='fas fa-check-circle'></i> " . ucfirst($absen['status']) . "</span>";
                    } else {
                        echo "<span class='status-message status-absen'><i class='fas fa-times-circle'></i> Belum Absen</span>";
                    }
                    ?>
                </td>
                <td>
                    <?php if (empty($absen)): ?>
                        <form method="POST" style="display:flex; gap:5px;">
                            <input type="hidden" name="id_siswa" value="<?= $row['id'] ?>">
                            <input type="hidden" name="tanggal" value="<?= $tanggal ?>">
                            <select name="status" required>
                                <option value="hadir">Hadir</option>
                                <option value="sakit">Sakit</option>
                                <option value="izin">Izin</option>
                                <option value="alfa">Alfa</option>
                            </select>
                            <button type="submit" class="btn"><i class="fas fa-save"></i> Isi</button>
                        </form>
                    <?php else: ?>
                        <a href="edit_absen.php?id=<?= $absen['id'] ?>" class="btn"><i class="fas fa-edit"></i> Edit</a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>
