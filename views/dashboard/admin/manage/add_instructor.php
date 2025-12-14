<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../login.php?error=access_denied");
    exit;
}

// Include the database connection
include '../../../../includes/config/koneksi.php';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the form inputs
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    // Check if the email already exists
    $check_email = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    if (mysqli_num_rows($check_email) > 0) {
        $error = "Email already exists!";
    } else {
        // Insert new instructor into the database
        $query = "INSERT INTO users (name, email, password_hash, role) VALUES ('$name', '$email', '$password', 'instructor')";
        if (mysqli_query($conn, $query)) {
            header("Location: manage_instructors.php?sukses=Instructor added successfully");
            exit;
        } else {
            $error = "Failed to add instructor!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Instructor</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
      <!-- Main Content -->
        <div class="flex-1 p-8">
            <div class="bg-white shadow-lg rounded-lg p-6">
                <h1 class="text-3xl font-semibold text-gray-800 mb-6">Add Instructor</h1>

                <!-- Back Button -->
                <a href="manage_instructors.php" class="text-white bg-blue-600 hover:bg-blue-700 p-2 rounded mb-6 inline-block">
                    <i class="fas fa-arrow-left"></i> Back to Manage Instructors
                </a>

                <!-- Add Instructor Form -->
                <?php if (isset($error)): ?>
                    <div class="bg-red-500 text-white p-4 rounded mb-4">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <form action="add_instructor.php" method="POST" class="space-y-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Instructor Name</label>
                        <input type="text" id="name" name="name" required class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600">
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Instructor Email</label>
                        <input type="email" id="email" name="email" required class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600">
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                        <input type="password" id="password" name="password_hash" required class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600">
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="text-white bg-green-600 hover:bg-green-700 px-6 py-2 rounded-md">Add Instructor</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>
</html>
