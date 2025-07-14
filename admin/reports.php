<?php
session_start();
$conn = new mysqli("localhost", "root", "", "mindcare_full");

if (!isset($_SESSION['user']) || $_SESSION['user'] !== 'admin@mindcare.com') {
  header("Location: ../auth/login.php");
  exit;
}
?>
<!DOCTYPE html>
<html>
<head><title>Reports</title></head>
<body>
<h2>All User Mood Logs</h2>
<table border="1" cellpadding="5">
  <tr><th>User</th><th>Mood</th><th>Date</th></tr>
  <?php
  $q1 = $conn->query("SELECT u.email, m.mood, m.created_at FROM moods m JOIN users u ON m.user_id = u.id ORDER BY m.created_at DESC");
  while ($r = $q1->fetch_assoc()) {
    echo "<tr><td>{$r['email']}</td><td>{$r['mood']}</td><td>{$r['created_at']}</td></tr>";
  }
  ?>
</table>

<h2>All Journal Entries</h2>
<table border="1" cellpadding="5">
  <tr><th>User</th><th>Entry</th><th>Date</th></tr>
  <?php
  $q2 = $conn->query("SELECT u.email, j.entry, j.created_at FROM journals j JOIN users u ON j.user_id = u.id ORDER BY j.created_at DESC");
  while ($r = $q2->fetch_assoc()) {
    echo "<tr><td>{$r['email']}</td><td>{$r['entry']}</td><td>{$r['created_at']}</td></tr>";
  }
  ?>
</table>
<a href="index.php">← Back to Admin</a>
</body>
</html>
