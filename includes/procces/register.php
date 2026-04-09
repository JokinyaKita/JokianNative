<?php
include '../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name     = htmlspecialchars(trim($_POST['name'] ?? ''));
    $email    = htmlspecialchars(trim($_POST['email'] ?? ''));
    $password = $_POST['password'];
    // Public registration is restricted to student only.
    $role     = 'student';

    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Cek email
    $check = mysqli_query($conn, "SELECT 1 FROM users WHERE email='$email'");
    if (mysqli_num_rows($check) > 0) {
        header("Location: ../../views/auth/register/register.html?error=email_terdaftar");
        exit;
    }

    // Insert user
    $query = "
        INSERT INTO users (name, email, password_hash, role)
        VALUES ('$name', '$email', '$password_hash', '$role')
    ";

    if (mysqli_query($conn, $query)) {
        header("Location: ../../views/auth/login/login.html?sukses=daftar_berhasil");
    } else {
        header("Location: ../../views/auth/register/register.html?error=gagal_mendaftar");
    }

    exit;
}
?>
