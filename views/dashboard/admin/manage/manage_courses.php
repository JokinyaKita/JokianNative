<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'superadmin') {
    header("Location: ../login.php?error=access_denied");
    exit;
}

include '../../../../includes/config/koneksi.php';

$query = "SELECT c.*, u.name AS instructor_name 
          FROM courses c
          LEFT JOIN users u ON c.instructor_id = u.user_id";
$result = mysqli_query($conn, $query);

if (!$result) die("Query failed: " . mysqli_error($conn));
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Manage Courses</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-br from-slate-50 to-slate-100 min-h-screen">

  <!-- Top Bar -->
  <header class="sticky top-0 z-40 bg-white/80 backdrop-blur border-b border-slate-200">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
      <div>
        <h1 class="text-xl sm:text-2xl font-bold text-slate-800">Manage Courses</h1>
        <p class="text-sm text-slate-500">Manage courses, content, quizzes, and assignments</p>
      </div>

      <div class="flex items-center gap-2">
        <a href="../dashboard.php"
          class="px-4 py-2 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800 transition">
          ← Back
        </a>
        <a href="add_course.php"
          class="px-4 py-2 rounded-xl bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700 transition">
          + Add Course
        </a>
      </div>
    </div>
  </header>

  <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">

      <!-- Table Header -->
      <div class="p-6 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
          <h2 class="text-lg font-semibold text-slate-800">Courses List</h2>
          <p class="text-sm text-slate-500">Click content shortcuts to manage course materials faster</p>
        </div>

        <div class="text-sm text-slate-500">
          Total: <span class="font-semibold text-slate-700"><?= mysqli_num_rows($result); ?></span>
        </div>
      </div>

      <!-- Table -->
      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="bg-slate-50 border-b border-slate-200">
            <tr class="text-left text-xs font-semibold uppercase tracking-wider text-slate-600">
              <th class="py-3 px-4 w-14">#</th>
              <th class="py-3 px-4">Title</th>
              <th class="py-3 px-4">Instructor</th>
              <th class="py-3 px-4">Manage</th>
              <th class="py-3 px-4 text-right">Actions</th>
            </tr>
          </thead>

          <tbody class="divide-y divide-slate-200 text-slate-700">
            <?php 
            $no = 1;
            while ($course = mysqli_fetch_assoc($result)) : 
              $title = htmlspecialchars($course['title']);
              $instructor = $course['instructor_name'] ? htmlspecialchars($course['instructor_name']) : "Not Assigned";
              $courseId = $course['course_id'];
            ?>
              <tr class="hover:bg-slate-50 transition">
                <td class="py-4 px-4"><?= $no++; ?></td>

                <td class="py-4 px-4">
                  <div class="font-semibold text-slate-800"><?= $title; ?></div>
                  <div class="text-xs text-slate-500">Course ID: <?= $courseId; ?></div>
                </td>

                <td class="py-4 px-4">
                  <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                    <?= $course['instructor_name'] ? 'bg-blue-50 text-blue-700' : 'bg-slate-100 text-slate-600'; ?>">
                    <?= $instructor; ?>
                  </span>
                </td>

                <!-- Manage shortcuts -->
                <td class="py-4 px-4">
                  <div class="flex flex-wrap gap-2">
                    <a href="../quiz/manage_quiz.php?course_id=<?= $courseId; ?>"
                      class="inline-flex items-center px-3 py-1.5 rounded-lg bg-purple-50 text-purple-700 hover:bg-purple-100 transition text-xs font-semibold">
                      Quiz
                    </a>
                  </div>
                </td>

                <!-- Actions -->
                <td class="py-4 px-4">
                  <div class="flex items-center justify-end gap-2">
                    <a href="edit_course.php?course_id=<?= $courseId; ?>"
                      class="inline-flex items-center px-3 py-2 rounded-lg bg-slate-900 text-white hover:bg-slate-800 transition text-xs font-semibold">
                      Edit
                    </a>

                    <a href="delete_course.php?course_id=<?= $courseId; ?>"
                      onclick="return confirm('Are you sure you want to delete this course?')"
                      class="inline-flex items-center px-3 py-2 rounded-lg bg-red-50 text-red-700 hover:bg-red-100 transition text-xs font-semibold">
                      Delete
                    </a>
                  </div>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>

        <?php if (mysqli_num_rows($result) === 0): ?>
          <div class="p-10 text-center">
            <div class="mx-auto w-14 h-14 rounded-2xl bg-slate-100 flex items-center justify-center mb-4">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <p class="text-slate-800 font-semibold">No courses available</p>
            <p class="text-slate-500 text-sm mt-1">Click “Add Course” to create your first course.</p>
          </div>
        <?php endif; ?>

      </div>
    </div>
  </main>
</body>
</html>

<?php mysqli_close($conn); ?>
