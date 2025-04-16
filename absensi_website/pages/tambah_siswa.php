<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'guru') {
    header("Location: login.php");
    exit;
}

// Cek apakah formulir disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $kelas = $_POST['kelas'];
    $jurusan = $_POST['jurusan'];
    $tahun = $_POST['tahun'];

    // Proses upload foto
    $foto = '';
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === 0) {
        $foto_tmp = $_FILES['foto']['tmp_name'];
        $foto_name = time() . '_' . $_FILES['foto']['name'];
        $foto_path = "../uploads/" . $foto_name;
        move_uploaded_file($foto_tmp, $foto_path);
        $foto = $foto_name;
    }

    // Insert data siswa ke database
    $stmt = $conn->prepare("INSERT INTO users (nama, username, password, kelas, jurusan, tahun_masuk, foto, role) VALUES (?, ?, ?, ?, ?, ?, ?, 'siswa')");
    $stmt->bind_param("sssssss", $nama, $username, $password, $kelas, $jurusan, $tahun, $foto);
    $stmt->execute();
    header("Location: daftar_siswa.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Siswa</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>
<body>

<?php include '../includes/sidebarguru.php'; ?>

<div class="container">
    <h2><i class="fas fa-user-plus"></i> Tambah Siswa</h2>

    <form method="POST" action="tambah_siswa.php" enctype="multipart/form-data" style="margin-bottom: 20px; display: flex; flex-wrap: wrap; gap: 10px;">
        <input type="text" name="nama" placeholder="Nama" required>
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        
        <!-- Kolom Kelas menggunakan <select> -->
        <select name="kelas" required>
            <option value="">Pilih Kelas</option>
            <option value="X">X</option>
            <option value="XI">XI</option>
            <option value="XII">XII</option>
        </select>
        
        <!-- Kolom Jurusan menggunakan <select> -->
        <select name="jurusan" required>
            <option value="">Pilih Jurusan</option>
            <option value="TKJ">TKJ</option>
            <option value="RPL">RPL</option>
        </select>
        
        <select name="tahun" required>
    <option value="">Pilih Tahun Masuk</option>
    <?php
    for ($i = 2020; $i <= 2025; $i++) {
        echo "<option value='$i'>$i</option>";
    }
    ?>
</select>

        <input type="file" name="foto" accept="image/*" required>
        <button type="submit" class="btn"><i class="fas fa-plus-circle"></i> Tambah</button>
    </form>
</div>

</body>
</html>
