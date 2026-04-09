<?php
session_start();
include '../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $query  = "SELECT * FROM users WHERE email = '$email' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) === 1) {

        $user = mysqli_fetch_assoc($result);

        if (!password_verify($password, $user['password_hash'])) {
            header("Location: ../../views/auth/login/login.html?error=incorrect_password");
            exit;
        }

        // Simpan user ke session
        $_SESSION['user'] = [
            'user_id' => $user['user_id'],
            'name'    => $user['name'],
            'email'   => $user['email'],
            'role'    => $user['role']
        ];

        // === REDIRECT SESUAI ROLE ===
        switch ($user['role']) {
            case 'superadmin':
                header("Location: ../../views/dashboard/admin/dashboard.php");
                break;

            case 'admin':
                session_destroy();
                header("Location: ../../views/auth/login/login.html?error=superadmin_only");
                break;

            case 'instructor':
                header("Location: ../../views/dashboard/instructor/dashboard.php");
                break;

            case 'student':
                header("Location: ../../views/dashboard/students/dashboard.php");
                break;

            default:
                session_destroy();
                header("Location: ../../views/auth/login/login.html?error=invalid_role");
                break;
        }
        exit;

    } else {
        header("Location: ../../views/auth/login/login.html?error=user_not_found");
        exit;
    }
}
?>
