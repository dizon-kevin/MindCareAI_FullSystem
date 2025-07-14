<?php
$conn = new mysqli("localhost", "root", "", "mindcare_full");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $email = $_POST['email'];
  $password = $_POST['password'];

  $stmt = $conn->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
  $stmt->bind_param("ss", $email, $password);
  $stmt->execute();
  header("Location: login.php");
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Register | MindCare AI</title>
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
      height: 100vh;
      background: linear-gradient(to right, #667eea, #764ba2);
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .register-box {
      background: #fff;
      padding: 40px;
      border-radius: 12px;
      width: 100%;
      max-width: 400px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
    }

    .register-box h2 {
      text-align: center;
      margin-bottom: 20px;
      color: #333;
    }

    .input-group {
      margin-bottom: 20px;
    }

    .input-group label {
      display: block;
      font-size: 14px;
      margin-bottom: 6px;
      color: #555;
    }

    .input-group input {
      width: 100%;
      padding: 12px;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 16px;
      transition: 0.2s;
    }

    .input-group input:focus {
      border-color: #667eea;
      outline: none;
    }

    .register-btn {
      width: 100%;
      padding: 12px;
      background-color: #667eea;
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 16px;
      cursor: pointer;
      transition: background 0.3s;
    }

    .register-btn:hover {
      background-color: #5563db;
    }

    .login-link {
      text-align: center;
      margin-top: 15px;
      font-size: 14px;
    }

    .login-link a {
      color: #667eea;
      text-decoration: none;
    }
  </style>
</head>
<body>
  <div class="register-box">
    <h2>Create an Account</h2>
    <form method="POST">
      <div class="input-group">
        <label for="email">Email</label>
        <input type="email" name="email" required />
      </div>

      <div class="input-group">
        <label for="password">Password</label>
        <input type="password" name="password" required />
      </div>

      <button type="submit" class="register-btn">Register</button>
    </form>
    <div class="login-link">
      Already have an account? <a href="login.php">Login here</a>
    </div>
  </div>
</body>
</html>
