<?php
session_start();
$conn = new mysqli("localhost", "root", "", "mindcare_full");

if (!isset($_SESSION['user'])) {
  header("Location: ../auth/login.php");
  exit;
}

$email = $_SESSION['user'];
$user = $conn->query("SELECT id FROM users WHERE email = '$email'")->fetch_assoc();
$user_id = $user['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $f = $_POST['functionality'];
  $u = $_POST['usability'];
  $r = $_POST['reliability'];
  $e = $_POST['efficiency'];
  $s = $_POST['security'];
  $conn->query("INSERT INTO quality_scores (user_id, functionality, usability, reliability, efficiency, security)
    VALUES ($user_id, $f, $u, $r, $e, $s)");
  echo "<p style='color:green;'>Thank you for your feedback!</p>";
}
?>
<!DOCTYPE html>
<html>
<head><title>System Evaluation</title></head>
<body>
<h2>System Feedback (ISO 25010)</h2>
<form method="POST">
  <label>Functionality (1–5):</label><input type="number" name="functionality" min="1" max="5" required><br>
  <label>Usability (1–5):</label><input type="number" name="usability" min="1" max="5" required><br>
  <label>Reliability (1–5):</label><input type="number" name="reliability" min="1" max="5" required><br>
  <label>Efficiency (1–5):</label><input type="number" name="efficiency" min="1" max="5" required><br>
  <label>Security (1–5):</label><input type="number" name="security" min="1" max="5" required><br><br>
  <button type="submit">Submit Evaluation</button>
</form>
<a href="../index.html">← Back to Dashboard</a>
</body>
</html>
