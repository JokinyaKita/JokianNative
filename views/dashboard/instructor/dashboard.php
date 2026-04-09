<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'instructor') {
  header("Location: ../login.php?error=access_denied");
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Instructor Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="../../../assets/css/ui-polish.css" />
</head>

<body class="ui-grid-bg min-h-screen">

  <!-- Header -->
  <header class="sticky top-0 z-40 bg-white/80 backdrop-blur border-b border-slate-200">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
      <div>
        <h1 class="text-xl sm:text-2xl font-bold text-slate-800">Instructor Dashboard</h1>
        <p class="text-sm text-slate-500">Manage your courses and learning content</p>
      </div>

      <!-- Optional: you can change this link if you have logout -->
      <a href="../../../includes/procces/logout.php"
        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800 transition">
        Logout
      </a>
    </div>
  </header>

  <!-- Main -->
  <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <!-- Welcome Card -->
    <section class="mb-6">
      <div class="ui-panel rounded-2xl p-6 ui-glow">
        <h2 class="text-lg font-semibold text-slate-800">Quick Actions</h2>
        <p class="text-sm text-slate-500 mt-1">
          Choose an option below to create a new course or manage your existing courses.
        </p>
      </div>
    </section>

    <!-- Action Cards -->
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      
      <!-- Create Course -->
      <a href="../instructor/course/create_course.php"
        class="group ui-panel rounded-2xl p-6 shadow-sm hover:shadow-md transition hover-lift">
        <div class="flex items-start justify-between">
          <div class="w-12 h-12 rounded-2xl bg-indigo-50 flex items-center justify-center">
            <!-- icon -->
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
          </div>
          <span class="text-slate-400 group-hover:text-slate-700 transition">→</span>
        </div>
        <h3 class="mt-4 text-lg font-semibold text-slate-800">Create Course</h3>
        <p class="mt-1 text-sm text-slate-500">Start a new course and upload materials.</p>
        <span class="mt-4 inline-flex text-sm font-semibold text-indigo-700">Open</span>
      </a>

      <!-- Manage Courses -->
      <a href="../instructor/course/manage_course.php"
        class="group ui-panel rounded-2xl p-6 shadow-sm hover:shadow-md transition hover-lift">
        <div class="flex items-start justify-between">
          <div class="w-12 h-12 rounded-2xl bg-emerald-50 flex items-center justify-center">
            <!-- icon -->
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01" />
            </svg>
          </div>
          <span class="text-slate-400 group-hover:text-slate-700 transition">→</span>
        </div>
        <h3 class="mt-4 text-lg font-semibold text-slate-800">Manage Courses</h3>
        <p class="mt-1 text-sm text-slate-500">Edit, publish, and organize your courses.</p>
        <span class="mt-4 inline-flex text-sm font-semibold text-emerald-700">Open</span>
      </a>

      <!-- Placeholder / Other Actions -->
      <div class="ui-panel border border-dashed border-slate-300 rounded-2xl p-6 shadow-sm">
        <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center">
          <!-- icon -->
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 20a8 8 0 100-16 8 8 0 000 16z" />
          </svg>
        </div>
        <h3 class="mt-4 text-lg font-semibold text-slate-800">Other Actions</h3>
        <p class="mt-1 text-sm text-slate-500">
          Add more instructor features here (assignments, quizzes, students, etc.).
        </p>
        <span class="mt-4 inline-flex text-sm font-semibold text-slate-500">Coming soon</span>
      </div>

    </section>
  </main>

</body>
</html>
