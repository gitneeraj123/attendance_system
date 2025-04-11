<?php
session_start();
require_once '../database/db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $subject_id = mysqli_real_escape_string($conn, $_POST['subject_id']);
    $student_ids = isset($_POST['student_ids']) ? $_POST['student_ids'] : [];
    $error = false;
    $enrollment_count = 0;

    if (!empty($student_ids) && !empty($subject_id)) {
        foreach ($student_ids as $student_id) {
            $student_id = mysqli_real_escape_string($conn, $student_id);

            // Check if the enrollment already exists (optional)
            $check_sql = "SELECT * FROM student_subjects WHERE student_id = $student_id AND subject_id = $subject_id";
            $check_result = mysqli_query($conn, $check_sql);

            if (mysqli_num_rows($check_result) == 0) {
                $insert_sql = "INSERT INTO student_subjects (student_id, subject_id) VALUES ($student_id, $subject_id)";
                if (mysqli_query($conn, $insert_sql)) {
                    $enrollment_count++;
                } else {
                    $error = true;
                    break; // Stop on the first error
                }
            }
        }

        if (!$error && $enrollment_count > 0) {
            $_SESSION['enrollment_success'] = "$enrollment_count student(s) enrolled successfully.";
        } elseif ($error) {
            $_SESSION['enrollment_error'] = "Error enrolling students: " . mysqli_error($conn);
        } else {
            $_SESSION['enrollment_info'] = "No new students were enrolled (they might already be enrolled in this subject).";
        }
    } else {
        $_SESSION['enrollment_error'] = "Please select a subject and at least one student.";
    }

    mysqli_close($conn);
    header("Location: enroll_students.php");
    exit();
} else {
    header("Location: enroll_students.php");
    exit();
}
?>