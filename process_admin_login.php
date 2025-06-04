<?php
session_start();

$admin_username = "admin";
$admin_password = "admin123"; // In real-world, hash this

$user = $_POST['username'];
$pass = $_POST['password'];

if ($user === $admin_username && $pass === $admin_password) {
  $_SESSION['admin'] = $user;
  header("Location: admin_dashboard.php");
  exit();
} else {
  echo "Invalid admin credentials. <a href='admin_login.php'>Try again</a>";
}
?>
