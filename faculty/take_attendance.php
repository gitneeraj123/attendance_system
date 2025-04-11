<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'faculty') {
    header("Location: ../index.php");
    exit();
}
require_once '../database/db_connection.php';

$faculty_id = $_SESSION['user_id'];
$error = '';
$success = '';
$subjects = [];
$students = [];
$selected_subject_id = isset($_GET['subject_id']) ? $_GET['subject_id'] : null;

// Fetch subjects taught by the faculty
$faculty_subjects_sql = "SELECT s.subject_id, s.subject_name
                         FROM faculty_subjects fs
                         JOIN subjects s ON fs.subject_id = s.subject_id
                         WHERE fs.faculty_id = (SELECT faculty_id FROM faculty WHERE user_id = $faculty_id)";
$faculty_subjects_result = mysqli_query($conn, $faculty_subjects_sql);
if (mysqli_num_rows($faculty_subjects_result) > 0) {
    while ($row = mysqli_fetch_assoc($faculty_subjects_result)) {
        $subjects[$row['subject_id']] = $row['subject_name'];
    }
}

// Fetch students for the selected subject
if ($selected_subject_id) {
    $students_sql = "SELECT s.student_id, u.name AS student_name, u.username AS roll_number
                     FROM student_subjects ss
                     JOIN students s ON ss.student_id = s.student_id
                     JOIN users u ON s.user_id = u.user_id
                     WHERE ss.subject_id = $selected_subject_id
                     ORDER BY u.name";
    $students_result = mysqli_query($conn, $students_sql);
    if ($students_result && mysqli_num_rows($students_result) > 0) {
        while ($row = mysqli_fetch_assoc($students_result)) {
            $students[] = $row;
        }
    } else {
        $error = "No students enrolled in the selected subject.";
    }
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Take Attendance</title>
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
                <a class="nav-link active" href="take_attendance.php">Take Attendance</a>
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
        <h2>Take Attendance</h2>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['success']; ?></div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <form method="GET" class="mb-3">
            <div class="form-group">
                <label for="subject_id">Select Subject:</label>
                <select class="form-control" id="subject_id" name="subject_id" required onchange="this.form.submit()">
                    <option value="">Select Subject</option>
                    <?php
                    foreach ($subjects as $id => $name):
                        echo '<option value="' . $id . '" ' . ($selected_subject_id == $id ? 'selected' : '') . '>' . $name . '</option>';
                    endforeach;
                    ?>
                </select>
            </div>
        </form>

        <?php if (!empty($students) && $selected_subject_id): ?>
            <h3>Attendance for <?php echo $subjects[$selected_subject_id]; ?></h3>
            <form method="POST" action="process_attendance.php">
                <input type="hidden" name="subject_id" value="<?php echo $selected_subject_id; ?>">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Roll Number</th>
                            <th>Student Name</th>
                            <th>Present</th>
                            <th>Absent</th>
                            <th>Late</th>
                            <th>Excused</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $student): ?>
                            <tr>
                                <td><?php echo $student['roll_number']; ?></td>
                                <td><?php echo $student['student_name']; ?></td>
                                <td><input type="radio" name="attendance[<?php echo $student['student_id']; ?>]" value="present" checked></td>
                                <td><input type="radio" name="attendance[<?php echo $student['student_id']; ?>]" value="absent"></td>
                                <td><input type="radio" name="attendance[<?php echo $student['student_id']; ?>]" value="late"></td>
                                <td><input type="radio" name="attendance[<?php echo $student['student_id']; ?>]" value="excused"></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <button type="submit" class="btn btn-primary">Submit Attendance</button>
            </form>
        <?php elseif ($selected_subject_id && empty($students) && !$error): ?>
            <p>No students are currently enrolled in this subject.</p>
        <?php endif; ?>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>