<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'superadmin') {
    header("Location: ../login.php");
    exit;
}

include '../../../../includes/config/koneksi.php';

$course_id = intval($_GET['course_id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    $query = "INSERT INTO quizzes (course_id, title, description)
              VALUES ($course_id, '$title', '$description')";

    if (mysqli_query($conn, $query)) {
        header("Location: manage_quiz.php?course_id=$course_id&sukses=quiz_added");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Quiz</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 p-8">

<div class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow">

    <h1 class="text-3xl font-semibold mb-4">Add New Quiz</h1>

    <form action="" method="POST">

        <label class="block mb-2 font-medium">Title:</label>
        <input type="text" name="title" required
               class="w-full p-2 border rounded mb-4">

        <label class="block mb-2 font-medium">Description:</label>
        <textarea name="description" rows="4"
                  class="w-full p-2 border rounded mb-4"></textarea>

        <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Save
        </button>

        <a href="manage_quiz.php?course_id=<?= $course_id ?>"
           class="bg-gray-700 text-white px-4 py-2 rounded ml-2">Cancel</a>

    </form>

</div>

</body>
</html>
