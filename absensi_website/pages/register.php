<?php
require_once '../includes/db.php';
$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['nama'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];
    $kelas = $_POST['kelas'] ?? null;
    $jurusan = $_POST['jurusan'] ?? null;
    $tahun = $_POST['tahun'] ?? null;

    $foto = $_FILES['foto'];
    $fotoName = '';

    // Validasi dan upload foto
    $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
    if ($foto['error'] === 0 && in_array($foto['type'], $allowedTypes)) {
        $ext = pathinfo($foto['name'], PATHINFO_EXTENSION);
        $fotoName = uniqid() . '.' . $ext;
        $uploadDir = '../uploads/';
        $uploadPath = $uploadDir . $fotoName;

        if (!move_uploaded_file($foto['tmp_name'], $uploadPath)) {
            $error = "Gagal mengupload foto.";
        }
    } else {
        $error = "Upload foto gagal atau format tidak didukung (hanya JPG, JPEG, PNG).";
    }

    if (empty($error)) {
        // Cek apakah username sudah ada
        $cek = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $cek->bind_param("s", $username);
        $cek->execute();
        $cek->store_result();

        if ($cek->num_rows > 0) {
            $error = "Username sudah digunakan!";
        } else {
            $stmt = $conn->prepare("INSERT INTO users (nama, username, password, foto, role, kelas, jurusan, tahun_masuk)
                                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssss", $nama, $username, $password, $fotoName, $role, $kelas, $jurusan, $tahun);
            if ($stmt->execute()) {
                $success = "Registrasi berhasil. Silakan login.";
            } else {
                $error = "Terjadi kesalahan saat registrasi.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
        body {
            background: #f0f2f5;
            font-family: Arial, sans-serif;
        }
        .container {
            background: #fff;
            width: 350px;
            margin: 80px auto;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #333;
        }
        form {
            margin-top: 20px;
        }
        .input-group {
            position: relative;
            margin-bottom: 20px;
        }
        .input-group i {
            position: absolute;
            top: 12px;
            left: 10px;
            color: #888;
        }
        input, select {
            width: 100%;
            padding: 10px 10px 10px 35px;
            border: 1px solid #ccc;
            border-radius: 8px;
            outline: none;
            transition: border 0.3s;
        }
        input:focus, select:focus {
            border-color: #4a90e2;
        }
        .btn {
            width: 100%;
            background-color: #4a90e2;
            color: #fff;
            padding: 10px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .btn:hover {
            background-color: #357abd;
        }
        .error, .success {
            text-align: center;
            margin-bottom: 15px;
        }
        .error {
            color: red;
        }
        .success {
            color: green;
        }
        p {
            text-align: center;
        }
        a {
            color: #4a90e2;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="container">
    <h2><i class="fas fa-user-plus"></i> Register</h2>
    <?php if ($error): ?>
        <p class="error"><?= $error ?></p>
    <?php elseif ($success): ?>
        <p class="success"><?= $success ?></p>
    <?php endif; ?>
    <form method="POST" enctype="multipart/form-data">
        <div class="input-group">
            <i class="fas fa-user"></i>
            <input type="text" name="nama" placeholder="Nama Lengkap" required>
        </div>
        <div class="input-group">
            <i class="fas fa-user"></i>
            <input type="text" name="username" placeholder="Username" required>
        </div>
        <div class="input-group">
            <i class="fas fa-lock"></i>
            <input type="password" name="password" placeholder="Password" required>
        </div>
        <div class="input-group">
            <i class="fas fa-user-tag"></i>
            <select name="role" required onchange="toggleSiswaFields(this.value)">
                <option value="">Pilih Role</option>
                <option value="siswa">Siswa</option>
                <option value="guru">Guru</option>
            </select>
        </div>

        <div id="siswa-fields" style="display:none;">
            <div class="input-group">
                <i class="fas fa-school"></i>
                <input type="text" name="kelas" placeholder="Kelas">
            </div>
            <div class="input-group">
                <i class="fas fa-graduation-cap"></i>
                <input type="text" name="jurusan" placeholder="Jurusan">
            </div>
            <div class="input-group">
                <i class="fas fa-calendar-alt"></i>
                <input type="number" name="tahun" placeholder="Tahun Masuk (contoh: 2023)">
            </div>
        </div>

        <div class="input-group">
            <i class="fas fa-image"></i>
            <input type="file" name="foto" accept="image/*" required>
        </div>

        <button type="submit" class="btn"><i class="fas fa-check-circle"></i> Register</button>
    </form>
    <p><a href="login.php"><i class="fas fa-sign-in-alt"></i> Sudah punya akun? Login</a></p>
</div>
<script>
    function toggleSiswaFields(role) {
        const siswaFields = document.getElementById('siswa-fields');
        siswaFields.style.display = (role === 'siswa') ? 'block' : 'none';
    }
</script>
</body>
</html>
