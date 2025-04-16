<?php
session_start();
if (isset($_SESSION['user'])) {
    if ($_SESSION['role'] == 'guru') {
        header("Location: pages/dashboard_guru.php");
    } else {
        header("Location: pages/dashboard_siswa.php");
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>SMK INDONESIA</title>
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background: #f0f2f5;
        }

        .container {
            max-width: 600px;
            margin: 100px auto;
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            text-align: center;
        }

        .logo {
            width: 120px;
            height: auto;
            margin-bottom: 20px;
        }

        h1 {
            font-size: 28px;
            color: #333;
            margin-bottom: 10px;
        }

        p {
            font-size: 16px;
            color: #555;
            margin-bottom: 30px;
        }

        .button-group {
            display: flex;
            justify-content: center;
            gap: 20px;
        }

        .btn {
            padding: 12px 24px;
            background-color: #4a90e2;
            color: white;
            border-radius: 5px;
            text-decoration: none;
            font-size: 16px;
            transition: 0.3s;
        }

        .btn:hover {
            background-color: #357abd;
        }

        .btn i {
            margin-right: 8px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Gambar Logo -->
        <img src="assets/logosklh.png" alt="Logo SMK" class="logo">

        <h1>Selamat Datang di SMK INDONESIA</h1>
        <p>Silakan login atau register untuk melanjutkan</p>

        <div class="button-group">
            <a href="pages/login.php" class="btn">
                <i class="fas fa-sign-in-alt"></i> Login
            </a>
            <a href="pages/register.php" class="btn">
                <i class="fas fa-user-plus"></i> Register
            </a>
        </div>
    </div>
</body>
</html>
