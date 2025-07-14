<?php
session_start();
$conn = new mysqli("localhost", "root", "", "mindcare_full");

if (!isset($_SESSION['user']) || $_SESSION['user'] !== 'admin@mindcare.com') {
  header("Location: ../auth/login.php");
  exit;
}

$result = $conn->query("SELECT id, email, role FROM users");
?>
<!DOCTYPE html>
<html>
<head><title>All Users</title></head>
<body>
<h2>Registered Users</h2>
<table border="1" cellpadding="5">
  <tr><th>ID</th><th>Email</th><th>Role</th></tr>
  <?php while ($row = $result->fetch_assoc()) {
    echo "<tr><td>{$row['id']}</td><td>{$row['email']}</td><td>{$row['role']}</td></tr>";
  } ?>
</table>
<a href="index.php">← Back to Admin</a>
</body>
</html>
