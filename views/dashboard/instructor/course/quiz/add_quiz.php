<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'instructor') {
    header("Location: ../login.php?error=access_denied");
    exit;
}

include '../../../../../includes/config/koneksi.php';

$course_id = $_GET['course_id'] ?? null;
if (!$course_id) die("Course ID missing.");

$course_id = intval($course_id);
$instructor_id = $_SESSION['user']['user_id'];

// Validate course belongs to instructor
$course = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM courses WHERE course_id=$course_id AND instructor_id='$instructor_id'"));
if (!$course) {
    die("Access denied. This course is not assigned to you.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    $insert = "INSERT INTO quizzes (course_id, title, description)
               VALUES ($course_id, '$title', '$description')";

    if (mysqli_query($conn, $insert)) {
        header("Location: manage_quiz.php?course_id=$course_id&sukses=quiz_created");
        exit;
    } else {
        header("Location: add_quiz.php?course_id=$course_id&error=quiz_creation_failed");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Add Quiz</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-br from-indigo-50 to-slate-100 min-h-screen">

  <!-- Header -->
  <header class="sticky top-0 z-40 bg-white/80 backdrop-blur border-b border-slate-200">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
      <div>
        <h1 class="text-xl sm:text-2xl font-bold text-slate-800">Add Quiz</h1>
        <p class="text-sm text-slate-500">
          Course: <span class="font-semibold text-slate-700"><?= htmlspecialchars($course['title']); ?></span>
        </p>
      </div>

      <a href="manage_quiz.php?course_id=<?= $course_id ?>"
         class="px-4 py-2 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800 transition">
        ← Back
      </a>
    </div>
  </header>

  <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <?php if (isset($_GET['error']) && $_GET['error'] === 'quiz_creation_failed'): ?>
      <div class="mb-6 p-4 rounded-xl bg-red-50 text-red-700 font-semibold">
        Failed to create quiz. Please try again.
      </div>
    <?php endif; ?>

    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
      <div class="p-6 border-b border-slate-200">
        <h2 class="text-lg font-semibold text-slate-800">Quiz Details</h2>
        <p class="text-sm text-slate-500">Enter the quiz title and a short description.</p>
      </div>

      <div class="p-6">
        <form method="POST" class="space-y-6">

          <!-- Title -->
          <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">Title</label>
            <input
              type="text"
              name="title"
              required
              placeholder="e.g. Chapter 1 Quiz"
              class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none"
            />
          </div>

          <!-- Description -->
          <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">Description</label>
            <textarea
              name="description"
              rows="5"
              placeholder="Optional description..."
              class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none resize-none"
            ></textarea>
          </div>

          <!-- Actions -->
          <div class="pt-2 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <p class="text-sm text-slate-500">
              Course ID: <span class="font-semibold text-slate-700"><?= htmlspecialchars($course_id); ?></span>
            </p>

            <div class="flex gap-2">
              <a href="manage_quiz.php?course_id=<?= $course_id ?>"
                 class="px-5 py-3 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200 transition text-sm font-semibold">
                Cancel
              </a>

              <button
                class="px-5 py-3 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 transition text-sm font-semibold">
                Create Quiz
              </button>
            </div>
          </div>

        </form>
      </div>
    </div>
  </main>

</body>
</html>

<?php mysqli_close($conn); ?>
