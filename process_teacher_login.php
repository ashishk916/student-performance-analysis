<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

$conn = new mysqli("localhost", "root", "12345678", "student_system");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if ($email && $password) {
    $stmt = $conn->prepare("SELECT id, name, password FROM teachers WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $teacher = $result->fetch_assoc();

        if (password_verify($password, $teacher['password'])) {
            $_SESSION['teacher'] = $teacher['id'];
            header("Location: enter_marks.php");
            exit();
        } else {
            echo "<div class='container mt-4 alert alert-danger'>Invalid password. <a href='teacher_login.php'>Try again</a></div>";
        }
    } else {
        echo "<div class='container mt-4 alert alert-warning'>Teacher not found. <a href='teacher_login.php'>Try again</a></div>";
    }

    $stmt->close();
} else {
    echo "<div class='container mt-4 alert alert-warning'>Please enter both email and password. <a href='teacher_login.php'>Try again</a></div>";
}

$conn->close();
