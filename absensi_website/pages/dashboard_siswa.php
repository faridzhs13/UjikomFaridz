<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'siswa') {
    header("Location: login.php");
    exit;
}

require_once '../includes/sidebar.php';

$user_id = $_SESSION['user'];
$tanggal = date('Y-m-d');

// Ambil data siswa (termasuk foto)
$stmt = $conn->prepare("SELECT nama, foto FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Cek absen hari ini
$cek = $conn->prepare("SELECT id FROM absensi WHERE user_id = ? AND tanggal = ?");
$cek->bind_param("is", $user_id, $tanggal);
$cek->execute();
$cek->store_result();
$sudah_absen = $cek->num_rows > 0;

if ($_SERVER["REQUEST_METHOD"] == "POST" && !$sudah_absen) {
    $status = $_POST['status'];
    $insert = $conn->prepare("INSERT INTO absensi (user_id, tanggal, status) VALUES (?, ?, ?)");
    $insert->bind_param("iss", $user_id, $tanggal, $status);
    $insert->execute();

    header("Location: ../index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Siswa</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
        body {
            background-color: #f0f2f5;
            font-family: Arial, sans-serif;
            margin: 0;
        }

        /* === TOPBAR === */
        .topbar {
            position: fixed;
            top: 0;
            left: 290px; /* sesuaikan dengan lebar sidebar */
            right: 0;
            height: 60px;
            background-color: #343a40;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            padding: 0 20px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            z-index: 999;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #fff;
            font-weight: bold;
        }

        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #fff;
        }

        /* === MAIN CONTENT === */
        .main-content {
            margin-left: 400px; /* Sesuaikan dengan lebar sidebar */
            margin-top: 100px;
            padding: 30px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            max-width: 800px;
        }

        h2 {
            color: #333;
            text-align: center;
        }

        p {
            color: #333;
            font-size: 16px;
        }

        .btn {
            background-color: #4a90e2;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            text-align: center;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: #357abd;
        }

        select {
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #ccc;
            outline: none;
            width: 100%;
            margin-bottom: 15px;
            font-size: 14px;
        }

        .status-message {
            font-size: 18px;
            font-weight: bold;
            color: green;
            text-align: center;
        }

        .status-message-error {
            color: red;
        }

        .status-message-success {
            color: green;
        }

        .icon {
            margin-right: 10px;
        }

        .logout-btn {
            display: inline-block;
            margin-top: 20px;
            background-color: #e74c3c;
            text-decoration: none;
            padding: 10px 20px;
            color: white;
            border-radius: 8px;
            transition: background-color 0.3s ease;
        }

        .logout-btn:hover {
            background-color: #c0392b;
        }
    </style>
</head>
<body>

<?php include '../includes/sidebar.php'; ?>

<!-- Navbar Top -->
<div class="topbar">
    <div class="topbar-right">
        <span><?= htmlspecialchars($user['nama']) ?></span>
        <img src="../uploads/<?= $user['foto'] ?? 'default.png' ?>" alt="Foto Profil" class="avatar">
    </div>
</div>

<!-- Konten Utama -->
<div class="main-content">
    <h2><i class="fas fa-user-graduate"></i> Halo, <?= htmlspecialchars($user['nama']) ?></h2>
    <p><i class="fas fa-calendar-day"></i> Tanggal: <strong><?= date('d-m-Y') ?></strong></p>

    <?php if ($sudah_absen): ?>
        <p class="status-message status-message-success"><i class="fas fa-check-circle"></i> Kamu sudah absen hari ini.</p>
        <a href="../logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
    <?php else: ?>
        <form method="POST">
            <label for="status"><i class="fas fa-check-circle"></i> Pilih Status Kehadiran:</label><br><br>
            <select name="status" required>
                <option value="">-- Pilih Status --</option>
                <option value="hadir">Hadir</option>
                <option value="sakit">Sakit</option>
                <option value="izin">Izin</option>
                <option value="alfa">Alfa</option>
            </select><br><br>
            <button type="submit" class="btn"><i class="fas fa-paper-plane"></i> Absen Sekarang</button>
        </form>
    <?php endif; ?>
</div>

</body>
</html>
