<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'student') {
    die("Access denied.");
}

include '../../../../../includes/config/koneksi.php';

$quiz_id = (int) ($_GET['quiz_id'] ?? 0);
$user_id = (int) ($_SESSION['user']['user_id'] ?? 0);

if ($quiz_id === 0 || $user_id === 0) {
    die("Invalid quiz.");
}

/* ===============================
   Get attempt
================================ */
$attemptRes = mysqli_query($conn, "
    SELECT * FROM quiz_attempts
    WHERE user_id = $user_id AND quiz_id = $quiz_id
    LIMIT 1
");
$attempt = $attemptRes ? mysqli_fetch_assoc($attemptRes) : null;

if (!$attempt) {
    die("Quiz attempt not found.");
}

/* ===============================
   Get quiz + course info
================================ */
$quizRes = mysqli_query($conn, "
    SELECT q.title, q.course_id, c.title AS course_title
    FROM quizzes q
    JOIN courses c ON q.course_id = c.course_id
    WHERE q.quiz_id = $quiz_id
    LIMIT 1
");
$quiz = $quizRes ? mysqli_fetch_assoc($quizRes) : null;

if (!$quiz) {
    die("Quiz not found.");
}

$course_id = (int) $quiz['course_id'];

/* ===============================
   Enrollment check (must be enrolled)
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
   Total questions
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
   Score + derived stats
================================ */
$score  = (float) ($attempt['score'] ?? 0);
$passed = $score >= 60;

$correct_est = (int) round(($score / 100) * $total_questions);
if ($correct_est > $total_questions) $correct_est = $total_questions;
if ($correct_est < 0) $correct_est = 0;

$wrong_est = $total_questions - $correct_est;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Quiz Result</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-br from-slate-50 to-slate-100 min-h-screen">

  <!-- Header -->
  <header class="sticky top-0 z-40 bg-white/80 backdrop-blur border-b border-slate-200">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
      <div>
        <h1 class="text-xl sm:text-2xl font-bold text-slate-800">Quiz Result</h1>
        <p class="text-sm text-slate-500">Here’s your score and performance summary</p>
      </div>

      <a href="../start_course.php?id=<?= $course_id ?>"
         class="px-4 py-2 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800 transition">
        ← Back to Course
      </a>
    </div>
  </header>

  <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">

      <!-- Title -->
      <div class="p-6 border-b border-slate-200">
        <h2 class="text-2xl sm:text-3xl font-bold text-slate-900">
          <?= htmlspecialchars($quiz['title']) ?>
        </h2>
        <p class="text-slate-500 mt-1">
          Course: <span class="font-semibold text-slate-700"><?= htmlspecialchars($quiz['course_title']) ?></span>
        </p>

        <div class="mt-3 flex flex-wrap items-center gap-2">
          <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">
            Quiz ID: <?= htmlspecialchars($quiz_id) ?>
          </span>

          <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold <?= $passed ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700' ?>">
            <?= $passed ? 'PASSED ' : 'FAILED ' ?>
          </span>
        </div>
      </div>

      <!-- Score -->
      <div class="p-6">
        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-6 text-center">
          <p class="text-sm text-slate-600 font-semibold">Your Score</p>
          <p class="mt-2 text-5xl font-extrabold <?= $passed ? 'text-emerald-600' : 'text-red-600' ?>">
            <?= rtrim(rtrim(number_format($score, 2, '.', ''), '0'), '.') ?>%
          </p>
          <p class="mt-3 text-sm text-slate-500">
            Passing score: <span class="font-semibold text-slate-700">60%</span>
          </p>
        </div>

        <!-- Stats -->
        <div class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-4">
          <div class="bg-white border border-slate-200 rounded-2xl p-5 text-center">
            <p class="text-sm text-slate-500">Total Questions</p>
            <p class="text-3xl font-bold text-slate-800 mt-1"><?= $total_questions ?></p>
          </div>

          <div class="bg-white border border-slate-200 rounded-2xl p-5 text-center">
            <p class="text-sm text-slate-500">Correct (estimated)</p>
            <p class="text-3xl font-bold text-emerald-600 mt-1"><?= $correct_est ?></p>
          </div>

          <div class="bg-white border border-slate-200 rounded-2xl p-5 text-center">
            <p class="text-sm text-slate-500">Wrong (estimated)</p>
            <p class="text-3xl font-bold text-red-600 mt-1"><?= $wrong_est ?></p>
          </div>
        </div>

        <!-- Actions -->
        <div class="mt-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
          <p class="text-sm text-slate-500">
            You can return to the course and continue learning.
          </p>

          <div class="flex gap-2">
            <a href="../start_course.php?id=<?= $course_id ?>"
               class="px-5 py-3 rounded-xl bg-blue-600 text-white font-semibold hover:bg-blue-700 transition">
              Back to Course
            </a>
          </div>
        </div>

      </div>
    </div>

  </main>

</body>
</html>
