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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mood'])) {
  $mood = $conn->real_escape_string($_POST['mood']);
  $conn->query("INSERT INTO moods (user_id, mood) VALUES ($user_id, '$mood')");
}
?>
<!DOCTYPE html>
<html>
<head><title>Mood Tracker</title></head>
<body>
<h2>How are you feeling today?</h2>
<form method="POST">
  <button name="mood" value="😊">😊 Happy</button>
  <button name="mood" value="😐">😐 Neutral</button>
  <button name="mood" value="😢">😢 Sad</button>
  <button name="mood" value="😠">😠 Angry</button>
  <button name="mood" value="😟">😟 Anxious</button>
</form>

<h3>Your Mood History</h3>
<ul>
<?php
$result = $conn->query("SELECT mood, created_at FROM moods WHERE user_id = $user_id ORDER BY created_at DESC LIMIT 10");
while ($row = $result->fetch_assoc()) {
  echo "<li><b>{$row['created_at']}</b>: {$row['mood']}</li>";
}
?>
</ul>
<a href="../index.html">← Back to Dashboard</a>
</body>
</html>
