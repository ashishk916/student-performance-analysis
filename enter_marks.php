<?php
session_start();
if (!isset($_SESSION['teacher'])) {
    header("Location: teacher_login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "12345678", "student_system");

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Initialize variables
$student_name_error = '';
$student_id = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get data from form
    $student_name = $_POST['student_name'];
    $subject = $_POST['subject'];
    $assessment_type = $_POST['assessment_type'];
    $marks = $_POST['marks'];

    // Fetch student ID based on the entered student name
    $stmt = $conn->prepare("SELECT id FROM students WHERE name = ?");
    $stmt->bind_param("s", $student_name);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $student = $result->fetch_assoc();
        $student_id = $student['id'];

        // You can now insert marks for the student (this part is an example)
        // Just demonstrate here how to handle student ID after fetch
        echo "Marks for student ID: " . $student_id; // Debug, remove in production
    } else {
        $student_name_error = 'Student not found!';
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Enter Marks</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
  <h2 class="mb-4">Enter Student Marks</h2>

  <form action="" method="post">
    <div class="mb-3">
      <label class="form-label">Student Name</label>
      <input type="text" name="student_name" class="form-control" required>
      <?php if ($student_name_error): ?>
        <div class="text-danger"><?php echo $student_name_error; ?></div>
      <?php endif; ?>
    </div>

    <div class="mb-3">
      <label class="form-label">Subject</label>
      <input type="text" name="subject" class="form-control" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Assessment Type</label>
      <select name="assessment_type" class="form-control" required>
        <option value="">-- Select Type --</option>
        <option value="Quiz">Quiz</option>
        <option value="Assignment">Assignment</option>
        <option value="Midterm">Midterm</option>
        <option value="Final">Final</option>
      </select>
    </div>

    <div class="mb-3">
      <label class="form-label">Marks (0 - 100)</label>
      <input type="number" name="marks" min="0" max="100" class="form-control" required>
    </div>

    <button type="submit" class="btn btn-success">Submit Marks</button>
    <div class="container mt-3 text-end">
      <a href="teacher_logout.php" class="btn btn-danger">Logout</a>
    </div>
  </form>
</div>
</body>
</html>
