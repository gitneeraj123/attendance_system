<?php
session_start();
require_once '../database/db_connection.php'; // Include database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Sanitize inputs (important for security)
    $username = mysqli_real_escape_string($conn, $username);
    $password = mysqli_real_escape_string($conn, $password);

    $sql = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['role'] = $row['role'];
            $_SESSION['name'] = $row['name'];


            if ($row['role'] == 'admin') {
                header("Location: ../admin/dashboard.php");
                exit();
            }

            elseif ($row['role'] == 'student') {
                // Check if it's the first login to force password change
                $student_sql = "SELECT first_login FROM students WHERE user_id = " . $row['user_id'];
                $student_result = mysqli_query($conn, $student_sql);
                if (mysqli_num_rows($student_result) == 1) {
                    $student_data = mysqli_fetch_assoc($student_result);
                    if ($student_data['first_login'] == 1) {
                        header("Location: change_password.php");
                        exit();
                    }
                }
                header("Location: ../student/dashboard.php");
                exit();
            } elseif ($row['role'] == 'faculty') {
                header("Location: ../faculty/dashboard.php");
                exit();
            }
        } else {
            $_SESSION['error'] = "Incorrect password.";
            header("Location: ../index.php");
            exit();
        }
    } else {
        $_SESSION['error'] = "User not found.";
        header("Location: ../index.php");
        exit();
    }

    mysqli_close($conn);
} else {
    header("Location: ../index.php");
    exit();
}
?>