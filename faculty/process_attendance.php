<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'faculty') {
    header("Location: ../index.php");
    exit();
}
require_once '../database/db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $faculty_id = $_SESSION['user_id'];
    $subject_id = $_POST['subject_id'];
    $attendance_data = $_POST['attendance'];
    $attendance_date = date("Y-m-d"); // Record attendance for the current date
    $error = false;

    mysqli_begin_transaction($conn); // Start transaction for atomicity

    foreach ($attendance_data as $student_id => $status) {
        // Check if attendance for this student and subject on this date already exists
        $check_sql = "SELECT attendance_id FROM attendance
                      WHERE student_id = $student_id
                        AND subject_id = $subject_id
                        AND attendance_date = '$attendance_date'";
        $check_result = mysqli_query($conn, $check_sql);

        if (mysqli_num_rows($check_result) == 0) {
            // Insert new attendance record
            $insert_sql = "INSERT INTO attendance (student_id, subject_id, attendance_date, status, taken_by)
                           VALUES ($student_id, $subject_id, '$attendance_date', '$status', $faculty_id)";

            if (!mysqli_query($conn, $insert_sql)) {
                $error = true;
                break; // Exit the loop if an error occurs
            }
        }
        else {
            // Attendance already recorded for this student, subject, and date
            // You might want to handle this differently (e.g., update the status)
            // For now, we'll just skip it to avoid duplicates.
        }
    }
    if ($error) {
        mysqli_rollback($conn);
        $_SESSION['error'] = "Error recording attendance. Please try again.";
    } else {
        mysqli_commit($conn);
        $_SESSION['success'] = "Attendance recorded successfully for " . date("d-m-Y");
    }

    mysqli_close($conn);
    header("Location: take_attendance.php?subject_id=" . $subject_id);
    exit();

} else {
    header("Location: take_attendance.php");
    exit();
}
?>