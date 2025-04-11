<!DOCTYPE html>
<html>
<head>
    <title>Enroll Students in Subject</title>
    </head>
<body>
    <h2>Enroll Students in Subject</h2>
    <form action="process_enrollment.php" method="POST">
    <div>
        <label for="subject_id">Select Subject:</label>
        <select name="subject_id" id="subject_id" required>
            <option value="">Select</option>
            <?php
            $subjects_result = mysqli_query($conn, "SELECT subject_id, subject_name FROM subjects ORDER BY subject_name");
            while ($row = mysqli_fetch_assoc($subjects_result)):
                echo '<option value="' . $row['subject_id'] . '">' . $row['subject_name'] . '</option>';
            endwhile;
            ?>
        </select>
    </div>
    <div>
        <h3>Select Students:</h3>
        <?php
        $students_result = mysqli_query($conn, "SELECT s.student_id, u.name FROM students s JOIN users u ON s.user_id = u.user_id ORDER BY u.name");
        while ($row = mysqli_fetch_assoc($students_result)):
            echo '<input type="checkbox" name="student_ids[]" value="' . $row['student_id'] . '"> ' . $row['name'] . '<br>';
        endwhile;
        ?>
    </div>
    <button type="submit" class="btn btn-primary">Enroll Students</button>
    </form>
</body>
</html>