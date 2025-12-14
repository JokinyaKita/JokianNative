<?php
session_start();

// Protect page (admin only)
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../login.php?error=access_denied");
    exit;
}

include '../../../../includes/config/koneksi.php';

// Validate course_id
if (!isset($_GET['course_id']) || empty($_GET['course_id'])) {
    header("Location: manage_courses.php?error=invalid_course");
    exit;
}

$course_id = $_GET['course_id'];

// Optional: check if course exists first
$checkQuery = "SELECT course_id FROM courses WHERE course_id = '$course_id'";
$checkResult = mysqli_query($conn, $checkQuery);

if (mysqli_num_rows($checkResult) === 0) {
    header("Location: manage_courses.php?error=course_not_found");
    exit;
}

// Delete course
$deleteQuery = "DELETE FROM courses WHERE course_id = '$course_id'";

if (mysqli_query($conn, $deleteQuery)) {
    header("Location: manage_courses.php?sukses=course_deleted");
} else {
    header("Location: manage_courses.php?error=delete_failed");
}

mysqli_close($conn);
exit;
