<?php
session_start();

// Check if the user is logged in and is a student
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'student') {
    header("Location: ../login.php?error=access_denied");
    exit;
}

$user = $_SESSION['user'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="../../../assets/css/ui-polish.css" />
  <script src="https://unpkg.com/feather-icons"></script>
</head>

<body class="ui-grid-bg min-h-screen">

  <!-- Top Bar -->
  <header class="sticky top-0 z-40 bg-white/80 backdrop-blur border-b border-slate-200">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
      <div>
        <h1 class="text-xl sm:text-2xl font-bold text-slate-800">Student Dashboard</h1>
        <p class="text-sm text-slate-500">Welcome back, keep learning!</p>
      </div>

      <a href="../../../includes/procces/logout.php"
         class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800 transition">
        <i data-feather="log-out" class="w-4 h-4"></i>
        Logout
      </a>
    </div>
  </header>

  <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <!-- Welcome Card -->
    <section class="mb-6">
      <div class="ui-panel rounded-2xl shadow-sm p-6 ui-glow">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
          <div>
            <h2 class="text-2xl sm:text-3xl font-bold text-slate-800">
              Hi, <?= htmlspecialchars($user['name']); ?> 👋
            </h2>
            <p class="text-slate-500 mt-1">
              Welcome to your <span class="font-semibold text-slate-700">Student Dashboard</span>
            </p>

            <div class="mt-3 text-sm text-slate-600">
              <p><span class="font-semibold text-slate-700">Email:</span> <?= htmlspecialchars($user['email']); ?></p>
            </div>
          </div>

          <div class="w-full sm:w-auto">
            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4">
              <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Account</p>
              <p class="mt-1 inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700">
                Student
              </p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Menu -->
    <section>
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-slate-800">Dashboard Menu</h3>
        <p class="text-sm text-slate-500">Choose an action below</p>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

        <!-- Enroll Courses -->
        <a href="enroll/enroll_courses.php"
           class="group ui-panel rounded-2xl p-6 shadow-sm hover:shadow-md transition hover-lift">
          <div class="flex items-start justify-between">
            <div class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center">
              <i data-feather="book" class="w-6 h-6 text-blue-600"></i>
            </div>
            <i data-feather="arrow-right" class="w-5 h-5 text-slate-400 group-hover:text-slate-700 transition"></i>
          </div>
          <h4 class="mt-4 text-lg font-semibold text-slate-800">Enroll Courses</h4>
          <p class="mt-1 text-sm text-slate-500">Join a new class and start learning.</p>
          <span class="mt-4 inline-flex text-sm font-semibold text-blue-700">Open</span>
        </a>

        <!-- My Courses -->
        <a href="courses/my_courses.php"
           class="group ui-panel rounded-2xl p-6 shadow-sm hover:shadow-md transition hover-lift">
          <div class="flex items-start justify-between">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 flex items-center justify-center">
              <i data-feather="layers" class="w-6 h-6 text-emerald-600"></i>
            </div>
            <i data-feather="arrow-right" class="w-5 h-5 text-slate-400 group-hover:text-slate-700 transition"></i>
          </div>
          <h4 class="mt-4 text-lg font-semibold text-slate-800">My Courses</h4>
          <p class="mt-1 text-sm text-slate-500">View the courses you’re enrolled in.</p>
          <span class="mt-4 inline-flex text-sm font-semibold text-emerald-700">Open</span>
        </a>

        <!-- Progress -->
        <a href="proggres/my_progress.php"
           class="group ui-panel rounded-2xl p-6 shadow-sm hover:shadow-md transition hover-lift">
          <div class="flex items-start justify-between">
            <div class="w-12 h-12 rounded-2xl bg-amber-50 flex items-center justify-center">
              <i data-feather="bar-chart-2" class="w-6 h-6 text-amber-600"></i>
            </div>
            <i data-feather="arrow-right" class="w-5 h-5 text-slate-400 group-hover:text-slate-700 transition"></i>
          </div>
          <h4 class="mt-4 text-lg font-semibold text-slate-800">My Progress</h4>
          <p class="mt-1 text-sm text-slate-500">Track your learning progress.</p>
          <span class="mt-4 inline-flex text-sm font-semibold text-amber-700">Open</span>
        </a>

      </div>
    </section>

  </main>

  <script>
    feather.replace();
  </script>

</body>
</html>
