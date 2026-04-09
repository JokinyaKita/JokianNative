<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'superadmin') {
    header("Location: ../login.php?error=access_denied");
    exit;
}

include '../../../../includes/config/koneksi.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: manage_users.php?error=invalid_user");
    exit;
}

$id = intval($_GET['id']);

// Prevent deleting self (if session stores user id as id or user_id)
$currentId = $_SESSION['user']['id'] ?? ($_SESSION['user']['user_id'] ?? null);
if ($currentId !== null && intval($currentId) === $id) {
    header("Location: manage_users.php?error=cannot_delete_self");
    exit;
}

// Check user exists
$check = mysqli_query($conn, "SELECT user_id, role FROM users WHERE user_id = $id");
if (!$check) {
    header("Location: manage_users.php?error=query_failed");
    exit;
}

if (mysqli_num_rows($check) === 0) {
    header("Location: manage_users.php?error=user_not_found");
    exit;
}

$user = mysqli_fetch_assoc($check);

// Optional: block deleting another admin (uncomment if you want)
// if ($user['role'] === 'admin') {
//     header("Location: manage_users.php?error=cannot_delete_admin");
//     exit;
// }

// Delete user
$delete = mysqli_query($conn, "DELETE FROM users WHERE user_id = $id");

if ($delete) {
    header("Location: manage_users.php?sukses=user_deleted");
} else {
    header("Location: manage_users.php?error=delete_failed");
}

mysqli_close($conn);
exit;
