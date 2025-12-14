<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'student') {
  header("Location: ../login.php?error=access_denied");
  exit;
}

include '../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $quiz_id = $_POST['quiz_id'];
  $answers = $_POST['answers'];

  $correct_answers = 0;

  foreach ($answers as $question_id => $answer) {
    // Compare answer with correct answer (Fetch from DB)
    $query = "SELECT correct_answer FROM quizzes WHERE quiz_id='$quiz_id' AND question_id='$question_id'";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);

    if ($answer === $row['correct_answer']) {
      $correct_answers++;
    }
  }

  // Save the score
  $score = ($correct_answers / count($answers)) * 100;

  $query = "INSERT INTO quiz_results (student_id, quiz_id, score) VALUES ('{$_SESSION['user']['user_id']}', '$quiz_id', '$score')";
  mysqli_query($conn, $query);

  header("Location: quiz_result.php?score=$score");
}
?>

<form method="POST">
  <!-- Quiz questions and choices -->
  <button type="submit">Submit Quiz</button>
</form>
