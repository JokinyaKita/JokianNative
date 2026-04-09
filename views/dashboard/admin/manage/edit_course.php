<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'superadmin') {
    header("Location: ../login.php?error=access_denied");
    exit;
}

include '../../../../includes/config/koneksi.php';

// Validate course_id from GET
if (!isset($_GET['course_id']) || empty($_GET['course_id'])) {
    header("Location: manage_courses.php?error=invalid_course");
    exit;
}

$course_id = $_GET['course_id'];

// Fetch course data
$courseQuery = "SELECT * FROM courses WHERE course_id = '$course_id'";
$courseResult = mysqli_query($conn, $courseQuery);
if (!$courseResult) {
    die("Query failed: " . mysqli_error($conn));
}

if (mysqli_num_rows($courseResult) === 0) {
    header("Location: manage_courses.php?error=course_not_found");
    exit;
}

$course = mysqli_fetch_assoc($courseResult);

// Fetch instructors list (optional, for assigning instructor)
$instructorQuery = "SELECT user_id, name, email FROM users WHERE role = 'instructor' ORDER BY name ASC";
$instructorResult = mysqli_query($conn, $instructorQuery);
if (!$instructorResult) {
    die("Query failed: " . mysqli_error($conn));
}

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $instructor_id = $_POST['instructor_id']; // can be empty

    // If instructor_id empty -> set NULL
    if ($instructor_id === '') {
        $updateQuery = "UPDATE courses 
                        SET title = '$title', description = '$description', instructor_id = NULL
                        WHERE course_id = '$course_id'";
    } else {
        $updateQuery = "UPDATE courses 
                        SET title = '$title', description = '$description', instructor_id = '$instructor_id'
                        WHERE course_id = '$course_id'";
    }

    if (mysqli_query($conn, $updateQuery)) {
        header("Location: manage_courses.php?sukses=course_updated");
        exit;
    } else {
        header("Location: edit_course.php?course_id=$course_id&error=update_failed");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Edit Course</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-br from-slate-50 to-slate-100 min-h-screen">

  <!-- Header -->
  <header class="sticky top-0 z-40 bg-white/80 backdrop-blur border-b border-slate-200">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
      <div>
        <h1 class="text-xl sm:text-2xl font-bold text-slate-800">Edit Course</h1>
        <p class="text-sm text-slate-500">Update course information and assign an instructor</p>
      </div>
      <a href="manage_courses.php"
        class="px-4 py-2 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800 transition">
        ← Back
      </a>
    </div>
  </header>

  <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <?php if (isset($_GET['error']) && $_GET['error'] === 'update_failed'): ?>
      <div class="mb-6 p-4 rounded-xl bg-red-50 text-red-700 font-semibold">
        Failed to update course. Please try again.
      </div>
    <?php endif; ?>

    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-8">
      <form method="POST" class="space-y-6">

        <!-- Title -->
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Course Title</label>
          <input
            type="text"
            name="title"
            required
            value="<?= htmlspecialchars($course['title']); ?>"
            class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none"
            placeholder="Course title..."
          />
        </div>

        <!-- Description -->
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Course Description</label>
          <textarea
            name="description"
            rows="5"
            required
            class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none resize-none"
            placeholder="Course description..."
          ><?= htmlspecialchars($course['description']); ?></textarea>
        </div>

        <!-- Instructor -->
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Assign Instructor</label>
          <select
            name="instructor_id"
            class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none bg-white">
            <option value="">Not Assigned</option>
            <?php while ($ins = mysqli_fetch_assoc($instructorResult)) : ?>
              <option value="<?= $ins['user_id']; ?>"
                <?= ($course['instructor_id'] == $ins['user_id']) ? 'selected' : ''; ?>>
                <?= htmlspecialchars($ins['name']); ?> (<?= htmlspecialchars($ins['email']); ?>)
              </option>
            <?php endwhile; ?>
          </select>
          <p class="mt-2 text-xs text-slate-500">
            Choose an instructor to own/manage this course.
          </p>
        </div>

        <!-- Actions -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pt-2">
          <p class="text-sm text-slate-500">
            Course ID: <span class="font-semibold text-slate-700"><?= htmlspecialchars($course_id); ?></span>
          </p>

          <div class="flex gap-2">
            <a href="manage_courses.php"
              class="px-5 py-3 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200 transition text-sm font-semibold">
              Cancel
            </a>
            <button
              type="submit"
              class="px-5 py-3 rounded-xl bg-blue-600 text-white hover:bg-blue-700 transition text-sm font-semibold">
              Save Changes
            </button>
          </div>
        </div>

      </form>
    </div>
  </main>

</body>
</html>

<?php mysqli_close($conn); ?>
