<?php
session_start();
// Hapus sesi pengguna
session_unset();
session_destroy();
// Redirect ke halaman login
header("Location: index.php");
exit;
?>
