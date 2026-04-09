<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'superadmin') {
    header("Location: ../login.php?error=access_denied");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Superadmin Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="../../../assets/css/ui-polish.css" />
  <script src="https://unpkg.com/feather-icons"></script>
</head>

<body class="ui-grid-bg min-h-screen">
  <!-- Top Header (No Sidebar) -->
  <header class="sticky top-0 z-40 bg-white/80 backdrop-blur border-b border-slate-200">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
      <div>
        <h1 class="text-xl sm:text-2xl font-bold text-slate-800">Superadmin Dashboard</h1>
        <p class="text-sm text-slate-500">Manage your platform settings and content</p>
      </div>

      <div class="flex items-center gap-3">
        <span class="hidden sm:inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-100 text-slate-600 text-sm">
          <i data-feather="shield" class="w-4 h-4"></i>
          Superadmin Access
        </span>
        <a href="../../../includes/procces/logout.php"
          class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800 transition">
          <i data-feather="log-out" class="w-4 h-4"></i>
          Logout
        </a>
      </div>
    </div>
  </header>

  <!-- Main Content -->
  <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Quick Stats / Info -->
    <section class="mb-6">
      <div class="ui-panel rounded-2xl p-5 ui-glow">
        <div class="flex items-start sm:items-center justify-between gap-4 flex-col sm:flex-row">
          <div>
            <h2 class="text-lg font-semibold text-slate-800">Control Center</h2>
            <p class="text-sm text-slate-500">
              Choose an action below to manage users, courses, instructors, settings, and reports.
            </p>
          </div>
          <div class="w-full sm:w-auto">
            <div class="relative">
              <input type="text" placeholder="Search menu..."
                class="w-full sm:w-72 pl-10 pr-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none bg-white" />
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                <i data-feather="search" class="w-4 h-4"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Cards -->
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <!-- Manage Users -->
      <a href="manage/manage_users.php"
        class="group ui-panel rounded-2xl p-6 shadow-sm hover:shadow-md transition hover-lift">
        <div class="flex items-start justify-between">
          <div class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center">
            <i data-feather="users" class="w-6 h-6 text-blue-600"></i>
          </div>
          <i data-feather="arrow-right" class="w-5 h-5 text-slate-400 group-hover:text-slate-700 transition"></i>
        </div>
        <h3 class="mt-4 text-lg font-semibold text-slate-800">Manage Users</h3>
        <p class="mt-1 text-sm text-slate-500">Manage student & instructor accounts</p>
        <span class="mt-4 inline-flex text-sm font-semibold text-blue-700">Open</span>
      </a>

      <!-- Manage Courses -->
      <a href="manage/manage_courses.php"
        class="group ui-panel rounded-2xl p-6 shadow-sm hover:shadow-md transition hover-lift">
        <div class="flex items-start justify-between">
          <div class="w-12 h-12 rounded-2xl bg-green-50 flex items-center justify-center">
            <i data-feather="book-open" class="w-6 h-6 text-green-600"></i>
          </div>
          <i data-feather="arrow-right" class="w-5 h-5 text-slate-400 group-hover:text-slate-700 transition"></i>
        </div>
        <h3 class="mt-4 text-lg font-semibold text-slate-800">Manage Courses</h3>
        <p class="mt-1 text-sm text-slate-500">Manage courses and learning materials</p>
        <span class="mt-4 inline-flex text-sm font-semibold text-green-700">Open</span>
      </a>

      <!-- Manage Instructors -->
      <a href="manage/manage_instructors.php"
        class="group ui-panel rounded-2xl p-6 shadow-sm hover:shadow-md transition hover-lift">
        <div class="flex items-start justify-between">
          <div class="w-12 h-12 rounded-2xl bg-purple-50 flex items-center justify-center">
            <i data-feather="briefcase" class="w-6 h-6 text-purple-600"></i>
          </div>
          <i data-feather="arrow-right" class="w-5 h-5 text-slate-400 group-hover:text-slate-700 transition"></i>
        </div>
        <h3 class="mt-4 text-lg font-semibold text-slate-800">Manage Instructors</h3>
        <p class="mt-1 text-sm text-slate-500">Manage instructor information</p>
        <span class="mt-4 inline-flex text-sm font-semibold text-purple-700">Open</span>
      </a>

      <!-- Reports -->
      <a href="reports.php"
        class="group ui-panel rounded-2xl p-6 shadow-sm hover:shadow-md transition hover-lift">
        <div class="flex items-start justify-between">
          <div class="w-12 h-12 rounded-2xl bg-red-50 flex items-center justify-center">
            <i data-feather="bar-chart-2" class="w-6 h-6 text-red-600"></i>
          </div>
          <i data-feather="arrow-right" class="w-5 h-5 text-slate-400 group-hover:text-slate-700 transition"></i>
        </div>
        <h3 class="mt-4 text-lg font-semibold text-slate-800">Reports</h3>
        <p class="mt-1 text-sm text-slate-500">Data and activity reports</p>
        <span class="mt-4 inline-flex text-sm font-semibold text-red-700">Open</span>
      </a>

    </section>
  </main>

  <script>
    feather.replace();
  </script>
</body>
</html>
