<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'instructor') {
  header("Location: ../login.php?error=access_denied");
  exit;
}

include '../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $course_id = $_POST['course_id'];
  $question = $_POST['question'];
  $choices = $_POST['choices'];
  $correct_answer = $_POST['correct_answer'];

  $query = "INSERT INTO quizzes (course_id, question, choices, correct_answer) 
            VALUES ('$course_id', '$question', '$choices', '$correct_answer')";
  if (mysqli_query($conn, $query)) {
    header("Location: manage_quizzes.php?sukses=quiz_created");
  } else {
    header("Location: create_quiz.php?error=quiz_creation_failed");
  }
}
?>

<form method="POST">
  <select name="course_id">
    <!-- Courses -->
  </select>
  <input type="text" name="question" placeholder="Quiz Question" required>
  <input type="text" name="choices[]" placeholder="Choice 1" required>
  <input type="text" name="choices[]" placeholder="Choice 2" required>
  <input type="text" name="choices[]" placeholder="Choice 3" required>
  <input type="text" name="choices[]" placeholder="Choice 4" required>
  <input type="text" name="correct_answer" placeholder="Correct Answer" required>
  <button type="submit">Create Quiz</button>
</form>
