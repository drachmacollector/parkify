<?php
$host = "sql105.infinityfree.com";
$user = "if0_39017725";
$pass = "your_actual_vpanel_password"; // Use your InfinityFree account password
$db   = "if0_39017725_parkify_db";


$conn = new mysqli("sql105.infinityfree.com", "if0_39017725", "jeZyqYSlUAhhmM", "if0_39017725_parkify_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$sql = "SELECT * FROM user_passwords";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
  <title>All User Passwords</title>
  <style>
    body {
      margin: 0;
      padding: 0;
      font-family: 'Segoe UI', sans-serif;
      background: linear-gradient(90deg, #d4d4d400, #140214d9, #041018d4, #d4d4d400), 
                  url(bg.jpg) no-repeat center center fixed;
      background-size: cover;
      color: #fff;
    }

    h2 {
      text-align: center;
      margin-top: 40px;
      font-size: 2.5rem;
      color: #ffffff;
      text-shadow: 0 0 10px #00ffff;
    }

    table {
      width: 85%;
      margin: 30px auto;
      border-collapse: collapse;
      background: rgba(255, 255, 255, 0.05);
      backdrop-filter: blur(15px);
      box-shadow: 0 0 30px rgba(0, 255, 255, 0.2);
      border-radius: 20px;
      overflow: hidden;
    }

    th, td {
      padding: 15px;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
      text-align: center;
      color: #e0f7fa;
    }

    th {
      background-color: rgba(0, 76, 255, 0.1);
      font-weight: bold;
      font-size: 1.1rem;
    }

    tr:hover {
      background-color: rgba(255, 255, 255, 0.08);
    }

    @media (max-width: 768px) {
      table {
        width: 95%;
        font-size: 0.85rem;
      }

      h2 {
        font-size: 1.8rem;
      }
    }
  </style>
</head>
<body>
  <h2>All Stored User Passwords (hashed)</h2>
  <table>
    <tr>
      <th>ID</th>
      <th>User ID</th>
      <th>Password (Hashed)</th>
      <th>Created At</th>
    </tr>

    <?php while($row = $result->fetch_assoc()): ?>
      <tr>
        <td><?= $row['id'] ?></td>
        <td><?= $row['user_id'] ?></td>
        <td><?= $row['password'] ?></td>
        <td><?= $row['created_at'] ?></td>
      </tr>
    <?php endwhile; ?>
  </table>
</body>
</html>
