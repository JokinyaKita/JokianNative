<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'instructor') {
  header("Location: ../login.php?error=access_denied");
  exit;
}

include '../../../../includes/config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $title = $_POST['title'];
  $description = $_POST['description'];
  $instructor_id = $_SESSION['user']['user_id'];

  $query = "INSERT INTO courses (title, description, instructor_id)
            VALUES ('$title', '$description', '$instructor_id')";

  if (mysqli_query($conn, $query)) {
    header("Location: ../course/manage_course.php?sukses=course_created");
  } else {
    header("Location: ../course/create_course.php?error=course_creation_failed");
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Create Course</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-br from-indigo-50 to-slate-100 min-h-screen">

  <!-- Header -->
  <header class="bg-white/80 backdrop-blur border-b border-slate-200">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
      <div>
        <h1 class="text-xl sm:text-2xl font-bold text-slate-800">Create New Course</h1>
        <p class="text-sm text-slate-500">Add a new course and start sharing knowledge</p>
      </div>
      <a href="manage_courses.php"
        class="inline-flex items-center px-4 py-2 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800 transition">
        ← Back
      </a>
    </div>
  </header>

  <!-- Main Content -->
  <main class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <!-- Alert Messages -->
    <?php if (isset($_GET['error']) && $_GET['error'] === 'course_creation_failed'): ?>
      <div class="mb-6 rounded-xl bg-red-50 border border-red-200 p-4 text-red-700">
        Failed to create course. Please try again.
      </div>
    <?php endif; ?>

    <!-- Form Card -->
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-8">
      <form method="POST" class="space-y-6">

        <!-- Course Title -->
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">
            Course Title
          </label>
          <input
            type="text"
            name="title"
            required
            placeholder="e.g. Web Development Basics"
            class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:outline-none"
          />
        </div>

        <!-- Course Description -->
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">
            Course Description
          </label>
          <textarea
            name="description"
            rows="5"
            required
            placeholder="Describe what students will learn in this course..."
            class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:outline-none resize-none"
          ></textarea>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-end gap-3 pt-4">
          <a href="../dashboard.php"
            class="px-5 py-3 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200 transition font-semibold text-sm">
            Cancel
          </a>
          <button
            type="submit"
            class="px-6 py-3 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 transition font-semibold text-sm">
            Create Course
          </button>
        </div>

      </form>
    </div>
  </main>

</body>
</html>
