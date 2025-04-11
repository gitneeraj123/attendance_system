<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}
require_once '../database/db_connection.php';

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password != $confirm_password) {
        $error = "New passwords do not match.";
    } elseif (strlen($new_password) < 6) {
        $error = "New password must be at least 6 characters long.";
    } else {
        $user_id = $_SESSION['user_id'];
        $sql = "SELECT password FROM users WHERE user_id = $user_id";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) == 1) {
            $row = mysqli_fetch_assoc($result);
            if (password_verify($old_password, $row['password'])) {
                $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);
                $update_sql = "UPDATE users SET password = '$hashed_new_password' WHERE user_id = $user_id";
                if (mysqli_query($conn, $update_sql)) {
                    if ($_SESSION['role'] == 'student') {
                        $update_student_sql = "UPDATE students SET first_login = 0 WHERE user_id = $user_id";
                        mysqli_query($conn, $update_student_sql);
                    }
                    $success = "Password changed successfully.";
                } else {
                    $error = "Error updating password: " . mysqli_error($conn);
                }
            } else {
                $error = "Incorrect old password.";
            }
        } else {
            $error = "User not found.";
        }
    }
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="#">Attendance System</a>
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <span class="nav-link">Welcome, <?php echo $_SESSION['name']; ?> (<?php echo ucfirst($_SESSION['role']); ?>)</span>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo ($_SESSION['role'] == 'student' ? '../student/dashboard.php' : '../faculty/dashboard.php'); ?>">Dashboard</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="logout.php">Logout</a>
            </li>
        </ul>
    </nav>
    <div class="container mt-4">
        <h2>Change Password</h2>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label for="old_password">Old Password</label>
                <input type="password" class="form-control" id="old_password" name="old_password" required>
            </div>
            <div class="form-group">
                <label for="new_password">New Password</label>
                <input type="password" class="form-control" id="new_password" name="new_password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm New Password</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
            </div>
            <button type="submit" class="btn btn-primary">Change Password</button>
        </form>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>