<?php
session_start();


// Check if user is logged in and has 'student' role
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'student') {
    header("Location: ../login.php?error=access_denied");
    exit;
}

include '../config/koneksi.php';

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Ambil user ID dari sesi
$student_id = $_SESSION['user']['user_id'];


if (!$student_id) {
    die("Error: User ID tidak ditemukan di session.");
}

// Validasi course_id
if (isset($_GET['course_id']) && is_numeric($_GET['course_id'])) {
    $course_id = intval($_GET['course_id']);

    // Cek apakah course ada
    $stmt = mysqli_prepare($conn, "SELECT 1 FROM courses WHERE course_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $course_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) === 0) {
        header("Location: enroll_courses.php?error=course_not_found");
        exit;
    }

    // ⛔ Cek apakah sudah terdaftar sebelumnya
    $check = mysqli_prepare($conn, "
        SELECT 1 FROM enrollments 
        WHERE user_id = ? AND course_id = ?
    ");
    mysqli_stmt_bind_param($check, "ii", $student_id, $course_id);
    mysqli_stmt_execute($check);
    $res = mysqli_stmt_get_result($check);

    if (mysqli_num_rows($res) > 0) {
        // Sudah pernah daftar → hindari duplikat
        header("Location: ../../views/dashboard/students/courses/my_courses.php?warning=already_enrolled");
        exit;
    }


    
    // Jika belum ada → insert data
    $stmt_enroll = mysqli_prepare($conn, "
        INSERT INTO enrollments (user_id, course_id) 
        VALUES (?, ?)
    ");
    mysqli_stmt_bind_param($stmt_enroll, "ii", $student_id, $course_id);

    if (mysqli_stmt_execute($stmt_enroll)) {
        header("Location: ../../views/dashboard/students/courses/my_courses.php?sukses=enrolled_successfully");
        exit;
    } else {
        header("Location: ../../views/dashboard/students/enroll/enroll_courses.php?error=enrollment_failed");
        exit;
    }

} else {
    header("Location: enroll_courses.php?error=invalid_course_id");
    exit;
}

mysqli_close($conn);
?>
