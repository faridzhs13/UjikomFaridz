<?php
$role = $_SESSION['role'];
?>

<div class="sidebar">
    <h3><i class="fas fa-user-check"></i> Absensi</h3>
    <ul>
        <?php if ($role === 'siswa'): ?>
            <li><a href="dashboard_siswa.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
        <?php else: ?>
            <li><a href="dashboard_guru.php"><i class="fas fa-chalkboard-teacher"></i> Dashboard</a></li>
            <li><a href="siswa.php"><i class="fas fa-users"></i> Siswa</a></li>
            
        <?php endif; ?>
        <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</div>

<!-- Add Font Awesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
