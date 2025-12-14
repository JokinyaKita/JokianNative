<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'instructor') {
    header("Location: ../login.php?error=access_denied");
    exit;
}

include '../../../../../includes/config/koneksi.php';

$course_id = $_GET['course_id'] ?? null;
if (!$course_id) {
    die("Course ID missing.");
}

$course_id = intval($course_id);
$instructor_id = $_SESSION['user']['user_id'];

// Check course belongs to instructor
$course = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM courses WHERE course_id=$course_id AND instructor_id='$instructor_id'"));
if (!$course) {
    die("Access denied. This course is not assigned to you.");
}

// Fetch quizzes
$quizzes = mysqli_query($conn, "SELECT * FROM quizzes WHERE course_id=$course_id ORDER BY created_at DESC");
if (!$quizzes) {
    die("Query failed: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Manage Quiz</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-br from-indigo-50 to-slate-100 min-h-screen">

  <!-- Top Bar -->
  <header class="sticky top-0 z-40 bg-white/80 backdrop-blur border-b border-slate-200">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
      <div>
        <h1 class="text-xl sm:text-2xl font-bold text-slate-800">Manage Quizzes</h1>
        <p class="text-sm text-slate-500">
          Course: <span class="font-semibold text-slate-700"><?= htmlspecialchars($course['title']); ?></span>
        </p>
      </div>

      <div class="flex items-center gap-2">
        <a href="../manage_course.php"
           class="px-4 py-2 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800 transition">
          ← Back
        </a>

        <a href="add_quiz.php?course_id=<?= $course_id; ?>"
           class="px-4 py-2 rounded-xl bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700 transition">
          + Add Quiz
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
          <h2 class="text-lg font-semibold text-slate-800">Quiz List</h2>
          <p class="text-sm text-slate-500">Create quizzes and manage questions.</p>
        </div>
        <div class="text-sm text-slate-500">
          Total: <span class="font-semibold text-slate-700"><?= mysqli_num_rows($quizzes); ?></span>
        </div>
      </div>

      <div class="p-6">
        <?php if (mysqli_num_rows($quizzes) == 0): ?>
          <div class="text-center py-10">
            <div class="mx-auto w-14 h-14 rounded-2xl bg-slate-100 flex items-center justify-center mb-4">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <p class="text-slate-800 font-semibold">No quizzes yet</p>
            <p class="text-slate-500 text-sm mt-1">Click “Add Quiz” to create your first quiz.</p>
          </div>
        <?php else: ?>

          <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
              <thead class="bg-slate-50 border border-slate-200">
                <tr class="text-left text-xs font-semibold uppercase tracking-wider text-slate-600">
                  <th class="py-3 px-4">Title</th>
                  <th class="py-3 px-4">Created At</th>
                  <th class="py-3 px-4 text-right">Actions</th>
                </tr>
              </thead>

              <tbody class="divide-y divide-slate-200 text-slate-700">
                <?php while ($q = mysqli_fetch_assoc($quizzes)) : ?>
                  <tr class="hover:bg-slate-50 transition">
                    <td class="py-4 px-4">
                      <div class="font-semibold text-slate-800"><?= htmlspecialchars($q['title']); ?></div>
                      <?php if (!empty($q['description'])): ?>
                        <div class="text-xs text-slate-500 mt-1"><?= htmlspecialchars($q['description']); ?></div>
                      <?php endif; ?>
                      <div class="text-xs text-slate-400 mt-1">Quiz ID: <?= $q['quiz_id']; ?></div>
                    </td>

                    <td class="py-4 px-4">
                      <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">
                        <?= $q['created_at'] ?? '-'; ?>
                      </span>
                    </td>

                    <td class="py-4 px-4">
                      <div class="flex items-center justify-end gap-2 flex-wrap">
                        <a href="questions/manage_questions.php?quiz_id=<?= $q['quiz_id']; ?>"
                           class="inline-flex items-center px-3 py-2 rounded-lg bg-purple-50 text-purple-700 hover:bg-purple-100 transition text-xs font-semibold">
                          Questions
                        </a>

                        <a href="edit_quiz.php?quiz_id=<?= $q['quiz_id']; ?>"
                           class="inline-flex items-center px-3 py-2 rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100 transition text-xs font-semibold">
                          Edit
                        </a>

                        <a href="delete_quiz.php?quiz_id=<?= $q['quiz_id']; ?>"
                           onclick="return confirm('Delete this quiz?')"
                           class="inline-flex items-center px-3 py-2 rounded-lg bg-red-50 text-red-700 hover:bg-red-100 transition text-xs font-semibold">
                          Delete
                        </a>
                      </div>
                    </td>
                  </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>

        <?php endif; ?>
      </div>
    </div>

  </main>
</body>
</html>

<?php mysqli_close($conn); ?>
