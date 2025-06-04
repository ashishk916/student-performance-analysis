<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "12345678", "student_system");

// Handle search query for students
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
if (!empty($search)) {
    $students = $conn->query("SELECT * FROM students WHERE name LIKE '%$search%'");
} else {
    $students = $conn->query("SELECT * FROM students");
}

// Fetch all teachers
$teachers = $conn->query("SELECT * FROM teachers");

// Fetch all marks
$marks = $conn->query("SELECT m.id, s.name AS student_name, m.subject, m.assessment_type, m.marks
                       FROM marks m
                       JOIN students s ON m.student_id = s.id
                       ORDER BY m.created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    @media print {
      body * {
        visibility: hidden;
      }
      #marksSection, #marksSection * {
        visibility: visible;
      }
      #marksSection {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
      }
      .no-print {
        display: none;
      }
    }
  </style>
</head>
<body>
<div class="container mt-5">
  <h2 class="mb-4">Welcome, Admin!</h2>

  <div class="mb-4 no-print">
    <a href="index.php" class="btn btn-primary">Home</a>
    <a href="enter_marks.php" class="btn btn-success">Enter Marks</a>
    <a href="logout.php" class="btn btn-danger">Logout</a>
  </div>

  <h4>Registered Students</h4>

  <!-- Search Form -->
  <form method="GET" class="mb-3 no-print">
    <div class="input-group">
      <input type="text" name="search" class="form-control" placeholder="Search student by name" value="<?= htmlspecialchars($search) ?>">
      <button class="btn btn-outline-secondary" type="submit">Search</button>
      <?php if (!empty($search)): ?>
        <a href="admin_dashboard.php" class="btn btn-outline-danger">Clear</a>
      <?php endif; ?>
    </div>
  </form>

  <table class="table table-bordered mb-5">
    <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Class</th></tr></thead>
    <tbody>
    <?php if ($students->num_rows > 0): ?>
      <?php while ($row = $students->fetch_assoc()): ?>
        <tr>
          <td><?= $row['id'] ?></td>
          <td><?= htmlspecialchars($row['name']) ?></td>
          <td><?= htmlspecialchars($row['email']) ?></td>
          <td><?= htmlspecialchars($row['class']) ?></td>
        </tr>
      <?php endwhile; ?>
    <?php else: ?>
      <tr><td colspan="4" class="text-center">No students found.</td></tr>
    <?php endif; ?>
    </tbody>
  </table>

  <h4>Registered Teachers</h4>
  <table class="table table-bordered mb-5">
    <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Subject</th></tr></thead>
    <tbody>
    <?php while ($row = $teachers->fetch_assoc()): ?>
      <tr>
        <td><?= $row['id'] ?></td>
        <td><?= htmlspecialchars($row['name']) ?></td>
        <td><?= htmlspecialchars($row['email']) ?></td>
        <td><?= htmlspecialchars($row['subject']) ?></td>
      </tr>
    <?php endwhile; ?>
    </tbody>
  </table>

  <div id="marksSection">
    <h4>Recent Marks</h4>
    <table class="table table-bordered">
      <thead><tr><th>ID</th><th>Student</th><th>Subject</th><th>Assessment</th><th>Marks</th></tr></thead>
      <tbody>
      <?php
      $marks->data_seek(0);
      while ($row = $marks->fetch_assoc()): ?>
        <tr>
          <td><?= $row['id'] ?></td>
          <td><?= htmlspecialchars($row['student_name']) ?></td>
          <td><?= htmlspecialchars($row['subject']) ?></td>
          <td><?= htmlspecialchars($row['assessment_type']) ?></td>
          <td><?= $row['marks'] ?></td>
        </tr>
      <?php endwhile; ?>
      </tbody>
    </table>
  </div>

  <div class="mt-3 no-print">
    <button class="btn btn-outline-primary" onclick="window.print()">🖨️ Print Marks</button>
  </div>
</div>
</body>
</html>
