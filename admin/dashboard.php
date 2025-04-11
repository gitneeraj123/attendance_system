<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <!-- <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="#">Attendance System - Admin</a>
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <span class="nav-link">Welcome, <?php echo $_SESSION['name']; ?> (Admin)</span>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="faculty_register.php">Register Faculty</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="manage_users.php">Manage Users</a>
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
    </nav> -->

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="dashboard.php">Attendance System - Admin</a>
    <ul class="navbar-nav ml-auto">
        <li class="nav-item">
            <a class="nav-link" href="faculty_register.php">Register Faculty</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="manage_users.php">Manage Users</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="manage_subjects.php">Manage Subjects</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="assign_faculty_subject.php">Assign Faculty to Subject</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="enroll_students.php">Enroll Students</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="../auth/logout.php">Logout</a>
        </li>
    </ul>
</nav>

    <div class="container mt-4">
        <h2>Admin Dashboard</h2>
        <p>Use the navigation links above to manage the system.</p>

        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card shadow">
                    <div class="card-body">
                        <h5 class="card-title">User Management</h5>
                        <p class="card-text">View, edit, and manage user accounts (students, faculty, admins).</p>
                        <a href="manage_users.php" class="btn btn-primary btn-sm">Go to User Management</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow">
                    <div class="card-body">
                        <h5 class="card-title">Subject Management</h5>
                        <p class="card-text">Add, edit, and manage subjects offered in the system.</p>
                        <a href="manage_subjects.php" class="btn btn-info btn-sm">Go to Subject Management</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow">
                    <div class="card-body">
                        <h5 class="card-title">Faculty Assignment</h5>
                        <p class="card-text">Assign faculty members to teach specific subjects.</p>
                        <a href="assign_faculty_subject.php" class="btn btn-success btn-sm">Go to Faculty Assignment</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>