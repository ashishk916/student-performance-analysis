<?php
session_start();

$conn = new mysqli("localhost", "root", "12345678", "student_system");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if ($email && $password) {
    $stmt = $conn->prepare("SELECT id, name, password FROM students WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $student = $result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $student['password'])) {
            // Store the student ID in session
            $_SESSION['student'] = $student['id'];
            header("Location: student_dashboard.php"); // Redirect to the student's dashboard
            exit();
        } else {
            // If password is invalid
            echo "<div class='alert alert-danger'>Invalid password. <a href='student_login.php'>Try again</a></div>";
        }
    } else {
        // If email does not exist
        echo "<div class='alert alert-warning'>Student not found. <a href='student_login.php'>Try again</a></div>";
    }

    $stmt->close();
} else {
    // If either email or password is empty
    echo "<div class='alert alert-warning'>Please enter both email and password. <a href='student_login.php'>Try again</a></div>";
}

$conn->close();
?>
