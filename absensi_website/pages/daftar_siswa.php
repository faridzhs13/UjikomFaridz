<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'guru') {
    header("Location: login.php");
    exit;
}

// Ambil data siswa dari database
$sql = "SELECT * FROM users WHERE role = 'siswa' ORDER BY nama ASC";
$siswa = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Siswa</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
        body {
            background-color: #f0f2f5;
            font-family: Arial, sans-serif;
        }
        .container {
            margin-left: 250px;
            padding: 100px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            max-width: 1200px;
            margin-top: 50px;
        }
        h2 {
            color: #333;
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: center;
        }
        th {
            background-color: #4a90e2;
            color: white;
        }
        td {
            background-color: #f9f9f9;
        }
        .foto-siswa {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }
    </style>
</head>
<body>

<?php include '../includes/sidebarguru.php'; ?>

<div class="container">
    <h2><i class="fas fa-users icon"></i> Daftar Seluruh Siswa</h2>

    <!-- Tabel Daftar Siswa -->
    <table>
        <thead>
            <tr>
                <th>Foto</th>
                <th>Nama</th>
                <th>Username</th>
                <th>Kelas</th>
                <th>Jurusan</th>
                <th>Tahun Masuk</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = $siswa->fetch_assoc()): ?>
            <tr>
                <td>
                    <?php if (!empty($row['foto']) && file_exists("../uploads/{$row['foto']}")): ?>
                        <img src="../uploads/<?= $row['foto'] ?>" alt="Foto" class="foto-siswa">
                    <?php else: ?>
                        <img src="../assets/default.png" alt="Default" class="foto-siswa">
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($row['nama']) ?></td>
                <td><?= htmlspecialchars($row['username']) ?></td>
                <td><?= $row['kelas'] ?></td>
                <td><?= $row['jurusan'] ?></td>
                <td><?= $row['tahun_masuk'] ?></td>
                <td>
                    <a href="edit_siswa.php?id=<?= $row['id'] ?>" class="btn"><i class="fas fa-edit"></i></a>
                    <a href="hapus_siswa.php?id=<?= $row['id'] ?>" class="btn" onclick="return confirm('Yakin ingin menghapus?')"><i class="fas fa-trash-alt"></i></a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>
