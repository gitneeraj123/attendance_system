<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit();
}
require_once '../database/db_connection.php';

if (isset($_GET['unassign_id'])) {
    $unassign_id = (int)$_GET['unassign_id'];
    if ($unassign_id > 0) {
        $delete_sql = "DELETE FROM faculty_subjects WHERE faculty_subject_id = $unassign_id";
        if (mysqli_query($conn, $delete_sql)) {
            $_SESSION['success_message'] = "Faculty assignment removed successfully.";
        } else {
            $_SESSION['error_message'] = "Error removing faculty assignment: " . mysqli_error($conn);
        }
        header("Location: assign_faculty_subject.php");
        exit();
    }
}

<option value="<?php echo $faculty['faculty_id']; ?>"><?php echo $faculty['name']; ?></option>

// Fetch all faculty members
$sql_faculty = "SELECT f.faculty_id, u.name FROM faculty f JOIN users u ON f.user_id = u.user_id ORDER BY u.name";
$result_faculty = mysqli_query($conn, $sql_faculty);
$faculty_members = [];
if (mysqli_num_rows($result_faculty) > 0) {
    while ($row = mysqli_fetch_assoc($result_faculty)) {
        $faculty_members[] = $row;
    }
}

// Fetch all subjects
$sql_subjects = "SELECT subject_id, subject_name FROM subjects ORDER BY subject_name";
$result_subjects = mysqli_query($conn, $sql_subjects);
$subjects = [];
if (mysqli_num_rows($result_subjects) > 0) {
    while ($row = mysqli_fetch_assoc($result_subjects)) {
        $subjects[] = $row;
    }
}

// Fetch current faculty-subject assignments
$sql_assignments = "SELECT fs.faculty_subject_id, f.faculty_id, u.name AS faculty_name, s.subject_id, s.subject_name
                    FROM faculty_subjects fs
                    JOIN faculty f ON fs.faculty_id = f.faculty_id
                    JOIN users u ON f.user_id = u.user_id
                    JOIN subjects s ON fs.subject_id = s.subject_id
                    ORDER BY u.name, s.subject_name";
$result_assignments = mysqli_query($conn, $sql_assignments);
$assignments = [];
if (mysqli_num_rows($result_assignments) > 0) {
    while ($row = mysqli_fetch_assoc($result_assignments)) {
        $assignments[] = $row;
    }
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Faculty to Subject</title>
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
                <a class="nav-link" href="manage_users.php">Manage Users</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="manage_subjects.php">Manage Subjects</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="assign_faculty_subject.php">Assign Faculty to Subject</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../auth/logout.php">Logout</a>
            </li>
        </ul>
    </nav>
    <div class="container mt-4">
        <h2>Assign Faculty to Subject</h2>

        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['success_message']; ?></div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error_message']; ?></div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>

        <h3>Current Assignments</h3>
        <?php if (empty($assignments)): ?>
            <p>No faculty members are currently assigned to any subjects.</p>
        <?php else: ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Faculty Name</th>
                        <th>Subject Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($assignments as $assignment): ?>
                        <tr>
                            <td><?php echo $assignment['faculty_name']; ?></td>
                            <td><?php echo $assignment['subject_name']; ?></td>
                            <td>
                                <a href="assign_faculty_subject.php?unassign_id=<?php echo $assignment['faculty_subject_id']; ?>" class="btn btn-sm btn-danger">Unassign</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <h3>Assign New Faculty to Subject</h3>
        <form method="POST" action="process_assign_faculty_subject.php">
            <div class="form-group">
                <label for="faculty_id">Faculty Member:</label>
                <select class="form-control" id="faculty_id" name="faculty_id" required>
                    <option value="">Select Faculty</option>
                    <?php foreach ($faculty_members as $faculty): ?>
                        <option value="<?php echo $faculty['faculty_id']; ?>"><?php echo $faculty['name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="subject_id">Subject:</label>
                <select class="form-control" id="subject_id" name="subject_id" required>
                    <option value="">Select Subject</option>
                    <?php foreach ($subjects as $subject): ?>
                        <option value="<?php echo $subject['subject_id']; ?>"><?php echo $subject['subject_name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Assign Faculty to Subject</button>
        </form>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>