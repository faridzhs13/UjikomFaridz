<?php
session_start();
require_once '../includes/db.php';

$error = ''; // Tambahkan ini untuk mendeklarasikan variabel error

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND role = ?");
    $stmt->bind_param("ss", $username, $role);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        if ($role === 'guru') {
            header("Location: dashboard_guru.php");
        } else {
            header("Location: dashboard_siswa.php");
        }
        exit;
    } else {
        $error = "Username atau password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
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
        .error {
            color: red;
            text-align: center;
            margin-bottom: 15px;
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
    <h2><i class="fas fa-sign-in-alt"></i> Login</h2>
    <?php if ($error): ?>
        <p class="error"><?= $error ?></p>
    <?php endif; ?>
    <form method="POST">
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
            <select name="role" required>
                <option value="">Pilih Role</option>
                <option value="siswa">Siswa</option>
                <option value="guru">Guru</option>
            </select>
        </div>
        <button type="submit" class="btn"><i class="fas fa-arrow-right-to-bracket"></i> Login</button>
    </form>
    <p><a href="register.php"><i class="fas fa-user-plus"></i> Belum punya akun? Register</a></p>
</div>
</body>
</html>