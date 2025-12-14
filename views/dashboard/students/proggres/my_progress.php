<?php
session_start();

// Ensure student
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'student') {
    header("Location: ../login.php?error=access_denied");
    exit;
}

include '../../../../includes/config/koneksi.php';

$user_id = (int) $_SESSION['user']['user_id'];

// Enrolled courses
$courses = mysqli_query($conn, "
    SELECT c.course_id, c.title
    FROM enrollments e
    JOIN courses c ON e.course_id = c.course_id
    WHERE e.user_id = $user_id
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>My Progress</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-br from-slate-50 to-slate-100 min-h-screen">

  <!-- Header -->
  <header class="sticky top-0 z-40 bg-white/80 backdrop-blur border-b border-slate-200">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
      <div>
        <h1 class="text-xl sm:text-2xl font-bold text-slate-800">My Learning Progress</h1>
        <p class="text-sm text-slate-500">Track quiz completion and your average score per course</p>
      </div>

      <a href="../dashboard.php"
         class="px-4 py-2 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800 transition">
        ← Back to Dashboard
      </a>
    </div>
  </header>

  <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <?php if (!$courses || mysqli_num_rows($courses) === 0): ?>
      <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-10 text-center">
        <div class="mx-auto w-14 h-14 rounded-2xl bg-slate-100 flex items-center justify-center mb-4">
          <span class="text-2xl">📊</span>
        </div>
        <p class="text-slate-800 font-semibold">No progress yet</p>
        <p class="text-slate-500 text-sm mt-1">You haven’t enrolled in any courses.</p>
        <div class="mt-6">
          <a href="../enroll/enroll_courses.php"
             class="inline-flex items-center px-5 py-3 rounded-xl bg-blue-600 text-white font-semibold hover:bg-blue-700 transition">
            Enroll in a Course
          </a>
        </div>
      </div>
    <?php else: ?>

      <!-- Overview Cards -->
      <?php
        // Optional overall summary
        $overall_total_quiz = 0;
        $overall_attempted  = 0;

        // We'll compute overall avg from attempts directly:
        $overallAvgRow = mysqli_fetch_assoc(mysqli_query($conn, "
          SELECT AVG(score) AS avg_score
          FROM quiz_attempts
          WHERE user_id = $user_id
        "));
        $overall_avg = $overallAvgRow['avg_score'] ?? null;

        // Need to loop once to count totals? We'll do lightweight totals per course below and sum.
        // We'll store course rows in an array to avoid rewinding mysqli result set issues.
        $courseList = [];
        while ($course = mysqli_fetch_assoc($courses)) {
          $courseList[] = $course;
        }

        foreach ($courseList as $c) {
          $cid = (int)$c['course_id'];

          $tRow = mysqli_fetch_assoc(mysqli_query($conn, "
            SELECT COUNT(*) AS total
            FROM quizzes
            WHERE course_id = $cid
          "));
          $aRow = mysqli_fetch_assoc(mysqli_query($conn, "
            SELECT COUNT(*) AS attempted
            FROM quiz_attempts qa
            JOIN quizzes q ON qa.quiz_id = q.quiz_id
            WHERE qa.user_id = $user_id AND q.course_id = $cid
          "));

          $overall_total_quiz += (int)($tRow['total'] ?? 0);
          $overall_attempted  += (int)($aRow['attempted'] ?? 0);
        }

        $overall_progress = ($overall_total_quiz > 0)
          ? round(($overall_attempted / $overall_total_quiz) * 100)
          : 0;
      ?>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-5">
          <p class="text-sm text-slate-500">Overall Progress</p>
          <p class="text-3xl font-bold text-slate-900 mt-1"><?= $overall_progress ?>%</p>
          <div class="mt-3 w-full bg-slate-200 rounded-full h-2">
            <div class="bg-blue-600 h-2 rounded-full" style="width: <?= $overall_progress ?>%"></div>
          </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-5">
          <p class="text-sm text-slate-500">Quizzes Completed</p>
          <p class="text-3xl font-bold text-slate-900 mt-1"><?= $overall_attempted ?>/<?= $overall_total_quiz ?></p>
          <p class="text-xs text-slate-500 mt-2">Based on quizzes available in your enrolled courses</p>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-5">
          <p class="text-sm text-slate-500">Average Score</p>
          <p class="text-3xl font-bold text-slate-900 mt-1">
            <?= $overall_avg !== null ? rtrim(rtrim(number_format((float)$overall_avg, 1, '.', ''), '0'), '.') . '%' : '-' ?>
          </p>
          <p class="text-xs text-slate-500 mt-2">Average of all your quiz attempts</p>
        </div>
      </div>

      <!-- Per-course progress -->
      <div class="space-y-5">
        <?php foreach ($courseList as $course) : ?>
          <?php
            $course_id = (int)$course['course_id'];

            $totalRow = mysqli_fetch_assoc(mysqli_query($conn, "
                SELECT COUNT(*) AS total
                FROM quizzes
                WHERE course_id = $course_id
            "));
            $attemptRow = mysqli_fetch_assoc(mysqli_query($conn, "
                SELECT COUNT(*) AS attempted
                FROM quiz_attempts qa
                JOIN quizzes q ON qa.quiz_id = q.quiz_id
                WHERE qa.user_id = $user_id AND q.course_id = $course_id
            "));
            $avgRow = mysqli_fetch_assoc(mysqli_query($conn, "
                SELECT AVG(score) AS avg_score
                FROM quiz_attempts qa
                JOIN quizzes q ON qa.quiz_id = q.quiz_id
                WHERE qa.user_id = $user_id AND q.course_id = $course_id
            "));

            $total_quiz     = (int)($totalRow['total'] ?? 0);
            $attempted_quiz = (int)($attemptRow['attempted'] ?? 0);
            $avg_score      = $avgRow['avg_score'];

            $progress = ($total_quiz > 0) ? round(($attempted_quiz / $total_quiz) * 100) : 0;

            $status = ($total_quiz > 0 && $progress === 100) ? 'Completed' : 'In Progress';

            // Status style
            $statusClass = ($status === 'Completed')
              ? 'bg-emerald-50 text-emerald-700 border-emerald-100'
              : 'bg-amber-50 text-amber-700 border-amber-100';

            // Progress bar color could stay blue, consistent with your theme
          ?>

          <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
              <div>
                <h2 class="text-lg sm:text-xl font-semibold text-slate-900">
                  <?= htmlspecialchars($course['title']) ?>
                </h2>
                <p class="text-sm text-slate-500 mt-1">Course ID: <?= $course_id ?></p>
              </div>

              <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border <?= $statusClass ?>">
                <?= $status ?>
              </span>
            </div>

            <div class="mt-5">
              <div class="flex items-center justify-between text-sm text-slate-600 mb-2">
                <span>Progress</span>
                <span class="font-semibold text-slate-800"><?= $progress ?>%</span>
              </div>

              <div class="w-full bg-slate-200 rounded-full h-3">
                <div class="bg-blue-600 h-3 rounded-full" style="width: <?= $progress ?>%"></div>
              </div>
            </div>

            <div class="mt-5 grid grid-cols-1 sm:grid-cols-3 gap-3 text-center">
              <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4">
                <p class="text-xs text-slate-500">Quizzes Completed</p>
                <p class="text-2xl font-bold text-slate-900 mt-1"><?= $attempted_quiz ?>/<?= $total_quiz ?></p>
              </div>

              <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4">
                <p class="text-xs text-slate-500">Average Score</p>
                <p class="text-2xl font-bold text-slate-900 mt-1">
                  <?= $avg_score !== null ? rtrim(rtrim(number_format((float)$avg_score, 1, '.', ''), '0'), '.') . '%' : '-' ?>
                </p>
              </div>

              <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4">
                <p class="text-xs text-slate-500">Quizzes Available</p>
                <p class="text-2xl font-bold text-slate-900 mt-1"><?= $total_quiz ?></p>
              </div>
            </div>
          </div>

        <?php endforeach; ?>
      </div>

    <?php endif; ?>

  </main>

</body>
</html>
