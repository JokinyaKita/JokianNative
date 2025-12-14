<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['email'] !== 'admin@gmail.com') {
  header("Location: ../index.html?error=akses_ditolak");
  exit;
}

include '../config/koneksi.php';

if (isset($_GET['id'])) {
  $id = intval($_GET['id']);
  mysqli_query($conn, "DELETE FROM users WHERE id = $id");
}

header("Location: ../dashboard/index.php?sukses=hapus");
exit;
?>
