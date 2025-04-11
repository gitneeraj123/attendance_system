<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'faculty') {
    header("Location: ../index.php");
    exit();
}
require_once '../database/db_connection.php';

$faculty_id = $_SESSION['user_id'];

// Fetch subjects taught by the faculty
$sql = "SELECT s.subject_id, s.subject_name
        FROM faculty_subjects fs
        JOIN subjects s ON fs.subject_id = s.subject_id
        WHERE fs.faculty_id = (SELECT faculty_id FROM faculty WHERE user_id = $faculty_id)";
$result = mysqli_query($conn, $sql);
$assigned_subjects = [];
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $assigned_subjects[] = $row;
    }
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="#">Attendance System</a>
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <span class="nav-link">Welcome, <?php echo $_SESSION['name']; ?> (Faculty)</span>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="take_attendance.php">Take Attendance</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="reports.php">View Reports</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../auth/logout.php">Logout</a>
            </li>
        </ul>
    </nav>
    <div class="container mt-4">
        <h2>Assigned Subjects</h2>
        <?php if (empty($assigned_subjects)): ?>
            <p>No subjects assigned to you yet.</p>
        <?php else: ?>
            <ul class="list-group">
                <?php foreach ($assigned_subjects as $subject): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <?php echo $subject['subject_name']; ?>
                        <a href="take_attendance.php?subject_id=<?php echo $subject['subject_id']; ?>" class="btn btn-sm btn-primary">Take Attendance</a>
                        <a href="reports.php?subject_id=<?php echo $subject['subject_id']; ?>" class="btn btn-sm btn-info">View Report</a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>