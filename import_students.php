<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once 'database/db_connection.php';

//$csvFile = '/data/students_data.csv';
$csvFile = __DIR__ . '/data/students_data.csv';
if (($handle = fopen($csvFile, "r")) !== FALSE) {
    $row = 0;
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        if ($row > 0) { // Skip the header row
            $roll_number = mysqli_real_escape_string($conn, trim($data[0]));
            $name = mysqli_real_escape_string($conn, trim($data[1]));
            $default_password = password_hash('default123', PASSWORD_DEFAULT);

            // Check if user already exists
            $check_user_sql = "SELECT user_id FROM users WHERE username = '$roll_number'";
            $check_user_result = mysqli_query($conn, $check_user_sql);

            if (mysqli_num_rows($check_user_result) == 0) {
                // Insert into users table
                $insert_user_sql = "INSERT INTO users (role, username, password, name)
                                     VALUES ('student', '$roll_number', '$default_password', '$name')";
                if (mysqli_query($conn, $insert_user_sql)) {
                    $user_id = mysqli_insert_id($conn);
                    // Insert into students table
                    $insert_student_sql = "INSERT INTO students (user_id, roll_number, first_login)
                                          VALUES ($user_id, '$roll_number', 1)";
                    if (!mysqli_query($conn, $insert_student_sql)) {
                        echo "Error inserting student data: " . mysqli_error($conn) . "<br>";
                    }
                } else {
                    echo "Error inserting user data: " . mysqli_error($conn) . "<br>";
                }
            } else {
                echo "User with roll number $roll_number already exists.<br>";
            }
        }
        $row++;
    }
    fclose($handle);
    echo "Student data import completed.<br>";
} else {
    echo "Error opening CSV file.<br>";
}

mysqli_close($conn);
?>