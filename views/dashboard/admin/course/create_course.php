<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'instructor') {
  header("Location: ../login.php?error=access_denied");
  exit;
}

include '../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $title = $_POST['title'];
  $description = $_POST['description'];
  $instructor_id = $_SESSION['user']['user_id']; // Get instructor id from session

  $query = "INSERT INTO courses (title, description, instructor_id) VALUES ('$title', '$description', '$instructor_id')";
  if (mysqli_query($conn, $query)) {
    header("Location: manage_courses.php?sukses=course_created");
  } else {
    header("Location: create_course.php?error=course_creation_failed");
  }
}
?>

<form method="POST">
  <input type="text" name="title" placeholder="Course Title" required>
  <textarea name="description" placeholder="Course Description" required></textarea>
  <button type="submit">Create Course</button>
</form>
