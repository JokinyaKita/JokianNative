<?php
session_start();

// Ensure student
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'student') {
    header("Location: ../login.php?error=access_denied");
    exit;
}

include '../../../../../includes/config/koneksi.php';

// Validate incoming id
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid ID.");
}

$raw_id  = (int) $_GET['id']; // can be quiz_id OR course_id
$user_id = (int) $_SESSION['user']['user_id'];

/* =========================================
   1) Try treating id as quiz_id
========================================= */
$quiz = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT q.*, c.title AS course_title
    FROM quizzes q
    JOIN courses c ON q.course_id = c.course_id
    WHERE q.quiz_id = $raw_id
    LIMIT 1
"));

/* =========================================
   2) If not found, treat id as course_id
      and load latest quiz for that course
========================================= */
if (!$quiz) {
    $quiz = mysqli_fetch_assoc(mysqli_query($conn, "
        SELECT q.*, c.title AS course_title
        FROM quizzes q
        JOIN courses c ON q.course_id = c.course_id
        WHERE q.course_id = $raw_id
        ORDER BY q.created_at DESC, q.quiz_id DESC
        LIMIT 1
    "));
}

if (!$quiz) {
    die("Quiz not found.");
}

// Now we have the real quiz_id and course_id
$quiz_id   = (int) $quiz['quiz_id'];
$course_id = (int) $quiz['course_id'];

/* =========================================
   3) Enrollment check (must be enrolled)
========================================= */
$enroll = mysqli_query($conn, "
    SELECT 1 FROM enrollments
    WHERE user_id = $user_id AND course_id = $course_id
    LIMIT 1
");
if (!$enroll || mysqli_num_rows($enroll) === 0) {
    die("You are not enrolled in this course.");
}

/* =========================================
   4) Attempt check -> show UI (not die)
========================================= */
$check = mysqli_query($conn, "
    SELECT 1 FROM quiz_attempts
    WHERE user_id = $user_id AND quiz_id = $quiz_id
    LIMIT 1
");

if ($check && mysqli_num_rows($check) > 0) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
      <meta charset="UTF-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1.0" />
      <title>Quiz Already Attempted</title>
      <script src="https://cdn.tailwindcss.com"></script>
    </head>

    <body class="bg-gradient-to-br from-slate-50 to-slate-100 min-h-screen">

      <header class="sticky top-0 z-40 bg-white/80 backdrop-blur border-b border-slate-200">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
          <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-800">Quiz</h1>
            <p class="text-sm text-slate-500">Attempt status</p>
          </div>

          <a href="../start_course.php?id=<?= $course_id; ?>"
             class="px-4 py-2 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800 transition">
            ← Back
          </a>
        </div>
      </header>

      <main class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
          <div class="p-6 border-b border-slate-200 text-center">
            <div class="w-16 h-16 mx-auto rounded-2xl bg-amber-50 flex items-center justify-center">
              <span class="text-3xl">⚠️</span>
            </div>

            <h2 class="mt-4 text-2xl font-bold text-slate-900">Quiz Already Attempted</h2>
            <p class="mt-2 text-slate-600">
              You have already submitted this quiz. Each quiz can only be attempted once.
            </p>
          </div>

          <div class="p-6">
            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4 mb-6">
              <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Quiz</p>
              <p class="mt-1 font-semibold text-slate-800"><?= htmlspecialchars($quiz['title']); ?></p>

              <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mt-4">Course</p>
              <p class="mt-1 font-semibold text-slate-800"><?= htmlspecialchars($quiz['course_title']); ?></p>

              <p class="text-xs text-slate-500 mt-3">Quiz ID: <?= $quiz_id; ?></p>
            </div>

            <div class="flex flex-col sm:flex-row gap-3">
              <a href="quiz_result.php?quiz_id=<?= $quiz_id; ?>"
                 class="flex-1 px-5 py-3 rounded-xl bg-blue-600 text-white font-semibold hover:bg-blue-700 transition text-center">
                View Result
              </a>

              <a href="../start_course.php?id=<?= $course_id; ?>"
                 class="flex-1 px-5 py-3 rounded-xl bg-slate-100 text-slate-700 font-semibold hover:bg-slate-200 transition text-center">
                Back to Course
              </a>
            </div>
          </div>
        </div>
      </main>

    </body>
    </html>
    <?php
    exit;
}

/* =========================================
   5) Fetch questions
========================================= */
$questions = mysqli_query($conn, "
    SELECT * FROM questions
    WHERE quiz_id = $quiz_id
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= htmlspecialchars($quiz['title']) ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-br from-slate-50 to-slate-100 min-h-screen">

  <!-- Header -->
  <header class="sticky top-0 z-40 bg-white/80 backdrop-blur border-b border-slate-200">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
      <div>
        <h1 class="text-xl sm:text-2xl font-bold text-slate-800">Quiz</h1>
        <p class="text-sm text-slate-500">Answer all questions, then submit</p>
      </div>

      <a href="../start_course.php?id=<?= $course_id; ?>"
         class="px-4 py-2 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800 transition">
        ← Back
      </a>
    </div>
  </header>

  <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">

      <!-- Top info -->
      <div class="p-6 border-b border-slate-200">
        <h2 class="text-2xl sm:text-3xl font-bold text-slate-900">
          <?= htmlspecialchars($quiz['title']) ?>
        </h2>

        <div class="mt-3 flex flex-wrap items-center gap-2">
          <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700">
            Course: <?= htmlspecialchars($quiz['course_title']) ?>
          </span>
          <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">
            Quiz ID: <?= $quiz_id ?>
          </span>
        </div>

        <?php if (!empty($quiz['description'])): ?>
          <p class="mt-3 text-sm text-slate-600">
            <?= htmlspecialchars($quiz['description']); ?>
          </p>
        <?php endif; ?>
      </div>

      <div class="p-6">

        <?php if (!$questions || mysqli_num_rows($questions) === 0): ?>
          <div class="text-center py-10">
            <div class="mx-auto w-14 h-14 rounded-2xl bg-slate-100 flex items-center justify-center mb-4">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <p class="text-slate-800 font-semibold">No questions available</p>
            <p class="text-slate-500 text-sm mt-1">This quiz doesn’t have any questions yet.</p>
          </div>
        <?php else: ?>

          <form action="submit_quiz.php" method="POST" class="space-y-6">
            <input type="hidden" name="quiz_id" value="<?= $quiz_id ?>">

            <?php $no = 1; ?>
            <?php while ($q = mysqli_fetch_assoc($questions)) : ?>

              <div class="p-5 rounded-2xl border border-slate-200 hover:bg-slate-50 transition">
                <p class="font-semibold text-slate-800 mb-4">
                  <?= $no++ ?>. <?= htmlspecialchars($q['question_text']) ?>
                </p>

                <?php
                  $choices = mysqli_query($conn, "
                      SELECT * FROM choices
                      WHERE question_id = {$q['question_id']}
                  ");
                ?>

                <div class="space-y-2">
                  <?php while ($c = mysqli_fetch_assoc($choices)) : ?>
                    <label class="flex items-start gap-3 p-3 rounded-xl border border-slate-200 hover:bg-white transition cursor-pointer">
                      <input
                        type="radio"
                        name="answers[<?= $q['question_id'] ?>]"
                        value="<?= $c['choice_id'] ?>"
                        class="mt-1"
                        required
                      />
                      <span class="text-slate-700"><?= htmlspecialchars($c['choice_text']) ?></span>
                    </label>
                  <?php endwhile; ?>
                </div>
              </div>

            <?php endwhile; ?>

            <div class="pt-2 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
              <p class="text-sm text-slate-500">
                Make sure every question has an answer before submitting.
              </p>

              <button
                class="px-6 py-3 rounded-xl bg-blue-600 text-white font-semibold hover:bg-blue-700 transition w-full sm:w-auto">
                Submit Quiz
              </button>
            </div>

          </form>

        <?php endif; ?>

      </div>
    </div>

  </main>

</body>
</html>
