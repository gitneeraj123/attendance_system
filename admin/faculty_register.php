<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Basic check if a user is logged in (you might want more robust admin authentication)
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Registration</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="#">Attendance System - Admin</a>
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="../faculty/dashboard.php">Faculty Dashboard</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="faculty_register.php">Register Faculty</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../auth/logout.php">Logout</a>
            </li>
        </ul>
    </nav>
    <div class="container mt-4">
        <h2>Register New Faculty Member</h2>
        <?php if (isset($_SESSION['registration_success'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['registration_success']; ?></div>
            <?php unset($_SESSION['registration_success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['registration_error'])): ?>
            <div class="alert alert-danger"><?php echo $_SESSION['registration_error']; ?></div>
            <?php unset($_SESSION['registration_error']); ?>
        <?php endif; ?>
        <form action="process_faculty_registration.php" method="POST">
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
                <small class="form-text text-muted">Password will be hashed for security.</small>
            </div>
            <div class="form-group">
                <label for="employee_id">Employee ID (Optional)</label>
                <input type="text" class="form-control" id="employee_id" name="employee_id">
            </div>
            <div class="form-group">
                <label for="designation">Designation (Optional)</label>
                <input type="text" class="form-control" id="designation" name="designation">
            </div>
            <button type="submit" class="btn btn-primary">Register Faculty</button>
        </form>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>