<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user'])) {
  header("Location: ../index.html?error=akses_ditolak");
  exit;
}

// Ensure the user is a superadmin
if ($_SESSION['user']['role'] !== 'superadmin') {
  header("Location: ../index.html?error=akses_ditolak");
  exit;
}

include '../config/koneksi.php';

// Fetch all users
$query = "SELECT * FROM users ORDER BY created_at DESC";
$users = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>Dashboard Superadmin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="../../assets/css/ui-polish.css" />
  <script src="https://unpkg.com/feather-icons"></script>
</head>

<body class="ui-grid-bg min-h-screen p-4 sm:p-6">
  <div class="max-w-6xl mx-auto">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
      <h1 class="text-xl sm:text-2xl font-bold text-gray-800">Dashboard Superadmin</h1>
      <a href="../proses/logout.php" class="text-red-600 hover:underline flex items-center gap-1 text-sm sm:text-base">
        <i data-feather="log-out"></i><span>Logout</span>
      </a>
    </div>

    <!-- Users Table Section -->
    <div class="ui-panel ui-glow rounded-xl overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full text-sm text-left">
          <thead class="bg-slate-900 text-white">
            <tr>
              <th class="py-3 px-4 whitespace-nowrap">#</th>
              <th class="py-3 px-4 whitespace-nowrap">Nama Lengkap</th>
              <th class="py-3 px-4 whitespace-nowrap">Email</th>
              <th class="py-3 px-4 whitespace-nowrap">Tanggal Daftar</th>
              <th class="py-3 px-4 text-center whitespace-nowrap">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php $no = 1; while ($row = mysqli_fetch_assoc($users)) : ?>
            <tr class="border-b hover:bg-gray-50">
              <td class="py-2 px-4"><?= $no++; ?></td>
              <td class="py-2 px-4"><?= htmlspecialchars($row['name']); ?></td>
              <td class="py-2 px-4"><?= htmlspecialchars($row['email']); ?></td>
              <td class="py-2 px-4"><?= date("d M Y", strtotime($row['created_at'])); ?></td>
              <td class="py-2 px-4 text-center">
                <!-- Delete User -->
                <a href="../proses/hapus_user.php?id=<?= $row['user_id']; ?>"
                  onclick="return confirm('Yakin ingin menghapus user ini?')" class="text-red-500 hover:underline">
                  <i data-feather="trash-2"></i>
                </a>
              </td>
            </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <footer>
    <div class="text-center text-sm text-gray-500 mt-6">
      &copy; <?= date("Y"); ?> Dashboard Superadmin. All rights reserved.
    </div>
  </footer>

  <!-- Feather Icons -->
  <script>
    feather.replace();
  </script>
</body>

</html>
