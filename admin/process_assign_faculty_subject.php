<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit();
}
require_once '../database/db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $faculty_id = isset($_POST['faculty_id']) ? (int)$_POST['faculty_id'] : 0;
    $subject_id = isset($_POST['subject_id']) ? (int)$_POST['subject_id'] : 0;

    if ($faculty_id > 0 && $subject_id > 0) {
        // Check if the assignment already exists
        $check_sql = "SELECT faculty_subject_id FROM faculty_subjects
                      WHERE faculty_id = $faculty_id AND subject_id = $subject_id";
        $check_result = mysqli_query($conn, $check_sql);

        if (mysqli_num_rows($check_result) == 0) {
            // Insert the new assignment
            $insert_sql = "INSERT INTO faculty_subjects (faculty_id, subject_id)
                           VALUES ($faculty_id, $subject_id)";

            if (mysqli_query($conn, $insert_sql)) {
                $_SESSION['success_message'] = "Faculty member assigned to subject successfully.";
            } else {
                $_SESSION['error_message'] = "Error assigning faculty to subject: " . mysqli_error($conn);
            }
        } else {
            $_SESSION['warning_message'] = "This faculty member is already assigned to this subject.";
        }
    } else {
        $_SESSION['error_message'] = "Invalid faculty or subject selected.";
    }

    header("Location: assign_faculty_subject.php");
    exit();
} else {
    // If the script is accessed directly without a POST request
    header("Location: assign_faculty_subject.php");
    die();
    exit();
}

mysqli_close($conn);
?>