`<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'student') {
    header("Location: ../login.php?error=access_denied");
    exit;
}

include '../../../../includes/config/koneksi.php';
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$query = "SELECT * FROM courses";
$result = mysqli_query($conn, $query);

if (!$result) {
    echo "<p>Error executing query: " . mysqli_error($conn) . "</p>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Enroll Courses</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-br from-slate-50 to-slate-100 min-h-screen">

  <!-- Header -->
  <header class="sticky top-0 z-40 bg-white/80 backdrop-blur border-b border-slate-200">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
      <div>
        <h1 class="text-xl sm:text-2xl font-bold text-slate-800">Available Courses</h1>
        <p class="text-sm text-slate-500">Browse courses and enroll to start learning</p>
      </div>

      <a href="../dashboard.php"
        class="px-4 py-2 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800 transition">
        ← Back to Dashboard
      </a>
    </div>
  </header>

  <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <?php if (mysqli_num_rows($result) === 0): ?>
      <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-10 text-center">
        <div class="mx-auto w-14 h-14 rounded-2xl bg-slate-100 flex items-center justify-center mb-4">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
        <p class="text-slate-800 font-semibold">No courses available</p>
        <p class="text-slate-500 text-sm mt-1">Please check back later.</p>
      </div>
    <?php else: ?>

      <!-- Courses Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php while ($course = mysqli_fetch_assoc($result)) : ?>
          <?php
            $title = htmlspecialchars($course['title']);
            $descRaw = $course['description'] ?? '';
            $desc = htmlspecialchars($descRaw);

            $short = mb_substr($descRaw, 0, 140);
            $short = htmlspecialchars($short);
            $hasMore = mb_strlen($descRaw) > 140;
          ?>

          <div class="bg-white border border-slate-200 rounded-2xl shadow-sm hover:shadow-md transition overflow-hidden">
            <div class="p-6">
              <div class="flex items-start justify-between gap-3">
                <h2 class="text-lg font-semibold text-slate-800">
                  <?= $title; ?>
                </h2>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700">
                  Course
                </span>
              </div>

              <p class="text-sm text-slate-500 mt-2">
                <?= $short; ?><?= $hasMore ? '...' : ''; ?>
              </p>

              <div class="mt-5 flex items-center justify-between">
                <p class="text-xs text-slate-400">
                  Course ID: <span class="font-semibold text-slate-600"><?= $course['course_id']; ?></span>
                </p>
              </div>
            </div>

            <div class="p-6 pt-0">
              <a href="../../../../includes/procces/enroll.php?course_id=<?= $course['course_id']; ?>"
                 class="block text-center px-4 py-3 rounded-xl bg-blue-600 text-white font-semibold hover:bg-blue-700 transition">
                Enroll Now
              </a>
            </div>
          </div>

        <?php endwhile; ?>
      </div>

    <?php endif; ?>

  </main>

</body>
</html>

<?php mysqli_close($conn); ?>
`