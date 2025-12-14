<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'instructor') {
    header("Location: ../../login.php?error=access_denied");
    exit;
}

include '../../../../../../includes/config/koneksi.php';

$quiz_id = $_GET['quiz_id'] ?? null;
if (!$quiz_id) die("Quiz ID not found.");

$quiz_id = intval($quiz_id);
$instructor_id = $_SESSION['user']['user_id'];

// Validate: quiz belongs to instructor (via course)
$quiz = mysqli_fetch_assoc(mysqli_query($conn, "
  SELECT q.quiz_id, q.title, q.course_id, c.title AS course_title
  FROM quizzes q
  JOIN courses c ON q.course_id = c.course_id
  WHERE q.quiz_id = $quiz_id AND c.instructor_id = '$instructor_id'
"));
if (!$quiz) die("Access denied. This quiz is not assigned to you.");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $question_text = mysqli_real_escape_string($conn, $_POST['question_text'] ?? '');

    // Insert question
    mysqli_query($conn, "
        INSERT INTO questions (quiz_id, question_text)
        VALUES ('$quiz_id', '$question_text')
    ");

    $question_id = mysqli_insert_id($conn);

    // Insert choices A-D
    $choices = [
        'A' => mysqli_real_escape_string($conn, $_POST['option_a'] ?? ''),
        'B' => mysqli_real_escape_string($conn, $_POST['option_b'] ?? ''),
        'C' => mysqli_real_escape_string($conn, $_POST['option_c'] ?? ''),
        'D' => mysqli_real_escape_string($conn, $_POST['option_d'] ?? '')
    ];

    $correct = $_POST['correct'] ?? '';

    foreach ($choices as $key => $value) {
        if (trim($value) === "") continue;

        $is_correct = ($correct === $key) ? 1 : 0;

        mysqli_query($conn, "
            INSERT INTO choices (question_id, choice_text, is_correct)
            VALUES ('$question_id', '$value', '$is_correct')
        ");
    }

    header("Location: manage_questions.php?quiz_id=$quiz_id&success=1");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Add Question</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-br from-indigo-50 to-slate-100 min-h-screen">

  <header class="sticky top-0 z-40 bg-white/80 backdrop-blur border-b border-slate-200">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
      <div>
        <h1 class="text-xl sm:text-2xl font-bold text-slate-800">Add Question</h1>
        <p class="text-sm text-slate-500">
          Course: <span class="font-semibold text-slate-700"><?= htmlspecialchars($quiz['course_title']); ?></span>
          • Quiz: <span class="font-semibold text-slate-700"><?= htmlspecialchars($quiz['title']); ?></span>
        </p>
      </div>

      <a href="manage_questions.php?quiz_id=<?= $quiz_id ?>"
         class="px-4 py-2 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800 transition">
        ← Back
      </a>
    </div>
  </header>

  <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
      <div class="p-6 border-b border-slate-200">
        <h2 class="text-lg font-semibold text-slate-800">Question Form</h2>
        <p class="text-sm text-slate-500">Fill the question and options A–D, then select the correct answer.</p>
      </div>

      <div class="p-6">
        <form method="POST" class="space-y-6">

          <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">Question</label>
            <textarea name="question_text" rows="4" required
              placeholder="Type your question here..."
              class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none resize-none"></textarea>
          </div>

          <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">Options</label>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <div class="p-4 rounded-xl border border-slate-200 bg-slate-50">
                <div class="text-xs font-semibold text-slate-500 mb-2">Option A</div>
                <input type="text" name="option_a" required placeholder="Option A..."
                  class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none bg-white" />
              </div>

              <div class="p-4 rounded-xl border border-slate-200 bg-slate-50">
                <div class="text-xs font-semibold text-slate-500 mb-2">Option B</div>
                <input type="text" name="option_b" required placeholder="Option B..."
                  class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none bg-white" />
              </div>

              <div class="p-4 rounded-xl border border-slate-200 bg-slate-50">
                <div class="text-xs font-semibold text-slate-500 mb-2">Option C</div>
                <input type="text" name="option_c" required placeholder="Option C..."
                  class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none bg-white" />
              </div>

              <div class="p-4 rounded-xl border border-slate-200 bg-slate-50">
                <div class="text-xs font-semibold text-slate-500 mb-2">Option D</div>
                <input type="text" name="option_d" required placeholder="Option D..."
                  class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none bg-white" />
              </div>
            </div>
          </div>

          <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 p-4 rounded-xl border border-slate-200">
            <div>
              <p class="text-sm font-semibold text-slate-700">Correct Answer</p>
              <p class="text-xs text-slate-500">Select which option is correct.</p>
            </div>

            <select name="correct"
              class="px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none bg-white w-full sm:w-40">
              <option value="A">A</option>
              <option value="B">B</option>
              <option value="C">C</option>
              <option value="D">D</option>
            </select>
          </div>

          <div class="pt-2 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <p class="text-sm text-slate-500">
              Quiz ID: <span class="font-semibold text-slate-700"><?= htmlspecialchars($quiz_id); ?></span>
            </p>

            <div class="flex gap-2">
              <a href="manage_questions.php?quiz_id=<?= $quiz_id ?>"
                class="px-5 py-3 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200 transition text-sm font-semibold">
                Cancel
              </a>

              <button class="px-5 py-3 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 transition text-sm font-semibold">
                Save Question
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
