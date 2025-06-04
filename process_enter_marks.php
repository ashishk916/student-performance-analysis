<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

if (!isset($_SESSION['teacher'])) {
    header("Location: teacher_login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "12345678", "student_system");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Collect and sanitize form data
$student_id      = $_POST['student_id'] ?? '';
$subject         = trim($_POST['subject'] ?? '');
$assessment_type = $_POST['assessment_type'] ?? '';
$marks           = $_POST['marks'] ?? '';

if ($student_id && $subject && $assessment_type && is_numeric($marks)) {
    $stmt = $conn->prepare("INSERT INTO marks (student_id, subject, assessment_type, marks) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("issi", $student_id, $subject, $assessment_type, $marks);

    if ($stmt->execute()) {
        echo "<div class='container mt-4 alert alert-success'>Marks entered successfully. <a href='enter_marks.php'>Enter more</a></div>";
    } else {
        echo "<div class='container mt-4 alert alert-danger'>Error: " . $stmt->error . "</div>";
    }

    $stmt->close();
} else {
    echo "<div class='container mt-4 alert alert-warning'>Please fill all fields correctly. <a href='enter_marks.php'>Go back</a></div>";
}

$conn->close();
?>
