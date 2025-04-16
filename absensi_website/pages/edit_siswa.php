<?php
session_start();
require_once '../includes/db.php';

if (!isset($_GET['id'])) {
    header("Location: siswa.php");
    exit;
}

$id = $_GET['id'];

// Ambil data siswa terlebih dahulu agar bisa akses foto lama
$data = $conn->query("SELECT * FROM users WHERE id = $id")->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'];
    $username = $_POST['username'];
    $kelas = $_POST['kelas'];
    $jurusan = $_POST['jurusan'];
    $tahun = $_POST['tahun'];

    // Ambil foto lama
    $foto = $data['foto'];

    // Cek apakah ada foto baru diupload
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === 0) {
        $foto_tmp = $_FILES['foto']['tmp_name'];
        $foto_name = time() . '_' . $_FILES['foto']['name'];
        $foto_path = "../uploads/" . $foto_name;

        // Simpan file baru
        move_uploaded_file($foto_tmp, $foto_path);

        // Hapus foto lama jika ada
        if (!empty($foto) && file_exists("../uploads/" . $foto)) {
            unlink("../uploads/" . $foto);
        }

        // Set nama file foto baru
        $foto = $foto_name;
    }

    // Update data siswa ke database
    $stmt = $conn->prepare("UPDATE users SET nama=?, username=?, kelas=?, jurusan=?, tahun_masuk=?, foto=? WHERE id=?");
    $stmt->bind_param("ssssssi", $nama, $username, $kelas, $jurusan, $tahun, $foto, $id);
    $stmt->execute();

    header("Location: siswa.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Siswa</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 0;
        }

        form {
            background-color: #fff;
            width: 60%;
            margin: 50px auto;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
            font-size: 24px;
        }

        input, select {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            box-sizing: border-box;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #4a90e2;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 20px;
        }

        button:hover {
            background-color: #357abd;
        }

        .btn-kembali {
            display: inline-block;
            width: 100%;
            padding: 12px;
            background-color: #ccc;
            color: #333;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 10px;
            text-align: center;
            text-decoration: none;
        }

        .btn-kembali:hover {
            background-color: #aaa;
        }

        input:focus, select:focus {
            border-color: #4a90e2;
            outline: none;
        }

        .foto-preview {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            margin: 10px 0;
        }

        @media screen and (max-width: 768px) {
            form {
                width: 90%;
            }

            button, .btn-kembali {
                font-size: 14px;
            }

            input {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>


<div class="container">
    <form method="POST" enctype="multipart/form-data">
        <h2>Edit Siswa</h2>

        <?php if (!empty($data['foto']) && file_exists("../uploads/{$data['foto']}")): ?>
            <img src="../uploads/<?= $data['foto'] ?>" alt="Foto Siswa" class="foto-preview">
        <?php else: ?>
            <img src="../assets/default.png" alt="Default" class="foto-preview">
        <?php endif; ?>

        <input type="text" name="nama" value="<?= htmlspecialchars($data['nama']) ?>" required>
        <input type="text" name="username" value="<?= htmlspecialchars($data['username']) ?>" required>

        <select name="kelas" required>
            <option value="">Pilih Kelas</option>
            <?php
            $kelas_options = ['X', 'XI', 'XII'];
            foreach ($kelas_options as $kelas) {
                $selected = ($data['kelas'] === $kelas) ? 'selected' : '';
                echo "<option value='$kelas' $selected>$kelas</option>";
            }
            ?>
        </select>

        <select name="jurusan" required>
            <option value="">Pilih Jurusan</option>
            <?php
            $jurusan_options = ['TKJ', 'RPL'];
            foreach ($jurusan_options as $jurusan) {
                $selected = ($data['jurusan'] === $jurusan) ? 'selected' : '';
                echo "<option value='$jurusan' $selected>$jurusan</option>";
            }
            ?>
        </select>

        <select name="tahun" required>
            <option value="">Pilih Tahun Masuk</option>
            <?php
            for ($tahun = 2020; $tahun <= 2025; $tahun++) {
                $selected = ($data['tahun_masuk'] == $tahun) ? 'selected' : '';
                echo "<option value='$tahun' $selected>$tahun</option>";
            }
            ?>
        </select>

        <input type="file" name="foto" accept="image/*">

        <button type="submit"><i class="fas fa-save"></i> Simpan</button>
        <a href="siswa.php" class="btn-kembali"><i class="fas fa-arrow-left"></i> Kembali</a>
    </form>
</div>

</body>
</html>
