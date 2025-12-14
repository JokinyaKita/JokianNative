<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include '../../../../../includes/config/koneksi.php';

$question_id = $_GET['question_id'] ?? null;
$quiz_id     = $_GET['quiz_id'] ?? null;

if (!$question_id || !$quiz_id) {
    die("Invalid request.");
}

// Hapus choices
mysqli_query($conn, "DELETE FROM choices WHERE question_id = $question_id");

// Hapus question
mysqli_query($conn, "DELETE FROM questions WHERE question_id = $question_id");

// Redirect kembali
header("Location: manage_questions.php?quiz_id=$quiz_id&deleted=1");
exit;
