<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit();
}
require_once '../database/db_connection.php';

// Fetch all users
$sql = "SELECT user_id, role, username, name, email FROM users ORDER BY role, name";
$result = mysqli_query($conn, $sql);
$users = [];
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $users[] = $row;
    }
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="dashboard.php">Attendance System - Admin</a>
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="faculty_register.php">Register Faculty</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="manage_users.php">Manage Users</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="manage_subjects.php">Manage Subjects</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="assign_faculty_subject.php">Assign Faculty to Subject</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../auth/logout.php">Logout</a>
            </li>
        </ul>
    </nav>
    <div class="container mt-4">
        <h2>Manage Users</h2>
        <?php if (empty($users)): ?>
            <p>No users found.</p>
        <?php else: ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Role</th>
                        <th>Username</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo $user['user_id']; ?></td>
                            <td><?php echo ucfirst($user['role']); ?></td>
                            <td><?php echo $user['username']; ?></td>
                            <td><?php echo $user['name']; ?></td>
                            <td><?php echo $user['email']; ?></td>
                            <td>
                                <button class="btn btn-sm btn-warning">Edit</button>
                                <button class="btn btn-sm btn-danger">Delete</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <p><button class="btn btn-success">Add New User</button></p>
        <?php endif; ?>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>