<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'superadmin') {
    header("Location: ../login.php?error=access_denied");
    exit;
}

// Include the database connection
include '../../../../includes/config/koneksi.php';

// Query to fetch all instructors
$query_instructors = "SELECT * FROM users WHERE role='instructor'";
$result_instructors = mysqli_query($conn, $query_instructors);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Instructors</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
        <!-- Main Content -->
        <div class="flex-1 p-8">
            <div class="bg-white shadow-lg rounded-lg p-6">
                <h1 class="text-3xl font-semibold text-gray-800 mb-6">Manage Instructors</h1>

                <!-- Back Button -->
                <a href="../dashboard.php" class="text-white bg-blue-600 hover:bg-blue-700 p-2 rounded mb-6 inline-block">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>

                <!-- Add New Instructor Button -->
                <a href="add_instructor.php" class="text-white bg-green-600 hover:bg-green-700 p-2 rounded mb-6 inline-block">
                    Add New Instructor
                </a>

                <!-- Instructor List Table -->
                <table class="min-w-full bg-white border border-gray-300 shadow-lg rounded-lg">
                    <thead class="bg-blue-600 text-white">
                        <tr>
                            <th class="py-3 px-4">#</th>
                            <th class="py-3 px-4">Instructor Name</th>
                            <th class="py-3 px-4">Email</th>
                            <th class="py-3 px-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($result_instructors) > 0): ?>
                            <?php $no = 1; while ($instructor = mysqli_fetch_assoc($result_instructors)): ?>
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="py-2 px-4"><?php echo $no++; ?></td>
                                    <td class="py-2 px-4"><?php echo htmlspecialchars($instructor['name']); ?></td>
                                    <td class="py-2 px-4"><?php echo htmlspecialchars($instructor['email']); ?></td>
                                    <td class="py-2 px-4 text-center">
                                        <!-- Edit Instructor Button -->
                                        <a href="edit_instructor.php?id=<?php echo $instructor['id']; ?>" class="text-yellow-500 hover:underline">Edit</a> |
                                        <!-- Delete Instructor Button -->
                                        <a href="delete_instructor.php?id=<?php echo $instructor['id']; ?>" 
                                           onclick="return confirm('Are you sure you want to delete this instructor?')" 
                                           class="text-red-500 hover:underline">Delete</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="py-2 px-4 text-center text-gray-500">No instructors found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>
</html>

