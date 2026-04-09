<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'superadmin') {
    header("Location: ../../login.php?error=access_denied");
    exit;
}

include '../../../../../includes/config/koneksi.php';

$quiz_id = $_GET['quiz_id'] ?? null;
$quiz = mysqli_fetch_assoc(mysqli_query(
    $conn,
    "SELECT title, course_id FROM quizzes WHERE quiz_id = $quiz_id"
));

if (!$quiz) {
    die("Quiz not found.");
}

$course_id = $quiz['course_id'];


// Fetch quiz title
$quiz = mysqli_fetch_assoc(mysqli_query($conn, "SELECT title FROM quizzes WHERE quiz_id=$quiz_id"));

// Fetch questions
$questions = mysqli_query($conn, "SELECT * FROM questions WHERE quiz_id=$quiz_id");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Manage Questions</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-br from-slate-50 to-slate-100 min-h-screen">

  <!-- Top Bar -->
  <header class="sticky top-0 z-40 bg-white/80 backdrop-blur border-b border-slate-200">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
      <div>
        <h1 class="text-xl sm:text-2xl font-bold text-slate-800">Manage Questions</h1>
        <p class="text-sm text-slate-500">
          Quiz: <span class="font-semibold text-slate-700"><?= htmlspecialchars($quiz['title']); ?></span>
        </p>
      </div>

      <div class="flex items-center gap-2">
        <!-- back to manage_quiz (assumes quiz page uses quiz_id) -->
       <a href="../manage_quiz.php?course_id=<?= $course_id ?>"
             class="px-4 py-2 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800 transition">
         ← Back
        </a>

        <a href="add_question.php?quiz_id=<?= $quiz_id ?>"
           class="px-4 py-2 rounded-xl bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700 transition">
          + Add Question
        </a>
      </div>
    </div>
  </header>

  <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">

      <!-- Header -->
      <div class="p-6 border-b border-slate-200 flex items-center justify-between">
        <div>
          <h2 class="text-lg font-semibold text-slate-800">Questions List</h2>
          <p class="text-sm text-slate-500">Edit or delete questions in this quiz.</p>
        </div>
        <div class="text-sm text-slate-500">
          Total: <span class="font-semibold text-slate-700"><?= mysqli_num_rows($questions) ?></span>
        </div>
      </div>

      <!-- Content -->
      <div class="p-6">
        <?php if (mysqli_num_rows($questions) == 0): ?>
          <div class="text-center py-10">
            <div class="mx-auto w-14 h-14 rounded-2xl bg-slate-100 flex items-center justify-center mb-4">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <p class="text-slate-800 font-semibold">No questions yet</p>
            <p class="text-slate-500 text-sm mt-1">Click “Add Question” to create the first question.</p>
          </div>
        <?php else: ?>

          <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
              <thead class="bg-slate-50 border border-slate-200">
                <tr class="text-left text-xs font-semibold uppercase tracking-wider text-slate-600">
                  <th class="py-3 px-4">Question</th>
                  <th class="py-3 px-4 text-right">Actions</th>
                </tr>
              </thead>

              <tbody class="divide-y divide-slate-200 text-slate-700">
                <?php while ($q = mysqli_fetch_assoc($questions)) : ?>
                  <tr class="hover:bg-slate-50 transition">
                    <td class="py-4 px-4">
                      <div class="font-medium text-slate-800">
                        <?= htmlspecialchars($q['question_text']); ?>
                      </div>
                      <div class="text-xs text-slate-500 mt-1">
                        Question ID: <?= $q['question_id'] ?>
                      </div>
                    </td>

                    <td class="py-4 px-4">
                      <div class="flex items-center justify-end gap-2 flex-wrap">
                        <a href="edit_question.php?question_id=<?= $q['question_id'] ?>"
                           class="inline-flex items-center px-3 py-2 rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100 transition text-xs font-semibold">
                          Edit
                        </a>

                        <a href="delete_questions.php?question_id=<?= $q['question_id'] ?>&quiz_id=<?= $quiz_id ?>"
                           class="inline-flex items-center px-3 py-2 rounded-lg bg-red-50 text-red-700 hover:bg-red-100 transition text-xs font-semibold"
                           onclick="return confirm('Delete this question?')">
                          Delete
                        </a>
                      </div>
                    </td>
                  </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>

        <?php endif; ?>
      </div>
    </div>
  </main>

</body>
</html>
