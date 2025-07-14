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
<head><title>Chat Logs</title></head>
<body>
<h2>All Chat Logs</h2>
<table border="1" cellpadding="5">
  <tr><th>User</th><th>Role</th><th>Message</th><th>Date</th></tr>
  <?php
  $logs = $conn->query("SELECT c.*, u.email FROM chat_logs c LEFT JOIN users u ON c.user_id = u.id ORDER BY c.created_at DESC");
  while ($row = $logs->fetch_assoc()) {
    $email = $row['email'] ?? 'Guest';
    echo "<tr><td>{$email}</td><td>{$row['role']}</td><td>{$row['message']}</td><td>{$row['created_at']}</td></tr>";
  }
  ?>
</table>
<a href="index.php">← Back to Admin</a>
</body>
</html>
