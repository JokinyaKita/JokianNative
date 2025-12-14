<?php
session_start();
include '../../../../../includes/config/koneksi.php';

$question_id = $_GET['question_id'] ?? null;

if (!$question_id) {
    die("Question ID not found.");
}

// Fetch question
$q = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT * FROM questions WHERE question_id = $question_id
"));

// Fetch choices
$options = mysqli_query($conn, "
    SELECT * FROM choices WHERE question_id = $question_id
");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $question_text = $_POST['question_text'] ?? '';

    // Update question
    mysqli_query($conn, "
        UPDATE questions SET question_text='$question_text'
        WHERE question_id = $question_id
    ");

    // Update choices
    foreach ($_POST['options'] as $choice_id => $text) {

        $is_correct = ($_POST['correct_option'] == $choice_id) ? 1 : 0;

        mysqli_query($conn, "
            UPDATE choices
            SET choice_text='$text', is_correct=$is_correct
            WHERE choice_id = $choice_id
        ");
    }

    header("Location: manage_questions.php?quiz_id=" . $q['quiz_id']);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Edit Question</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-br from-slate-50 to-slate-100 min-h-screen">

  <!-- Header -->
  <header class="sticky top-0 z-40 bg-white/80 backdrop-blur border-b border-slate-200">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
      <div>
        <h1 class="text-xl sm:text-2xl font-bold text-slate-800">Edit Question</h1>
        <p class="text-sm text-slate-500">Update the question text and choose the correct answer.</p>
      </div>

      <a href="manage_questions.php?quiz_id=<?= $q['quiz_id']; ?>"
         class="px-4 py-2 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800 transition">
        ← Back
      </a>
    </div>
  </header>

  <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
      <div class="p-6 border-b border-slate-200">
        <h2 class="text-lg font-semibold text-slate-800">Question Details</h2>
        <p class="text-sm text-slate-500">Make sure only one option is marked as correct.</p>
      </div>

      <div class="p-6">
        <form method="POST" class="space-y-6">

          <!-- Question -->
          <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">Question</label>
            <textarea
              name="question_text"
              required
              rows="4"
              class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none resize-none"
              placeholder="Type the question here..."
            ><?= htmlspecialchars($q['question_text']); ?></textarea>
          </div>

          <!-- Options -->
          <div>
            <div class="flex items-center justify-between">
              <label class="block text-sm font-semibold text-slate-700">Options</label>
              <span class="text-xs text-slate-500">Select the correct answer</span>
            </div>

            <div class="mt-3 space-y-3">
              <?php while ($op = mysqli_fetch_assoc($options)) : ?>
                <div class="flex items-start gap-3 p-4 rounded-xl border border-slate-200 hover:bg-slate-50 transition">
                  <input
                    type="radio"
                    name="correct_option"
                    value="<?= $op['choice_id'] ?>"
                    <?= $op['is_correct'] ? 'checked' : '' ?>
                    class="mt-1"
                    required
                  />

                  <div class="flex-1">
                    <input
                      type="text"
                      name="options[<?= $op['choice_id'] ?>]"
                      value="<?= htmlspecialchars($op['choice_text']); ?>"
                      class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none"
                      required
                      placeholder="Option text..."
                    />

                    <?php if ($op['is_correct']) : ?>
                      <div class="mt-2">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700">
                          Correct answer
                        </span>
                      </div>
                    <?php endif; ?>
                  </div>
                </div>
              <?php endwhile; ?>
            </div>
          </div>

          <!-- Actions -->
          <div class="pt-2 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <p class="text-sm text-slate-500">
              Question ID: <span class="font-semibold text-slate-700"><?= htmlspecialchars($question_id); ?></span>
            </p>

            <div class="flex gap-2">
              <a href="manage_questions.php?quiz_id=<?= $q['quiz_id']; ?>"
                 class="px-5 py-3 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200 transition text-sm font-semibold">
                Cancel
              </a>

              <button
                class="px-5 py-3 rounded-xl bg-blue-600 text-white hover:bg-blue-700 transition text-sm font-semibold">
                Save Changes
              </button>
            </div>
          </div>

        </form>
      </div>
    </div>

  </main>
</body>
</html>
