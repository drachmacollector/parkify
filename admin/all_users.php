<?php
$mysqli = new mysqli("sql105.infinityfree.com", "if0_39017725", "jeZyqYSlUAhhmM", "if0_39017725_parkify_db");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$result = $mysqli->query("SELECT * FROM user_form");
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>All Users</title>
  <style>
    * {
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      margin: 0;
      padding: 0;
      background: linear-gradient(135deg, #0f0c29, #302b63, #24243e);
      color: white;
      overflow-x: hidden;
    }

    #particles {
      position: fixed;
      top: 0;
      left: 0;
      z-index: -1;
      width: 100%;
      height: 100%;
      pointer-events: none;
    }

    header {
      text-align: center;
      font-size: 28px;
      font-weight: bold;
      padding: 20px 0;
      background: rgba(0, 0, 0, 0.3);
      backdrop-filter: blur(0px);
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .container {
      max-width: 95%;
      margin: 30px auto;
      background: rgba(255, 255, 255, 0.05);
      border-radius: 16px;
      padding: 25px;
      backdrop-filter: blur(3px);
      box-shadow: 0 4px 30px rgba(0, 0, 0, 0.4);
      border: 1px solid rgba(255, 255, 255, 0.1);
    }

    table {
      width: 100%;
      border-collapse: collapse;
      border-radius: 10px;
      overflow: hidden;
    }

    th, td {
      padding: 14px 12px;
      text-align: left;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
      color: #f0f0f0;
    }

    th {
      background: rgba(0, 255, 255, 0.1);
      font-weight: 600;
    }

    tr:hover {
      background-color: rgba(255, 255, 255, 0.05);
    }

    .edit-btn {
      background: rgba(0, 255, 255, 0.1);
      color: cyan;
      padding: 8px 14px;
      border-radius: 8px;
      text-decoration: none;
      transition: all 0.2s ease;
      border: 1px solid cyan;
    }

    .edit-btn:hover {
      background: rgba(0, 255, 255, 0.2);
      box-shadow: 0 0 10px cyan;
    }

    .back-btn {
      display: inline-block;
      margin: 16px auto;
      margin-bottom: 7px;
      padding: 10px 22px;
      font-size: 16px;
      background-color: rgba(0, 255, 255, 0.1);
      color: cyan;
      border: 1px solid cyan;
      border-radius: 8px;
      cursor: pointer;
      transition: 0.3s ease;
      text-decoration: none;
      text-align: center;
    }

    .back-btn:hover {
      background-color: rgba(0, 255, 255, 0.2);
      box-shadow: 0 0 10px cyan;
    }
  </style>
</head>
<body>
<canvas id="particles"></canvas>

<header>All Registered Users</header>

<div style="text-align: center;">
  <a href="admin_panel.php" class="back-btn">← Back to Admin Panel</a>
</div>

<div class="container">
  <table>
    <tr>
      <th>ID</th>
      <th>Username</th>
      <th>Email</th>
      <th>Phone</th>
      <th>State</th>
      <th>City</th>
      <th>Address 1</th>
      <th>Address 2</th>
      <th>Car Number</th>
      <th>Created Date</th>
      <th>Edit</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()) { ?>
      <tr>
        <td><?= $row['id']; ?></td>
        <td><?= htmlspecialchars($row['username']); ?></td>
        <td><?= htmlspecialchars($row['email']); ?></td>
        <td><?= htmlspecialchars($row['phoneNo']); ?></td>
        <td><?= htmlspecialchars($row['state']); ?></td>
        <td><?= htmlspecialchars($row['city']); ?></td>
        <td><?= htmlspecialchars($row['address1']); ?></td>
        <td><?= htmlspecialchars($row['address2']); ?></td>
        <td><?= htmlspecialchars($row['carNumber']); ?></td>
        <td><?= htmlspecialchars($row['created_at']); ?></td>
        <td><a href="edit_user.php?id=<?= $row['id']; ?>" class="edit-btn">Edit</a></td>
      </tr>
    <?php } ?>
  </table>
</div>

<script src="particles.js"></script>

</body>
</html>
