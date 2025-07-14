<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user'] !== 'admin@mindcare.com') {
  header("Location: ../auth/login.php");
  exit;
}
?>
<!DOCTYPE html>
<html>
<head><title>Admin Dashboard</title></head>
<body>
<h2>Welcome, Admin</h2>
<ul>
  <li><a href="users.php">View All Users</a></li>
  <li><a href="reports.php">View Mood & Journal Logs</a></li>
  <li><a href="../index.html">Go to Dashboard</a></li>
</ul>
</body>
</html>
