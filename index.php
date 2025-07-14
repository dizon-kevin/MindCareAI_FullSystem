<?php
session_start();
$conn = new mysqli("localhost", "root", "", "mindcare_full");

if (!isset($_SESSION['user'])) {
  header("Location: auth/login.php");
  exit;
}

$user = $_SESSION['user'];
$name = ucfirst(explode('@', $user)[0]);

// Get user ID
$userId = $conn->query("SELECT id FROM users WHERE email = '$user'")->fetch_assoc()['id'];

// Journal preview (last 3 entries)
$journal = $conn->query("SELECT * FROM journals WHERE user_id = $userId ORDER BY created_at DESC LIMIT 3");

// Mood chart data (last 7)
$moodData = $conn->query("SELECT mood, created_at FROM moods WHERE user_id = $userId ORDER BY created_at DESC LIMIT 7");
$labels = [];
$data = [];
while ($row = $moodData->fetch_assoc()) {
  $labels[] = date("M d", strtotime($row['created_at']));
  $data[] = $row['mood'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>MindCare AI - Dashboard</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    :root {
      --primary: #333;
      --accent: #eef1f5;
      --text-dark: #2d2d2d;
      --text-light: #666;
      --bg: #f7f8fa;
      --white: #fff;
      --radius: 12px;
    }
    * {
      margin: 0; padding: 0;
      box-sizing: border-box;
      font-family: 'Inter', sans-serif;
    }
    body {
      display: flex;
      background: var(--bg);
      color: var(--text-dark);
      min-height: 100vh;
    }
    .sidebar {
      width: 250px;
      background: var(--white);
      padding: 24px;
      border-right: 1px solid #e0e0e0;
    }
    .sidebar h2 { margin-bottom: 30px; font-size: 22px; }
    .nav a {
      display: block;
      text-decoration: none;
      color: var(--text-dark);
      padding: 12px 16px;
      border-radius: var(--radius);
      margin-bottom: 10px;
    }
    .nav a:hover, .nav a.active {
      background: var(--accent);
      font-weight: 600;
    }
    .main { flex: 1; padding: 32px; }
    .topbar {
      display: flex;
      justify-content: space-between;
      margin-bottom: 30px;
    }
    .greeting h3 { font-size: 22px; }
    .user-profile img {
      width: 40px; height: 40px;
      border-radius: 50%;
    }
    .grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
      gap: 24px;
      margin-bottom: 32px;
    }
    .card {
      background: var(--white);
      padding: 20px;
      border-radius: var(--radius);
      box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    .btn {
      background: var(--primary);
      color: #fff;
      padding: 10px 16px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
    }
    .user-msg, .bot-msg {
      margin: 10px 0;
      padding: 10px 14px;
      border-radius: 12px;
      max-width: 75%;
    }
    .user-msg { background: #667eea; color: white; margin-left: auto; }
    .bot-msg { background: #e7e7e7; color: #222; margin-right: auto; }
    #chatbox {
      border: 1px solid #ccc;
      border-radius: 8px;
      background: white;
      padding: 12px;
      height: 240px;
      overflow-y: auto;
    }
    #user-input {
      width: calc(100% - 110px);
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 6px;
    }
    #send-btn {
      padding: 10px 16px;
      margin-left: 10px;
      background-color: var(--primary);
      color: #fff;
      border-radius: 6px;
      border: none;
    }
    #camera-box video {
      width: 100%; border-radius: 8px;
    }
  </style>
</head>
<body>

  <!-- Sidebar -->
  <div class="sidebar">
    <h2>MindCare AI</h2>
    <div class="nav">
      <a href="#" class="active">Dashboard</a>
      <a href="pages/journal.php">Journal</a>
      <a href="pages/mood_tracker.php">Mood Tracker</a>
      <a href="#chat-section">AI Chatbot</a>
      <a href="auth/logout.php">Logout</a>
    </div>
  </div>

  <!-- Main -->
  <div class="main">
    <div class="topbar">
      <div class="greeting">
        <h3>Welcome, <?= $name ?>!</h3>
        <p>Your mental health matters.</p>
      </div>
      <div class="user-profile">
        <img src="https://i.ibb.co/60t3mKK/user-profile.jpg" />
      </div>
    </div>

    <!-- Journal and Mood -->
    <div class="grid">
      <!-- Camera Mood Capture -->
      <div class="card">
        <h4>📸 Scan Your Mood</h4>
        <video id="video" autoplay muted></video>
        <button class="btn" onclick="capturePhoto()">Scan Mood</button>
        <p id="mood-status">Waiting for scan...</p>
      </div>

      <!-- Journal Preview -->
      <div class="card">
        <h4>Recent Journal</h4>
        <ul>
          <?php while($j = $journal->fetch_assoc()): ?>
            <li><?= date("M d", strtotime($j['created_at'])) ?> – <?= htmlspecialchars(substr($j['entry'],0,40)) ?>...</li>
          <?php endwhile; ?>
        </ul>
        <a href="pages/journal.php" style="font-size:13px;">View All</a>
      </div>

      <!-- Mood Chart -->
      <div class="card">
        <h4>Weekly Mood Chart</h4>
        <canvas id="moodChart" height="180"></canvas>
      </div>
    </div>

    <!-- Chatbot -->
    <div class="card" id="chat-section">
      <h4>AI Chatbot (Mental Health)</h4>
      <label>Mode:</label>
      <select onchange="sessionStorage.setItem('chatMode', this.value)">
        <option value="basic">Basic</option>
        <option value="professional">Professional</option>
      </select>
      <div id="chatbox"></div>
      <div style="margin-top:10px;">
        <input type="text" id="user-input" placeholder="Ask your assistant..." />
        <button id="send-btn" onclick="sendMessage()">Send</button>
      </div>
    </div>
  </div>

  <!-- JS -->
  <script>
    let messages = sessionStorage.getItem("chatMessages")
      ? JSON.parse(sessionStorage.getItem("chatMessages"))
      : [{ role: "system", content: "You are a helpful mental health assistant." }];
    let mode = sessionStorage.getItem("chatMode") || "basic";

    function sendMessage() {
      const input = document.getElementById("user-input");
      const text = input.value.trim();
      if (!text) return;

      const chatbox = document.getElementById("chatbox");
      chatbox.innerHTML += `<div class='user-msg'><b>You:</b> ${text}</div>`;
      messages.push({ role: "user", content: text });
      sessionStorage.setItem("chatMessages", JSON.stringify(messages));
      input.value = "";

      fetch("backend/chatbot.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ messages, mode })
      })
      .then(res => res.json())
      .then(data => {
        const reply = data.choices[0].message.content;
        messages.push({ role: "assistant", content: reply });
        sessionStorage.setItem("chatMessages", JSON.stringify(messages));
        chatbox.innerHTML += `<div class='bot-msg'><b>Bot:</b> ${reply}</div>`;
        chatbox.scrollTop = chatbox.scrollHeight;
      });
    }

    // On load: restore chat history
    window.addEventListener("load", () => {
      const chatbox = document.getElementById("chatbox");
      messages.forEach(msg => {
        const role = msg.role === "user" ? "user-msg" : "bot-msg";
        const label = msg.role === "user" ? "You" : "Bot";
        chatbox.innerHTML += `<div class="${role}"><b>${label}:</b> ${msg.content}</div>`;
      });

      // Start camera
      const video = document.getElementById("video");
      if (navigator.mediaDevices) {
        navigator.mediaDevices.getUserMedia({ video: true })
          .then(stream => { video.srcObject = stream; });
      }
    });

    function capturePhoto() {
      document.getElementById("mood-status").innerText = "Your mood is being analyzed... (placeholder)";
    }

    // Chart
    new Chart(document.getElementById('moodChart'), {
      type: 'bar',
      data: {
        labels: <?= json_encode(array_reverse($labels)) ?>,
        datasets: [{
          label: 'Mood',
          data: <?= json_encode(array_reverse($data)) ?>,
          backgroundColor: '#667eea'
        }]
      },
      options: {
        scales: {
          y: { beginAtZero: true }
        }
      }
    });
  </script>
</body>
</html>
