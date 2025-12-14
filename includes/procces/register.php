<?php
include '../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name     = htmlspecialchars($_POST['name']);
    $email    = htmlspecialchars($_POST['email']);
    $password = $_POST['password'];
    $role     = $_POST['role'] ?? 'student';

    $allowed_roles = ['student', 'instructor'];
    if (!in_array($role, $allowed_roles)) {
        $role = 'student';
    }

    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Cek email
    $check = mysqli_query($conn, "SELECT 1 FROM users WHERE email='$email'");
    if (mysqli_num_rows($check) > 0) {
        header("Location: /views/auth/register/index.html?error=email_exists");
        exit;
    }

    // Insert user
    $query = "
        INSERT INTO users (name, email, password_hash, role)
        VALUES ('$name', '$email', '$password_hash', '$role')
    ";

    if (mysqli_query($conn, $query)) {
        header("Location: /views/auth/login/login.html?success=register_success");
    } else {
        header("Location: /views/auth/register/register.html?error=register_failed");
    }

    exit;
}
?>
