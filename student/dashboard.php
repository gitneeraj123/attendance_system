<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
    header("Location: ../index.php");
    exit();
}
require_once '../database/db_connection.php';

$student_id = $_SESSION['user_id'];

// Fetch student's enrolled subjects and attendance summary
$sql = "SELECT s.subject_name,
               COUNT(a.attendance_id) AS total_classes,
               SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) AS present_classes
        FROM student_subjects ss
        JOIN subjects s ON ss.subject_id = s.subject_id
        LEFT JOIN attendance a ON ss.subject_id = a.subject_id AND a.student_id = $student_id
        WHERE ss.student_id = (SELECT student_id FROM students WHERE user_id = $student_id)
        GROUP BY s.subject_name";
$result = mysqli_query($conn, $sql);
$attendance_summary = [];
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $total = $row['total_classes'] > 0 ? $row['total_classes'] : 1; // Avoid division by zero
        $percentage = ($row['present_classes'] / $total) * 100;
        $attendance_summary[] = [
            'subject_name' => $row['subject_name'],
            'present' => $row['present_classes'],
            'total' => $row['total_classes'],
            'percentage' => round($percentage, 2)
        ];
    }
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="#">Attendance System</a>
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <span class="nav-link">Welcome, <?php echo $_SESSION['name']; ?> (Student)</span>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="view_attendance.php">View Attendance</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../auth/change_password.php">Change Password</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../auth/logout.php">Logout</a>
            </li>
        </ul>
    </nav>
    <div class="container mt-4">
        <h2>Your Attendance Summary</h2>
        <?php if (empty($attendance_summary)): ?>
            <p>No attendance records found yet.</p>
        <?php else: ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Subject</th>
                        <th>Present</th>
                        <th>Total Classes</th>
                        <th>Percentage</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($attendance_summary as $attendance): ?>
                        <tr>
                            <td><?php echo $attendance['subject_name']; ?></td>
                            <td><?php echo $attendance['present']; ?></td>
                            <td><?php echo $attendance['total']; ?></td>
                            <td><?php echo $attendance['percentage']; ?>%</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>