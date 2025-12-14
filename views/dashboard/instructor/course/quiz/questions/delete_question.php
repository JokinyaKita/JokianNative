<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'instructor') {
    header("Location: ../../login.php?error=access_denied");
    exit;
}

include '../../../../../../includes/config/koneksi.php';

$question_id = $_GET['question_id'] ?? null;
$quiz_id = $_GET['quiz_id'] ?? null;

if (!$question_id) {
    die("Question ID missing.");
}

$question_id = intval($question_id);
$instructor_id = $_SESSION['user']['user_id'];

// Validate ownership + get quiz_id (source of truth)
$row = mysqli_fetch_assoc(mysqli_query($conn, "
  SELECT qs.question_id, qs.quiz_id, qz.course_id
  FROM questions qs
  JOIN quizzes qz ON qs.quiz_id = qz.quiz_id
  JOIN courses c ON qz.course_id = c.course_id
  WHERE qs.question_id = $question_id AND c.instructor_id = '$instructor_id'
"));

if (!$row) {
    die("Access denied.");
}

$quiz_id_real = $row['quiz_id'];

// Delete choices first (safer if no cascade)
mysqli_query($conn, "DELETE FROM choices WHERE question_id = $question_id");

// Delete question
$del = mysqli_query($conn, "DELETE FROM questions WHERE question_id = $question_id");

if ($del) {
    header("Location: manage_questions.php?quiz_id=$quiz_id_real&sukses=question_deleted");
} else {
    header("Location: manage_questions.php?quiz_id=$quiz_id_real&error=delete_failed");
}

mysqli_close($conn);
exit;
