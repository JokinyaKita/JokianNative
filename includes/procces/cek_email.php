<?php
include '../config/koneksi.php';

if (isset($_POST['email'])) {
  $email = mysqli_real_escape_string($conn, $_POST['email']);
  $query = "SELECT * FROM users WHERE email = '$email'";
  $result = mysqli_query($conn, $query);

  echo (mysqli_num_rows($result) > 0) ? "ada" : "tidak_ada";
}
?>