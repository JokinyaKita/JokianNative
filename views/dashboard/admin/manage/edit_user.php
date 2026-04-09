<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'superadmin') {
    header("Location: ../dashboard.php?error=access_denied");
    exit;
}

include '../../../../includes/config/koneksi.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header("Location: manage_users.php?error=invalid_user");
    exit;
}

$userQuery = mysqli_query($conn, "SELECT user_id, name, email, role FROM users WHERE user_id = $id LIMIT 1");
if (!$userQuery || mysqli_num_rows($userQuery) === 0) {
    header("Location: manage_users.php?error=user_not_found");
    exit;
}

$user = mysqli_fetch_assoc($userQuery);
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($conn, trim($_POST['name'] ?? ''));
    $email = mysqli_real_escape_string($conn, trim($_POST['email'] ?? ''));
    $role = $_POST['role'] ?? 'student';
    $newPassword = $_POST['new_password'] ?? '';

    $allowedRoles = ['student', 'instructor', 'admin', 'superadmin'];
    if (!in_array($role, $allowedRoles, true)) {
        $role = 'student';
    }

    if ($name === '' || $email === '') {
        $error = 'Name dan email wajib diisi.';
    } else {
        $checkEmail = mysqli_query($conn, "SELECT user_id FROM users WHERE email = '$email' AND user_id != $id LIMIT 1");
        if ($checkEmail && mysqli_num_rows($checkEmail) > 0) {
            $error = 'Email sudah dipakai akun lain.';
        } else {
            if ($newPassword !== '') {
                $passwordHash = mysqli_real_escape_string($conn, password_hash($newPassword, PASSWORD_DEFAULT));
                $updateQuery = "UPDATE users SET name='$name', email='$email', role='$role', password_hash='$passwordHash' WHERE user_id=$id";
            } else {
                $updateQuery = "UPDATE users SET name='$name', email='$email', role='$role' WHERE user_id=$id";
            }

            if (mysqli_query($conn, $updateQuery)) {
                header("Location: manage_users.php?sukses=user_updated");
                exit;
            }

            $error = 'Gagal memperbarui user.';
        }
    }

    // Refresh displayed values when validation fails.
    $user['name'] = htmlspecialchars($_POST['name'] ?? '', ENT_QUOTES, 'UTF-8');
    $user['email'] = htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8');
    $user['role'] = $role;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Edit User</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="../../../../assets/css/ui-polish.css" />
</head>
<body class="ui-grid-bg min-h-screen">
  <header class="sticky top-0 z-40 bg-white/80 backdrop-blur border-b border-slate-200">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
      <div>
        <h1 class="text-xl sm:text-2xl font-bold text-slate-800">Edit User</h1>
        <p class="text-sm text-slate-500">Perbarui data akun pengguna</p>
      </div>
      <a href="manage_users.php" class="inline-flex items-center px-4 py-2 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800 transition">
        Kembali
      </a>
    </div>
  </header>

  <main class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="ui-panel ui-glow rounded-2xl p-6 sm:p-8">
      <?php if ($error !== ''): ?>
        <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
          <?= htmlspecialchars($error); ?>
        </div>
      <?php endif; ?>

      <form method="POST" class="space-y-5">
        <div>
          <label for="name" class="block text-sm font-semibold text-slate-700 mb-1">Name</label>
          <input type="text" id="name" name="name" required
            value="<?= htmlspecialchars($user['name']); ?>"
            class="w-full rounded-xl border border-slate-300 px-4 py-2.5 bg-white" />
        </div>

        <div>
          <label for="email" class="block text-sm font-semibold text-slate-700 mb-1">Email</label>
          <input type="email" id="email" name="email" required
            value="<?= htmlspecialchars($user['email']); ?>"
            class="w-full rounded-xl border border-slate-300 px-4 py-2.5 bg-white" />
        </div>

        <div>
          <label for="role" class="block text-sm font-semibold text-slate-700 mb-1">Role</label>
          <select id="role" name="role" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 bg-white">
            <?php
              $roles = ['student', 'instructor', 'admin', 'superadmin'];
              foreach ($roles as $itemRole):
                $selected = ($user['role'] === $itemRole) ? 'selected' : '';
            ?>
              <option value="<?= $itemRole; ?>" <?= $selected; ?>><?= ucfirst($itemRole); ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div>
          <label for="new_password" class="block text-sm font-semibold text-slate-700 mb-1">Password Baru (Opsional)</label>
          <input type="password" id="new_password" name="new_password"
            placeholder="Biarkan kosong jika tidak ingin ganti password"
            class="w-full rounded-xl border border-slate-300 px-4 py-2.5 bg-white" />
        </div>

        <button type="submit" class="w-full rounded-xl bg-blue-600 px-4 py-2.5 text-white font-semibold hover:bg-blue-700 transition">
          Simpan Perubahan
        </button>
      </form>
    </div>
  </main>
</body>
</html>
