<!-- Sidebar and Dashboard Content -->
  <div class="flex min-h-screen">
    <!-- Sidebar -->
    <div class="w-64 bg-blue-600 text-white p-4">
      <h2 class="text-2xl font-bold mb-6">Admin Panel</h2>
      <nav>
        <ul>
          <li><a href="manage/manage_users.php" class="block py-2 px-4 rounded hover:bg-blue-700 transition">Manage Users</a></li>
          <li><a href="manage/manage_courses.php" class="block py-2 px-4 rounded hover:bg-blue-700 transition">Manage Courses</a></li>
          <li><a href="settings/settings.php" class="block py-2 px-4 rounded hover:bg-blue-700 transition">Settings</a></li>
          <li><a href="reports/reports.php" class="block py-2 px-4 rounded hover:bg-blue-700 transition">Reports</a></li>
        <div class="mt-8 text-center">
          <a href="../../../../includes/procces/logout.php" class="text-red-600 hover:underline flex items-center gap-1 text-sm sm:text-base">
            <i data-feather="log-out" class="inline-block mr-2"></i> Logout
          </a>
        </div>
        </ul>
      </nav>
    </div>