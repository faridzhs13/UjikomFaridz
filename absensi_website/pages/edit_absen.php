<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'guru') {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id'])) {
    $id_absen = $_GET['id'];

    // Ambil data absen untuk siswa tertentu
    $absen = $conn->query("SELECT * FROM absensi WHERE id = $id_absen")->fetch_assoc();
    if (!$absen) {
        die("Absen tidak ditemukan.");
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $status = $_POST['status'];

        // Update status absen
        $update = $conn->prepare("UPDATE absensi SET status = ? WHERE id = ?");
        $update->bind_param("si", $status, $id_absen);
        $update->execute();

        header("Location: dashboard_guru.php");
        exit;
    }
} else {
    die("ID absen tidak diberikan.");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Absen</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<?php include '../includes/sidebar.php'; ?>

<div class="container">
    <h2>Edit Absen</h2>

    <form method="POST">
        <label for="status">Status Absen</label>
        <select name="status" required>
            <option value="hadir" <?= $absen['status'] == 'hadir' ? 'selected' : '' ?>>Hadir</option>
            <option value="sakit" <?= $absen['status'] == 'sakit' ? 'selected' : '' ?>>Sakit</option>
            <option value="izin" <?= $absen['status'] == 'izin' ? 'selected' : '' ?>>Izin</option>
            <option value="alfa" <?= $absen['status'] == 'alfa' ? 'selected' : '' ?>>Alfa</option>
        </select>
        <button type="submit" class="btn">Update</button>
    </form>
</div>

</body>
</html>
