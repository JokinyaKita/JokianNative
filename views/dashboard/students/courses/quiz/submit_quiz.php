<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'student') {
    die("Access denied.");
}

include '../../../../../includes/config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid request.");
}

$quiz_id = (int) ($_POST['quiz_id'] ?? 0);
$user_id = (int) ($_SESSION['user']['user_id'] ?? 0);
$answers = $_POST['answers'] ?? [];

if ($quiz_id === 0 || $user_id === 0) {
    die("Invalid submission.");
}

/* ===============================
   Fetch quiz + course
================================ */
$quizRes = mysqli_query($conn, "
    SELECT quiz_id, course_id
    FROM quizzes
    WHERE quiz_id = $quiz_id
    LIMIT 1
");
$quizRow = $quizRes ? mysqli_fetch_assoc($quizRes) : null;

if (!$quizRow) {
    die("Quiz not found.");
}

$course_id = (int) $quizRow['course_id'];

/* ===============================
   Check enrollment (must be enrolled)
================================ */
$enroll = mysqli_query($conn, "
    SELECT 1 FROM enrollments
    WHERE user_id = $user_id AND course_id = $course_id
    LIMIT 1
");
if (!$enroll || mysqli_num_rows($enroll) === 0) {
    die("You are not enrolled in this course.");
}

/* ===============================
   Prevent double attempt
================================ */
$check = mysqli_query($conn, "
    SELECT 1 FROM quiz_attempts
    WHERE user_id = $user_id AND quiz_id = $quiz_id
    LIMIT 1
");

if ($check && mysqli_num_rows($check) > 0) {
    die("You already attempted this quiz.");
}

/* ===============================
   Count total questions (from DB)
================================ */
$totalRes = mysqli_query($conn, "
    SELECT COUNT(*) AS total
    FROM questions
    WHERE quiz_id = $quiz_id
");
$totalRow = $totalRes ? mysqli_fetch_assoc($totalRes) : null;

$total_questions = (int)($totalRow['total'] ?? 0);
if ($total_questions <= 0) {
    die("This quiz has no questions.");
}

/* ===============================
   Score calculation
   - Validate each submitted answer:
     choice must belong to question, and question must belong to this quiz
================================ */
$correct_answers = 0;

if (!is_array($answers)) {
    $answers = [];
}

foreach ($answers as $question_id => $choice_id) {
    $question_id = (int) $question_id;
    $choice_id   = (int) $choice_id;

    if ($question_id <= 0 || $choice_id <= 0) continue;

    $res = mysqli_query($conn, "
        SELECT ch.is_correct
        FROM choices ch
        JOIN questions q ON ch.question_id = q.question_id
        WHERE ch.choice_id = $choice_id
          AND ch.question_id = $question_id
          AND q.quiz_id = $quiz_id
        LIMIT 1
    ");

    if ($res && ($row = mysqli_fetch_assoc($res))) {
        if ((int)$row['is_correct'] === 1) {
            $correct_answers++;
        }
    }
}

// Score in percent (based on total questions in DB)
$score = round(($correct_answers / $total_questions) * 100, 2);

/* ===============================
   Save attempt
================================ */
$insert = mysqli_query($conn, "
    INSERT INTO quiz_attempts (user_id, quiz_id, score)
    VALUES ($user_id, $quiz_id, $score)
");

if (!$insert) {
    die("Failed to save attempt: " . mysqli_error($conn));
}

/* ===============================
   Redirect to result
================================ */
header("Location: quiz_result.php?quiz_id=$quiz_id");
exit;
