<?php
$conn = new mysqli("sql105.infinityfree.com", "if0_39017725", "jeZyqYSlUAhhmM", "if0_39017725_parkify_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id = $_GET['id'];
$result = $conn->query("SELECT * FROM user_form WHERE id = $id");
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
  <title>Confirm Your Details</title>
  <style>
    body {
      margin: 0;
      padding: 0;
      background-color:black;
      background-size: cover;
      font-family: 'Segoe UI', sans-serif;
      color: #fff;
    }

    .container {
      max-width: 600px;
      margin: 100px auto;
      background: rgba(255, 255, 255, 0.08);
      backdrop-filter: blur(12px);
      border-radius: 20px;
      padding: 30px;
      box-shadow: 0 0 25px rgba(0, 255, 255, 0.2);
      border: 1px solid rgba(255, 255, 255, 0.2);
    }

    h2 {
      text-align: center;
      margin-bottom: 30px;
      font-size: 28px;
      text-shadow: 0 0 10px #00ffffaa;
    }

    ul {
      list-style: none;
      padding: 0;
      margin-bottom: 30px;
    }

    li {
      margin: 12px 0;
      font-size: 18px;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
      padding-bottom: 8px;
    }

    strong {
      color: #00ffe7;
    }

    button {
      background: #00ffe7;
      color: #000;
      padding: 10px 20px;
      border: none;
      margin: 10px 10px 0 0;
      border-radius: 8px;
      cursor: pointer;
      transition: 0.3s ease;
      box-shadow: 0 0 10px #00ffe7a0;
    }

    button:hover {
      background: #00ffff;
      box-shadow: 0 0 20px #00ffe7;
    }

    .edit {
      display: flex;
      justify-content: center;
      gap: 20px;
    }

    .password-box {
      display: none;
      margin-top: 30px;
    }

    .password-box input[type="password"] {
      padding: 10px;
      width: 100%;
      margin-bottom: 15px;
      border: none;
      border-radius: 8px;
      outline: none;
      background: rgba(255, 255, 255, 0.1);
      color: white;
      font-size: 16px;
    }

    .password-box button {
      width: 100%;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Hello, <?= $user['firstName'] ?> 👋<br>Confirm Your Details</h2>
    <ul>
      <li><strong>Username:</strong> <?= $user['username'] ?></li>
      <li><strong>Email:</strong> <?= $user['email'] ?></li>
      <li><strong>Phone:</strong> <?= $user['phoneNo'] ?></li>
      <li><strong>City:</strong> <?= $user['city'] ?></li>
    </ul>

    <div class="edit">
    <button onclick="window.location.href='../userName/userName.php'">✏️ Edit</button>
    <button onclick="document.getElementById('passwordBox').style.display='block'">✅ Confirm</button>
    </div>

    <div class="password-box" id="passwordBox">
    <form action="save_password.php" method="POST">
  <input type="hidden" name="id" value="<?= $user['id'] ?>">
  <input type="password" name="password" placeholder="Enter Password" required>
  <button type="submit">Save Password</button>
</form>

    </div>
    
  </div>
</body>
</html>
