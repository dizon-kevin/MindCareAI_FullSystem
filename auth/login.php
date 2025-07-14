<?php
session_start();
$conn = new mysqli("localhost", "root", "", "mindcare_full");

if (isset($_SESSION['user'])) {
  header("Location: ../index.html");
  exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $email = $_POST['email'];
  $password = $_POST['password'];

  $query = $conn->prepare("SELECT * FROM users WHERE email = ? AND password = ?");
  $query->bind_param("ss", $email, $password);
  $query->execute();
  $result = $query->get_result();

  if ($result->num_rows > 0) {
    $_SESSION['user'] = $email;
    header("Location: ../index.html");
    exit;
  } else {
    $error = "Invalid email or password.";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login | MindCare AI</title>
  <style>
    * {
      box-sizing: border-box;
      margin: 0; padding: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
      height: 100vh;
      background: linear-gradient(to right, #667eea, #764ba2);
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .login-box {
      background: #fff;
      padding: 40px;
      border-radius: 12px;
      width: 100%;
      max-width: 400px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
    }

    .login-box h2 {
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

    .login-btn {
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

    .login-btn:hover {
      background-color: #5563db;
    }

    .register-link {
      text-align: center;
      margin-top: 15px;
      font-size: 14px;
    }

    .register-link a {
      color: #667eea;
      text-decoration: none;
    }

    .error {
      color: red;
      font-size: 14px;
      margin-bottom: 15px;
      text-align: center;
    }
  </style>
</head>
<body>
  <div class="login-box">
    <h2>MindCare AI Login</h2>
    <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
    <form method="POST">
      <div class="input-group">
        <label for="email">Email</label>
        <input type="email" name="email" required />
      </div>

      <div class="input-group">
        <label for="password">Password</label>
        <input type="password" name="password" required />
      </div>

      <button type="submit" class="login-btn">Login</button>
    </form>
    <div class="register-link">
      Don’t have an account? <a href="register.php">Register here</a>
    </div>
  </div>
</body>
</html>
