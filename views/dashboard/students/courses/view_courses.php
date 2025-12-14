<?php
session_start();

// Check login and student role
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'student') {
    header("Location: ../login.php?error=access_denied");
    exit;
}

include '../../../../includes/config/koneksi.php';

// Validate ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: my_courses.php?error=invalid_id");
    exit;
}

$course_id = intval($_GET['id']);

// Fetch course using prepared statement (safer)
$stmt = $conn->prepare("
    SELECT c.*, u.name AS instructor_name, u.email AS instructor_email
    FROM courses c
    LEFT JOIN users u ON c.instructor_id = u.user_id
    WHERE c.course_id = ?
");
$stmt->bind_param("i", $course_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: my_courses.php?error=course_not_found");
    exit;
}

$course = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= htmlspecialchars($course['title']); ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-br from-slate-50 to-slate-100 min-h-screen">

  <!-- Header -->
  <header class="sticky top-0 z-40 bg-white/80 backdrop-blur border-b border-slate-200">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
      <div>
        <h1 class="text-xl sm:text-2xl font-bold text-slate-800">Course Details</h1>
        <p class="text-sm text-slate-500">Review course information before you start learning</p>
      </div>

      <a href="my_courses.php"
         class="px-4 py-2 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800 transition">
        ← Back
      </a>
    </div>
  </header>

  <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
      <!-- Top section -->
      <div class="p-6 border-b border-slate-200">
        <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
          <div>
            <h2 class="text-2xl sm:text-3xl font-bold text-slate-900">
              <?= htmlspecialchars($course['title']); ?>
            </h2>

            <div class="mt-3 flex flex-wrap items-center gap-2">
              <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700">
                Enrolled Course
              </span>

              <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">
                Course ID: <?= htmlspecialchars($course['course_id']); ?>
              </span>
            </div>
          </div>

          <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4 w-full md:w-80">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Instructor</p>
            <p class="mt-2 text-slate-800 font-semibold">
              <?= $course['instructor_name'] ? htmlspecialchars($course['instructor_name']) : "Not Assigned"; ?>
            </p>

            <?php if (!empty($course['instructor_email'])): ?>
              <p class="mt-1 text-sm text-slate-500">
                Contact: <?= htmlspecialchars($course['instructor_email']); ?>
              </p>
            <?php endif; ?>

            <p class="mt-3 text-xs text-slate-500">
              Published: <?= !empty($course['created_at']) ? htmlspecialchars($course['created_at']) : "-"; ?>
            </p>
          </div>
        </div>
      </div>

      <!-- Description -->
      <div class="p-6">
        <h3 class="text-lg font-semibold text-slate-800">About this course</h3>
        <div class="mt-3 text-slate-700 leading-relaxed">
          <?= nl2br(htmlspecialchars($course['description'] ?? 'No description provided.')); ?>
        </div>

        <hr class="my-6 border-slate-200">

        <!-- Actions -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
          <p class="text-sm text-slate-500">
            Ready to start? Click the button below.
          </p>

          <div class="flex gap-2">
            <a href="my_courses.php"
               class="px-5 py-3 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200 transition text-sm font-semibold">
              Back
            </a>

            <a href="start_course.php?id=<?= $course['course_id']; ?>"
               class="px-5 py-3 rounded-xl bg-blue-600 text-white hover:bg-blue-700 transition text-sm font-semibold">
              Start Learning
            </a>
          </div>
        </div>
      </div>
    </div>

  </main>

</body>
</html>
