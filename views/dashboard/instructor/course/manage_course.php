<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'instructor') {
  header("Location: ../login.php?error=access_denied");
  exit;
}

include '../../../../includes/config/koneksi.php';

$instructor_id = $_SESSION['user']['user_id'];

// Fetch instructor courses
$query = "SELECT * FROM courses WHERE instructor_id = '$instructor_id' ORDER BY course_id DESC";
$result = mysqli_query($conn, $query);
if (!$result) {
  die("Query failed: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Instructor - Manage Courses</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-br from-indigo-50 to-slate-100 min-h-screen">

  <!-- Top Bar -->
  <header class="sticky top-0 z-40 bg-white/80 backdrop-blur border-b border-slate-200">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
      <div>
        <h1 class="text-xl sm:text-2xl font-bold text-slate-800">Manage Courses</h1>
        <p class="text-sm text-slate-500">View and manage your courses</p>
      </div>

      <div class="flex items-center gap-2">
        <a href="../dashboard.php"
           class="px-4 py-2 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800 transition">
          ← Dashboard
        </a>

        <a href="create_course.php"
           class="px-4 py-2 rounded-xl bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700 transition">
          + Create Course
        </a>
      </div>
    </div>
  </header>

  <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <!-- Alerts -->
    <?php if (isset($_GET['sukses'])): ?>
      <div class="mb-6 p-4 rounded-xl bg-emerald-50 text-emerald-700 font-semibold">
        Success: <?= htmlspecialchars($_GET['sukses']); ?>
      </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
      <div class="mb-6 p-4 rounded-xl bg-red-50 text-red-700 font-semibold">
        Error: <?= htmlspecialchars($_GET['error']); ?>
      </div>
    <?php endif; ?>

    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
      <div class="p-6 border-b border-slate-200 flex items-center justify-between">
        <div>
          <h2 class="text-lg font-semibold text-slate-800">Your Courses</h2>
          <p class="text-sm text-slate-500">Open a course to manage its content.</p>
        </div>
        <div class="text-sm text-slate-500">
          Total: <span class="font-semibold text-slate-700"><?= mysqli_num_rows($result); ?></span>
        </div>
      </div>

      <div class="p-6">
        <?php if (mysqli_num_rows($result) === 0): ?>
          <div class="text-center py-10">
            <div class="mx-auto w-14 h-14 rounded-2xl bg-slate-100 flex items-center justify-center mb-4">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <p class="text-slate-800 font-semibold">No courses yet</p>
            <p class="text-slate-500 text-sm mt-1">Click “Create Course” to publish your first course.</p>
          </div>
        <?php else: ?>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <?php while ($course = mysqli_fetch_assoc($result)) : ?>
              <div class="bg-white border border-slate-200 rounded-2xl p-6 hover:shadow-md transition">
                <div class="flex items-start justify-between gap-4">
                  <div>
                    <h3 class="text-lg font-semibold text-slate-800">
                      <?= htmlspecialchars($course['title']); ?>
                    </h3>
                    <p class="text-sm text-slate-500 mt-1 line-clamp-2">
                      <?= htmlspecialchars($course['description']); ?>
                    </p>
                    <p class="text-xs text-slate-400 mt-2">
                      Course ID: <span class="font-semibold text-slate-600"><?= $course['course_id']; ?></span>
                    </p>
                  </div>

                  <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700">
                    Instructor
                  </span>
                </div>

                <!-- Manage shortcuts -->
                <div class="mt-5 flex flex-wrap gap-2">
                  <a href="quiz/manage_quiz.php?course_id=<?= $course['course_id']; ?>"
                     class="px-3 py-2 rounded-lg bg-purple-50 text-purple-700 hover:bg-purple-100 transition text-xs font-semibold">
                    Quiz
                  </a>
                </div>

                <!-- Actions -->
                <div class="mt-6 flex items-center justify-between">
                  <a href="edit_course.php?course_id=<?= $course['course_id']; ?>"
                     class="px-4 py-2 rounded-xl bg-slate-900 text-white hover:bg-slate-800 transition text-sm font-semibold">
                    Edit
                  </a>

                  <a href="delete_course.php?course_id=<?= $course['course_id']; ?>"
                     onclick="return confirm('Delete this course?')"
                     class="px-4 py-2 rounded-xl bg-red-50 text-red-700 hover:bg-red-100 transition text-sm font-semibold">
                    Delete
                  </a>
                </div>
              </div>
            <?php endwhile; ?>
          </div>

        <?php endif; ?>
      </div>
    </div>

  </main>
</body>
</html>

<?php mysqli_close($conn); ?>
