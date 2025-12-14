<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'student') {
    header("Location: ../login.php?error=access_denied");
    exit;
}

include '../../../../includes/config/koneksi.php';

$user_id = $_SESSION['user']['user_id'];

$query = "SELECT c.* FROM courses c
          JOIN enrollments e ON c.course_id = e.course_id
          WHERE e.user_id = $user_id";

$result = mysqli_query($conn, $query);

$success = $_GET['sukses'] ?? null;
$error   = $_GET['error'] ?? null;
$warning = $_GET['warning'] ?? null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>My Courses</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-br from-slate-50 to-slate-100 min-h-screen">

  <!-- Header -->
  <header class="sticky top-0 z-40 bg-white/80 backdrop-blur border-b border-slate-200">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
      <div>
        <h1 class="text-xl sm:text-2xl font-bold text-slate-800">My Enrolled Courses</h1>
        <p class="text-sm text-slate-500">Access courses you’ve joined and continue learning</p>
      </div>

      <a href="../dashboard.php"
         class="px-4 py-2 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800 transition">
        ← Back to Dashboard
      </a>
    </div>
  </header>

  <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <!-- Alerts -->
    <?php if ($success === "enrolled_successfully"): ?>
      <div class="mb-6 p-4 rounded-xl bg-emerald-50 text-emerald-700 font-semibold border border-emerald-100">
        You have successfully enrolled in the course!
      </div>
    <?php endif; ?>

    <?php if ($warning === "already_enrolled"): ?>
      <div class="mb-6 p-4 rounded-xl bg-amber-50 text-amber-700 font-semibold border border-amber-100">
        ⚠ You are already enrolled in this course.
      </div>
    <?php endif; ?>

    <?php if ($error === "enrollment_failed"): ?>
      <div class="mb-6 p-4 rounded-xl bg-red-50 text-red-700 font-semibold border border-red-100">
        ❌ Enrollment failed. Please try again later.
      </div>
    <?php endif; ?>

    <!-- Content -->
    <?php if (mysqli_num_rows($result) === 0): ?>
      <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-10 text-center">
        <div class="mx-auto w-14 h-14 rounded-2xl bg-slate-100 flex items-center justify-center mb-4">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
        <p class="text-slate-800 font-semibold">No courses yet</p>
        <p class="text-slate-500 text-sm mt-1">Enroll in a course to see it here.</p>
      </div>
    <?php else: ?>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <?php while ($course = mysqli_fetch_assoc($result)) : ?>
          <?php
            $title = htmlspecialchars($course['title']);
            $descRaw = $course['description'] ?? '';
            $short = mb_substr($descRaw, 0, 140);
            $short = htmlspecialchars($short);
            $hasMore = mb_strlen($descRaw) > 140;
          ?>

          <div class="bg-white border border-slate-200 rounded-2xl shadow-sm hover:shadow-md transition overflow-hidden">
            <div class="p-6">
              <div class="flex items-start justify-between gap-3">
                <h2 class="text-lg font-semibold text-slate-800">
                  <?= $title; ?>
                </h2>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700">
                  Enrolled
                </span>
              </div>

              <p class="text-sm text-slate-500 mt-2">
                <?= $short; ?><?= $hasMore ? '...' : ''; ?>
              </p>

              <div class="mt-5 flex items-center justify-between">
                <p class="text-xs text-slate-400">
                  Course ID: <span class="font-semibold text-slate-600"><?= $course['course_id']; ?></span>
                </p>

                <a href="view_courses.php?id=<?= $course['course_id'] ?>"
                   class="inline-flex items-center text-blue-700 font-semibold hover:text-blue-900 hover:underline transition">
                  View Course <span class="ml-1">→</span>
                </a>
              </div>
            </div>
          </div>

        <?php endwhile; ?>
      </div>

    <?php endif; ?>

  </main>

</body>
</html>
