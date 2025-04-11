<?php
session_start();
require_once '../database/db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $employee_id = $_POST['employee_id'];
    $designation = $_POST['designation'];

    // **IMPORTANT SECURITY WARNING:**
    // The following code lacks proper input validation and sanitization.
    // This makes it vulnerable to SQL injection and other security risks.
    // In a real application, you MUST use prepared statements
    // (mysqli_stmt_prepare, mysqli_stmt_bind_param, mysqli_stmt_execute)
    // to prevent SQL injection.

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert into users table
    $sql_users = "INSERT INTO users (role, username, password, name)
                  VALUES ('faculty', '$username', '$hashed_password', '$name')";

    if (mysqli_query($conn, $sql_users)) {
        $user_id = mysqli_insert_id($conn);

        // Insert into faculty table (optional employee_id and designation)
        $sql_faculty = "INSERT INTO faculty (user_id, employee_id, designation)
                        VALUES ($user_id, '" . mysqli_real_escape_string($conn, $employee_id) . "', '" . mysqli_real_escape_string($conn, $designation) . "')";

        if (mysqli_query($conn, $sql_faculty)) {
            $_SESSION['registration_success'] = "Faculty member registered successfully. You can register another.";
        } else {
            $_SESSION['registration_error'] = "Error registering faculty details: " . mysqli_error($conn);
            // Consider deleting the user if faculty details fail
            mysqli_query($conn, "DELETE FROM users WHERE user_id = $user_id");
        }
    } else {
        $_SESSION['registration_error'] = "Error registering user: " . mysqli_error($conn);
    }

    mysqli_close($conn);
    header("Location: faculty_register.php"); // Redirect back to the registration form
    exit();

} else {
    header("Location: faculty_register.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Registration Processing</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container mt-5">
        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Display registration status messages here
        }
        ?>
        <p><a href="faculty_register.php" class="btn btn-secondary">Go Back to Registration</a></p>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>