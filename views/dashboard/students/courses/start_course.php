<?php
session_start();

// Ensure user is a student
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'student') {
    header("Location: ../login.php?error=access_denied");
    exit;
}

include '../../../../includes/config/koneksi.php';

// Validate course_id
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid course ID.");
}

$course_id = (int) $_GET['id'];
$user_id   = $_SESSION['user']['user_id'];

// ==== CHECK ENROLLMENT ====
$check = mysqli_query($conn, "
    SELECT 1 FROM enrollments 
    WHERE user_id = $user_id AND course_id = $course_id
");

if (mysqli_num_rows($check) === 0) {
    die("You are not enrolled in this course.");
}

// ==== FETCH COURSE DETAILS ====
$result = mysqli_query($conn, "
    SELECT c.*, u.name AS instructor_name
    FROM courses c
    LEFT JOIN users u ON c.instructor_id = u.user_id
    WHERE c.course_id = $course_id
");

if (mysqli_num_rows($result) === 0) {
    die("Course not found.");
}

$course = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Start Learning - <?= htmlspecialchars($course['title']); ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-br from-slate-50 to-slate-100 min-h-screen">

  <!-- Header -->
  <header class="sticky top-0 z-40 bg-white/80 backdrop-blur border-b border-slate-200">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
      <div>
        <h1 class="text-xl sm:text-2xl font-bold text-slate-800">Start Learning</h1>
        <p class="text-sm text-slate-500">Choose what you want to learn next</p>
      </div>

      <a href="./view_courses.php?id=<?= $course_id ?>"
         class="px-4 py-2 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800 transition">
        ← Back
      </a>
    </div>
  </header>

  <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">

      <!-- Course header -->
      <div class="p-6 border-b border-slate-200">
        <h2 class="text-2xl sm:text-3xl font-bold text-slate-900">
          <?= htmlspecialchars($course['title']); ?>
        </h2>

        <div class="mt-3 flex flex-wrap items-center gap-2">
          <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700">
            Enrolled
          </span>

          <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">
            Course ID: <?= htmlspecialchars($course_id); ?>
          </span>
        </div>

        <p class="mt-3 text-sm text-slate-600">
          <span class="font-semibold text-slate-700">Instructor:</span>
          <?= htmlspecialchars($course['instructor_name'] ?? 'Unknown'); ?>
        </p>
      </div>

      <!-- Content section -->
      <div class="p-6">
        <h3 class="text-lg font-semibold text-slate-800">Course Content</h3>
        <p class="text-sm text-slate-500 mt-1">This page is your starting point for learning in this course.</p>

        <div class="mt-5 grid grid-cols-1 sm:grid-cols-2 gap-4">

          <!-- Quiz -->
          <a href="../courses/quiz/view_quiz.php?id=<?= $course_id ?>"
             class="group bg-white border border-slate-200 rounded-2xl p-5 hover:shadow-md transition">
            <div class="flex items-start justify-between">
              <div class="w-12 h-12 rounded-2xl bg-purple-50 flex items-center justify-center">
                <span class="text-xl">❓</span>
              </div>
              <span class="text-slate-400 group-hover:text-slate-700 transition">→</span>
            </div>
            <h4 class="mt-4 text-base font-semibold text-slate-800">Quiz</h4>
            <p class="mt-1 text-sm text-slate-500">Answer questions and test your understanding.</p>
            <span class="mt-4 inline-flex text-sm font-semibold text-purple-700">Open Quiz</span>
          </a>

          <!-- Placeholder (optional for future materials/modules) -->
          <div class="bg-slate-50 border border-dashed border-slate-300 rounded-2xl p-5">
            <div class="w-12 h-12 rounded-2xl bg-slate-200/60 flex items-center justify-center">
              <span class="text-xl">📚</span>
            </div>
            <h4 class="mt-4 text-base font-semibold text-slate-700">Materials (Coming Soon)</h4>
            <p class="mt-1 text-sm text-slate-500">Your learning materials will appear here.</p>
          </div>

        </div>
      </div>

    </div>

  </main>

</body>
</html>
