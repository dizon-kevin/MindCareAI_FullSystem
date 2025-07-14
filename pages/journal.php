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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['entry'])) {
  $entry = $conn->real_escape_string($_POST['entry']);
  $conn->query("INSERT INTO journals (user_id, entry) VALUES ($user_id, '$entry')");
}
?>
<!DOCTYPE html>
<html>
<head><title>My Journal</title></head>
<body>
<h2>My Journal</h2>
<form method="POST">
  <textarea name="entry" rows="4" cols="50" placeholder="Write your thoughts..." required></textarea><br><br>
  <button type="submit">Save Entry</button>
</form>

<h3>Previous Entries</h3>
<ul>
<?php
$result = $conn->query("SELECT entry, created_at FROM journals WHERE user_id = $user_id ORDER BY created_at DESC");
while ($row = $result->fetch_assoc()) {
  echo "<li><b>{$row['created_at']}</b>: {$row['entry']}</li><br>";
}
?>
</ul>
<a href="../index.html">← Back to Dashboard</a>
</body>
</html>
