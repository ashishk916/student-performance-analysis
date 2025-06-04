<?php
$conn = new mysqli("localhost", "root", "12345678", "student_system");

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$role     = $_POST['role'];
$name     = $_POST['name'];
$email    = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_BCRYPT);

if ($role == "student") {
    $class = $_POST['class'];

    $sql = "INSERT INTO students (name, email, password, class)
            VALUES ('$name', '$email', '$password', '$class')";

} elseif ($role == "teacher") {
    $subject = $_POST['subject'];

    $sql = "INSERT INTO teachers (name, email, password, subject)
            VALUES ('$name', '$email', '$password', '$subject')";
} else {
    die("Invalid role.");
}

if ($conn->query($sql) === TRUE) {
    echo ucfirst($role) . " registered successfully! <a href='index.php'>Go Home</a>";
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>
