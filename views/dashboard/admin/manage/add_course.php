<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../login.php?error=access_denied");
    exit;
}

include '../../../../includes/config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the form inputs
    $title = htmlspecialchars($_POST['title']);
    $description = htmlspecialchars($_POST['description']);
    
    // Check if an instructor is selected, otherwise set it to NULL
    $instructor_id = !empty($_POST['instructor_id']) ? $_POST['instructor_id'] : NULL;

    // Check if the course already exists
    $check_course = mysqli_query($conn, "SELECT * FROM courses WHERE title='$title'");
    if (mysqli_num_rows($check_course) > 0) {
        $error = "Course with this title already exists!";
    } else {
        // Insert the new course into the database
        $query = "INSERT INTO courses (title, description, instructor_id) VALUES ('$title', '$description', '$instructor_id')";
        if (mysqli_query($conn, $query)) {
            header("Location: manage_courses.php?sukses=Course added successfully");
            exit;
        } else {
            $error = "Failed to add course!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Course</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

    <!-- Main Content -->
    <div class="flex-1 p-8">
        <div class="bg-white shadow-lg rounded-lg p-6">
            <h1 class="text-3xl font-semibold text-gray-800 mb-6">Add New Course</h1>

            <!-- Back Button -->
            <a href="manage_courses.php" class="text-white bg-blue-600 hover:bg-blue-700 p-2 rounded mb-6 inline-block">
                <i class="fas fa-arrow-left"></i> Back to Manage Courses
            </a>

            <!-- Form -->
            <?php if (isset($error)): ?>
                <div class="bg-red-500 text-white p-4 rounded mb-4">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <form action="add_course.php" method="POST" class="space-y-4">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700">Course Title</label>
                    <input type="text" id="title" name="title" required class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600">
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Course Description</label>
                    <textarea id="description" name="description" rows="4" required class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600"></textarea>
                </div>

                <div>
                    <label for="instructor_id" class="block text-sm font-medium text-gray-700">Instructor</label>
                    <select id="instructor_id" name="instructor_id" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600">
                        <option value="">Select Instructor</option>
                        <?php
                        // Fetch instructors from the database
                        $instructors = mysqli_query($conn, "SELECT user_id, name FROM users WHERE role='instructor'");
                        while ($instructor = mysqli_fetch_assoc($instructors)) {
                            echo "<option value='{$instructor['user_id']}'>{$instructor['name']}</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="text-white bg-green-600 hover:bg-green-700 px-6 py-2 rounded-md">Add Course</button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>
