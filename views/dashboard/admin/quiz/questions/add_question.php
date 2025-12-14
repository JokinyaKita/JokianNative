<?php
session_start();
include '../../../../../includes/config/koneksi.php';

$quiz_id = $_GET['quiz_id'] ?? null;
if (!$quiz_id) die("Quiz ID not found.");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $question_text = $_POST['question_text'];

    // Insert question
    mysqli_query($conn, "
        INSERT INTO questions (quiz_id, question_text)
        VALUES ('$quiz_id', '$question_text')
    ");

    $question_id = mysqli_insert_id($conn);

    // Insert choices A-D
    $choices = [
        'A' => $_POST['option_a'],
        'B' => $_POST['option_b'],
        'C' => $_POST['option_c'],
        'D' => $_POST['option_d']
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

<body class="bg-gradient-to-br from-slate-50 to-slate-100 min-h-screen">

  <!-- Header -->
  <header class="sticky top-0 z-40 bg-white/80 backdrop-blur border-b border-slate-200">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
      <div>
        <h1 class="text-xl sm:text-2xl font-bold text-slate-800">Add Question</h1>
        <p class="text-sm text-slate-500">Create a new question and set the correct answer.</p>
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
        <p class="text-sm text-slate-500">Fill the question and options A–D, then choose the correct one.</p>
      </div>

      <div class="p-6">
        <form method="POST" class="space-y-6">

          <!-- Question -->
          <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">Question</label>
            <textarea
              name="question_text"
              rows="4"
              required
              placeholder="Type your question here..."
              class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none resize-none"
            ></textarea>
          </div>

          <!-- Options -->
          <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">Options</label>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <div class="p-4 rounded-xl border border-slate-200 bg-slate-50">
                <div class="text-xs font-semibold text-slate-500 mb-2">Option A</div>
                <input type="text" name="option_a" required
                  placeholder="Type option A..."
                  class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none bg-white" />
              </div>

              <div class="p-4 rounded-xl border border-slate-200 bg-slate-50">
                <div class="text-xs font-semibold text-slate-500 mb-2">Option B</div>
                <input type="text" name="option_b" required
                  placeholder="Type option B..."
                  class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none bg-white" />
              </div>

              <div class="p-4 rounded-xl border border-slate-200 bg-slate-50">
                <div class="text-xs font-semibold text-slate-500 mb-2">Option C</div>
                <input type="text" name="option_c" required
                  placeholder="Type option C..."
                  class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none bg-white" />
              </div>

              <div class="p-4 rounded-xl border border-slate-200 bg-slate-50">
                <div class="text-xs font-semibold text-slate-500 mb-2">Option D</div>
                <input type="text" name="option_d" required
                  placeholder="Type option D..."
                  class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none bg-white" />
              </div>
            </div>
          </div>

          <!-- Correct Answer -->
          <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 p-4 rounded-xl border border-slate-200">
            <div>
              <p class="text-sm font-semibold text-slate-700">Correct Answer</p>
              <p class="text-xs text-slate-500">Select which option is correct.</p>
            </div>

            <select name="correct"
              class="px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:outline-none bg-white w-full sm:w-40">
              <option value="A">A</option>
              <option value="B">B</option>
              <option value="C">C</option>
              <option value="D">D</option>
            </select>
          </div>

          <!-- Actions -->
          <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pt-2">
            <p class="text-sm text-slate-500">
              Quiz ID: <span class="font-semibold text-slate-700"><?= htmlspecialchars($quiz_id); ?></span>
            </p>

            <div class="flex gap-2">
              <a href="manage_questions.php?quiz_id=<?= $quiz_id ?>"
                class="px-5 py-3 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200 transition text-sm font-semibold">
                Cancel
              </a>

              <button class="px-5 py-3 rounded-xl bg-blue-600 text-white hover:bg-blue-700 transition text-sm font-semibold">
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
