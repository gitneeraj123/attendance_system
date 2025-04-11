<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
    header("Location: ../index.php");
    exit();
}
require_once '../database/db_connection.php';

$student_id = $_SESSION['user_id'];
$attendance_details = [];
$subjects = [];
$selected_subject = isset($_GET['subject_id']) ? $_GET['subject_id'] : null;
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : null;
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : null;

// Fetch enrolled subjects
$subjects_sql = "SELECT s.subject_id, s.subject_name
                 FROM student_subjects ss
                 JOIN subjects s ON ss.subject_id = s.subject_id
                 WHERE ss.student_id = (SELECT student_id FROM students WHERE user_id = $student_id)";
$subjects_result = mysqli_query($conn, $subjects_sql);
if (mysqli_num_rows($subjects_result) > 0) {
    while ($row = mysqli_fetch_assoc($subjects_result)) {
        $subjects[] = $row;
    }
}

if ($selected_subject) {
    $sql = "SELECT a.attendance_date, a.status, s.subject_name
            FROM attendance a
            JOIN subjects s ON a.subject_id = s.subject_id
            WHERE a.student_id = (SELECT student_id FROM students WHERE user_id = $student_id)
            AND a.subject_id = $selected_subject";

    if ($start_date && $end_date) {
        $sql .= " AND a.attendance_date BETWEEN '$start_date' AND '$end_date'";
    }

    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $attendance_details[] = $row;
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
    <title>View Attendance</title>
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
                <a class="nav-link" href="dashboard.php">Dashboard</a>
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
        <h2>View Your Attendance</h2>
        <form method="GET" class="mb-3">
            <div class="form-row align-items-center">
                <div class="col-md-4 mb-2">
                    <label for="subject_id">Subject:</label>
                    <select class="form-control" id="subject_id" name="subject_id" required>
                        <option value="">Select Subject</option>
                        <?php foreach ($subjects as $subject): ?>
                            <option value="<?php echo $subject['subject_id']; ?>" <?php if ($selected_subject == $subject['subject_id']) echo 'selected'; ?>><?php echo $subject['subject_name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3 mb-2">
                    <label for="start_date">Start Date:</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo $start_date; ?>">
                </div>
                <div class="col-md-3 mb-2">
                    <label for="end_date">End Date:</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo $end_date; ?>">
                </div>
                <div class="col-md-2 mb-2">
                    <button type="submit" class="btn btn-primary">Filter</button>
                </div>
            </div>
        </form>

        <?php if (!empty($attendance_details)): ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Subject</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($attendance_details as $attendance): ?>
                        <tr>
                            <td><?php echo $attendance['attendance_date']; ?></td>
                            <td><?php echo $attendance['subject_name']; ?></td>
                            <td><?php echo ucfirst($attendance['status']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php elseif ($selected_subject): ?>
            <p>No attendance records found for the selected subject and date range.</p>
        <?php else: ?>
            <p>Please select a subject to view your attendance.</p>
        <?php endif; ?>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>