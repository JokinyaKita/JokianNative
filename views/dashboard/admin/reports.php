<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'superadmin') {
    header("Location: ../login.php?error=access_denied");
    exit;
}

include '../../../includes/config/koneksi.php';

function countRows($conn, $tableName)
{
    $res = mysqli_query($conn, "SELECT COUNT(*) AS total FROM $tableName");
    if (!$res) {
        return 0;
    }
    $row = mysqli_fetch_assoc($res);
    return (int)($row['total'] ?? 0);
}

$totalUsers = countRows($conn, 'users');
$totalCourses = countRows($conn, 'courses');
$totalQuizzes = countRows($conn, 'quizzes');
$totalEnrollments = countRows($conn, 'enrollments');

$roleStatsQuery = mysqli_query($conn, "SELECT role, COUNT(*) AS total FROM users GROUP BY role ORDER BY total DESC");
$latestUsersQuery = mysqli_query($conn, "SELECT name, email, role, created_at FROM users ORDER BY created_at DESC LIMIT 8");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Superadmin Reports</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="../../../assets/css/ui-polish.css" />
</head>
<body class="ui-grid-bg min-h-screen">
  <header class="sticky top-0 z-40 bg-white/80 backdrop-blur border-b border-slate-200">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
      <div>
        <h1 class="text-xl sm:text-2xl font-bold text-slate-800">Reports</h1>
        <p class="text-sm text-slate-500">Ringkasan data platform untuk superadmin</p>
      </div>
      <a href="dashboard.php" class="inline-flex items-center px-4 py-2 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800 transition">
        Kembali ke Dashboard
      </a>
    </div>
  </header>

  <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="ui-panel rounded-2xl p-5 ui-glow">
        <p class="text-xs uppercase tracking-wider text-slate-500 font-semibold">Total Users</p>
        <p class="mt-2 text-3xl font-bold text-slate-800"><?= $totalUsers; ?></p>
      </div>
      <div class="ui-panel rounded-2xl p-5 ui-glow">
        <p class="text-xs uppercase tracking-wider text-slate-500 font-semibold">Total Courses</p>
        <p class="mt-2 text-3xl font-bold text-slate-800"><?= $totalCourses; ?></p>
      </div>
      <div class="ui-panel rounded-2xl p-5 ui-glow">
        <p class="text-xs uppercase tracking-wider text-slate-500 font-semibold">Total Quizzes</p>
        <p class="mt-2 text-3xl font-bold text-slate-800"><?= $totalQuizzes; ?></p>
      </div>
      <div class="ui-panel rounded-2xl p-5 ui-glow">
        <p class="text-xs uppercase tracking-wider text-slate-500 font-semibold">Total Enrollments</p>
        <p class="mt-2 text-3xl font-bold text-slate-800"><?= $totalEnrollments; ?></p>
      </div>
    </section>

    <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <div class="ui-panel rounded-2xl p-6 lg:col-span-1">
        <h2 class="text-lg font-semibold text-slate-800 mb-4">User Role Distribution</h2>
        <div class="space-y-3">
          <?php if ($roleStatsQuery && mysqli_num_rows($roleStatsQuery) > 0): ?>
            <?php while ($item = mysqli_fetch_assoc($roleStatsQuery)): ?>
              <div class="flex items-center justify-between rounded-xl bg-white/70 border border-slate-200 px-4 py-3">
                <span class="text-sm font-semibold text-slate-700"><?= ucfirst($item['role']); ?></span>
                <span class="text-sm font-bold text-slate-900"><?= (int)$item['total']; ?></span>
              </div>
            <?php endwhile; ?>
          <?php else: ?>
            <p class="text-sm text-slate-500">Belum ada data role.</p>
          <?php endif; ?>
        </div>
      </div>

      <div class="ui-panel rounded-2xl p-6 lg:col-span-2">
        <h2 class="text-lg font-semibold text-slate-800 mb-4">Latest Users</h2>
        <div class="overflow-x-auto">
          <table class="min-w-full text-sm">
            <thead>
              <tr class="border-b border-slate-200 text-slate-500 uppercase text-xs tracking-wider">
                <th class="py-2 text-left">Name</th>
                <th class="py-2 text-left">Email</th>
                <th class="py-2 text-left">Role</th>
                <th class="py-2 text-left">Created</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($latestUsersQuery && mysqli_num_rows($latestUsersQuery) > 0): ?>
                <?php while ($u = mysqli_fetch_assoc($latestUsersQuery)): ?>
                  <tr class="border-b border-slate-100">
                    <td class="py-3 font-semibold text-slate-800"><?= htmlspecialchars($u['name']); ?></td>
                    <td class="py-3 text-slate-600"><?= htmlspecialchars($u['email']); ?></td>
                    <td class="py-3 text-slate-700"><?= ucfirst($u['role']); ?></td>
                    <td class="py-3 text-slate-600"><?= htmlspecialchars($u['created_at']); ?></td>
                  </tr>
                <?php endwhile; ?>
              <?php else: ?>
                <tr>
                  <td colspan="4" class="py-4 text-center text-slate-500">Belum ada data user.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </section>
  </main>
</body>
</html>
