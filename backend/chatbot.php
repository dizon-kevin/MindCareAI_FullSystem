<?php
session_start();
header("Content-Type: application/json");

$conn = new mysqli("localhost", "root", "", "mindcare");
$input = json_decode(file_get_contents("php://input"), true);
$messages = $input['messages'] ?? [];

// Limit for guest users (max 5 messages)
if (!isset($_SESSION['user']) && count($messages) > 6) {
  echo json_encode([
    "choices" => [
      ["message" => ["content" => "You've reached your 5-message limit. Please log in to continue."]]
    ]
  ]);
  exit;
}

$api_key = "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";

// Get user_id if logged in
$user_id = null;
if (isset($_SESSION['user'])) {
  $email = $_SESSION['user'];
  $result = $conn->query("SELECT id FROM users WHERE email = '$email'");
  if ($result && $row = $result->fetch_assoc()) {
    $user_id = $row['id'];
  }
}

// Get latest user message for logging
$last_msg = end($messages);
if ($last_msg && $last_msg['role'] === 'user') {
  $msg = $conn->real_escape_string($last_msg['content']);
  $uid = $user_id ? $user_id : "NULL";
  $conn->query("INSERT INTO chat_logs (user_id, message, role) VALUES ($uid, '$msg', 'user')");
}

// Call API
$ch = curl_init("https://openrouter.ai/api/v1/chat/completions");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
  "Authorization: Bearer $api_key",
  "Content-Type: application/json",
  "HTTP-Referer: http://localhost",
  "X-Title: MindCare AI"
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
  "model" => "deepseek/deepseek-chat:free",
  "messages" => $messages
]));

$response = curl_exec($ch);
if (curl_errno($ch)) {
  echo json_encode(["error" => curl_error($ch)]);
  exit;
}
curl_close($ch);

// Parse and log bot reply
$data = json_decode($response, true);
$bot_reply = $data['choices'][0]['message']['content'] ?? null;

if ($bot_reply) {
  $reply = $conn->real_escape_string($bot_reply);
  $uid = $user_id ? $user_id : "NULL";
  $conn->query("INSERT INTO chat_logs (user_id, message, role) VALUES ($uid, '$reply', 'bot')");
}

echo $response;
?>
