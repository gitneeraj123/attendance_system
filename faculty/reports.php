<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'faculty') {
    header("Location: ../index.php");
    exit();
}
require_once '../database/db_connection.php';

$faculty_id = $_SESSION['user_id'];
$reports_data = [];
$error = '';

// Handle form submission for filtering (if you implement filtering)
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['subject_id']) && isset($_GET['start_date']) && isset($_GET['end_date'])) {
    $subject_id = mysqli_real_escape_string($conn, $_GET['subject_id']);
    $start_date = mysqli_real_escape_string($conn, $_GET['start_date']);
    $end_date = mysqli_real_escape_string($conn, $_GET['end_date']);

    $sql = "SELECT u.name AS student_name, s.subject_name, a.attendance_date, a.status
            FROM attendance a
            JOIN students st ON a.student_id = st.student_id
            JOIN users u ON st.user_id = u.user_id
            JOIN subjects s ON a.subject_id = s.subject_id
            JOIN faculty f ON a.taken_by = f.faculty_id
            WHERE f.user_id = $faculty_id
              AND a.subject_id = $subject_id
              AND a.attendance_date BETWEEN '$start_date' AND '$end_date'
            ORDER BY s.subject_name, u.name, a.attendance_date";

    $result = mysqli_query($conn, $sql);
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $reports_data[] = $row;
        }
    } else {
        $error = "No attendance records found for the selected criteria.";
    }
} else {
    // Fetch all reports for the logged-in faculty (you might want to limit this initially)
    $sql_all = "SELECT u.name AS student_name, s.subject_name, a.attendance_date, a.status
            FROM attendance a
            JOIN students st ON a.student_id = st.student_id
            JOIN users u ON st.user_id = u.user_id
            JOIN subjects s ON a.subject_id = s.subject_id
            JOIN faculty f ON a.taken_by = f.faculty_id
            WHERE f.user_id = $faculty_id
            -- AND a.subject_id = $subject_id
            -- AND a.attendance_date BETWEEN '$start_date' AND '$end_date'
            ORDER BY s.subject_name, u.name, a.attendance_date";
    $result_all = mysqli_query($conn, $sql_all);
    if ($result_all && mysqli_num_rows($result_all) > 0) {
        while ($row = mysqli_fetch_assoc($result_all)) {
            $reports_data[] = $row;
        }
    }
}

// Fetch subjects taught by the faculty for the filter dropdown
$faculty_subjects_sql = "SELECT s.subject_id, s.subject_name
                         FROM faculty_subjects fs
                         JOIN subjects s ON fs.subject_id = s.subject_id
                         WHERE fs.faculty_id = (SELECT faculty_id FROM faculty WHERE user_id = $faculty_id)";
$faculty_subjects_result = mysqli_query($conn, $faculty_subjects_sql);
$faculty_subjects = [];
if (mysqli_num_rows($faculty_subjects_result) > 0) {
    while ($row = mysqli_fetch_assoc($faculty_subjects_result)) {
        $faculty_subjects[$row['subject_id']] = $row['subject_name'];
    }
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Attendance Reports</title>
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
                <a class="nav-link active" href="reports.php">View Reports</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../auth/logout.php">Logout</a>
            </li>
        </ul>
    </nav>
    <div class="container mt-4">
        <h2>Attendance Reports</h2>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="GET" class="mb-3">
            <div class="form-group">
                <label for="subject_id">Select Subject:</label>
                <select class="form-control" id="subject_id" name="subject_id" required>
                    <option value="">All Subjects</option>
                    <?php
                    foreach ($faculty_subjects as $id => $name):
                        echo '<option value="' . $id . '">' . $name . '</option>';
                    endforeach;
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="start_date">Start Date:</label>
                <input type="date" class="form-control" id="start_date" name="start_date">
            </div>
            <div class="form-group">
                <label for="end_date">End Date:</label>
                <input type="date" class="form-control" id="end_date" name="end_date">
            </div>
            <button type="submit" class="btn btn-primary">Filter Reports</button>
        </form>

        <?php if (!empty($reports_data)): ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Subject</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reports_data as $report): ?>
                        <tr>
                            <td><?php echo $report['student_name']; ?></td>
                            <td><?php echo $report['subject_name']; ?></td>
                            <td><?php echo $report['attendance_date']; ?></td>
                            <td><?php echo $report['status']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No attendance reports available.</p>
        <?php endif; ?>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>