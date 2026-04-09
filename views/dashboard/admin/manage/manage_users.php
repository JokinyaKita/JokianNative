<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'superadmin') {
    header("Location: ../login.php?error=access_denied");
    exit;
}

include '../../../../includes/config/koneksi.php';

// Search logic
$search = isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '';
$query = "SELECT * FROM users";

if ($search !== '') {
    $query = "SELECT * FROM users WHERE name LIKE '%$search%' OR email LIKE '%$search%'";
}

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
  <title>Manage Users</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-br from-slate-50 to-slate-100 min-h-screen">
  <!-- Top Bar -->
  <header class="sticky top-0 z-40 bg-white/80 backdrop-blur border-b border-slate-200">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
      <div>
        <h1 class="text-xl sm:text-2xl font-bold text-slate-800">Manage Users</h1>
        <p class="text-sm text-slate-500">Search, review, and manage user accounts</p>
      </div>

      <div class="flex items-center gap-2">
        <a href="../reports.php"
          class="inline-flex items-center gap-2 bg-red-50 text-red-700 px-4 py-2 rounded-xl hover:bg-red-100 transition text-sm font-semibold">
          Reports
        </a>
        <a href="../dashboard.php"
          class="inline-flex items-center gap-2 bg-slate-900 text-white px-4 py-2 rounded-xl hover:bg-slate-800 transition text-sm font-semibold">
          ← Back to Dashboard
        </a>
      </div>
    </div>
  </header>

  <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white border border-slate-200 shadow-sm rounded-2xl overflow-hidden">
      <!-- Controls -->
      <div class="p-6 border-b border-slate-200">
        <form method="GET" class="flex flex-col sm:flex-row gap-3 sm:items-center">
          <div class="flex-1">
            <label class="block text-sm font-medium text-slate-600 mb-1">Search</label>
            <div class="relative">
              <input
                type="text"
                name="search"
                value="<?= $search; ?>"
                placeholder="Search by name or email..."
                class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none"
              />
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                <!-- simple inline icon -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1010.5 18.5a7.5 7.5 0 006.15-3.85z" />
                </svg>
              </div>
            </div>
          </div>

          <div class="flex gap-2">
            <button
              class="px-5 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition text-sm font-semibold">
              Search
            </button>

            <?php if ($search !== ''): ?>
              <a href="manage_users.php"
                class="px-5 py-3 bg-slate-100 text-slate-700 rounded-xl hover:bg-slate-200 transition text-sm font-semibold">
                Reset
              </a>
            <?php endif; ?>
          </div>
        </form>

        <div class="mt-4 flex items-center justify-between text-sm text-slate-500">
          <div>
            <?php if ($search !== ''): ?>
              Showing results for: <span class="font-semibold text-slate-700"><?= $search; ?></span>
            <?php else: ?>
              Showing all users
            <?php endif; ?>
          </div>
          <div class="hidden sm:block">
            Tip: use partial keywords (e.g., “john”, “@gmail”)
          </div>
        </div>
      </div>

      <!-- Table -->
      <div class="overflow-x-auto">
        <table class="min-w-full">
          <thead class="bg-slate-50 border-b border-slate-200">
            <tr class="text-left text-xs font-semibold uppercase tracking-wider text-slate-600">
              <th class="py-3 px-4">#</th>
              <th class="py-3 px-4">Name</th>
              <th class="py-3 px-4">Email</th>
              <th class="py-3 px-4">Role</th>
              <th class="py-3 px-4 text-right">Actions</th>
            </tr>
          </thead>

          <tbody class="divide-y divide-slate-200 text-sm text-slate-700">
            <?php
            $no = 1;
            while ($row = mysqli_fetch_assoc($result)):
              $role = strtolower($row['role']);
              $roleClass = "bg-slate-100 text-slate-700";
              if ($role === 'superadmin') $roleClass = "bg-red-50 text-red-700";
              if ($role === 'admin') $roleClass = "bg-orange-50 text-orange-700";
              if ($role === 'instructor') $roleClass = "bg-purple-50 text-purple-700";
              if ($role === 'student') $roleClass = "bg-blue-50 text-blue-700";
            ?>
              <tr class="hover:bg-slate-50 transition">
                <td class="py-4 px-4"><?= $no++; ?></td>
                <td class="py-4 px-4 font-semibold text-slate-800"><?= $row['name']; ?></td>
                <td class="py-4 px-4"><?= $row['email']; ?></td>
                <td class="py-4 px-4">
                  <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold <?= $roleClass; ?>">
                    <?= ucfirst($row['role']); ?>
                  </span>
                </td>
                <td class="py-4 px-4">
                  <div class="flex items-center justify-end gap-2">
                    <a href="edit_user.php?id=<?= $row['user_id']; ?>"
                      class="inline-flex items-center px-3 py-2 rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100 transition text-sm font-semibold">
                      Edit
                    </a>

                    <a href="delete_user.php?id=<?= $row['user_id']; ?>"
                      onclick="return confirm('Are you sure you want to delete this user?')"
                      class="inline-flex items-center px-3 py-2 rounded-lg bg-red-50 text-red-700 hover:bg-red-100 transition text-sm font-semibold">
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
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <p class="text-slate-700 font-semibold">No users found</p>
            <p class="text-slate-500 text-sm mt-1">Try a different keyword or reset the search.</p>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </main>
</body>
</html>

<?php mysqli_close($conn); ?>
