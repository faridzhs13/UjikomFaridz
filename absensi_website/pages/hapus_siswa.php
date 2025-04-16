<?php
session_start();
require_once '../includes/db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $conn->query("DELETE FROM users WHERE id = $id AND role = 'siswa'");
}

header("Location: siswa.php");
exit;
?>
