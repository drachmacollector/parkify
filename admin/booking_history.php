<?php
$mysqli = new mysqli("sql105.infinityfree.com", "if0_39017725", "jeZyqYSlUAhhmM", "if0_39017725_parkify_db");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Fetch unique areas
$areas = [];
$area_query = $mysqli->query("SELECT DISTINCT area FROM parkingspots");
while ($row = $area_query->fetch_assoc()) {
    $areas[] = $row['area'];
}

// Fetch unique usernames
$usernames = [];
$user_query = $mysqli->query("SELECT DISTINCT user_name FROM booking_history");
while ($row = $user_query->fetch_assoc()) {
    $usernames[] = $row['user_name'];
}

// Get selected filters
$selected_area = isset($_GET['area']) ? $_GET['area'] : '';
$selected_user = isset($_GET['user_name']) ? $_GET['user_name'] : '';

// Dynamic query
$query = "SELECT * FROM booking_history WHERE 1=1";
$params = [];
$types = "";

if (!empty($selected_area)) {
    $query .= " AND area = ?";
    $params[] = $selected_area;
    $types .= "s";
}

if (!empty($selected_user)) {
    $query .= " AND user_name = ?";
    $params[] = $selected_user;
    $types .= "s";
}

$stmt = $mysqli->prepare($query);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Booking History</title>
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
    max-width: 60%;
    margin: 30px auto;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 16px;
    padding: 25px;
    backdrop-filter: blur(3px);
    box-shadow: 0 4px 30px rgba(0, 0, 0, 0.4);
    border: 1px solid rgba(255, 255, 255, 0.1);
  }

  form {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    margin-bottom: 20px;
    align-items: center;
  }

  select, button {
    padding: 10px;
    font-size: 16px;
    background-color: rgba(255, 255, 255, 0.05);
    color: cyan;
    border: 1px solid cyan;
    border-radius: 8px;
    backdrop-filter: blur(3px);
    transition: all 0.3s ease;
  }

  select:hover, button:hover {
    background-color: rgba(0, 0, 0, 0.7);
    box-shadow: 0 0 10px cyan;
  }

  table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
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

  .back-btn {
    display: inline-block;
    margin-bottom: 20px;
    padding: 10px 20px;
    font-size: 16px;
    background-color: rgba(0, 255, 255, 0.1);
    color: cyan;
    border: 1px solid cyan;
    border-radius: 8px;
    cursor: pointer;
    transition: 0.3s ease;
    text-decoration: none;
  }

  .back-btn:hover {
    background-color: rgba(0, 255, 255, 0.2);
    box-shadow: 0 0 10px cyan;
  }
</style>

</head>
<body>
  <canvas id="particles"></canvas>
  <header>📊 Booking History</header>
  <div class="container">
    <div class="back-btn">
      <a href="admin_panel.php"><button>← Back to Admin Panel</button></a>
    </div>

    <form method="GET" action="booking_history.php">
      <label>Area:</label>
      <select name="area">
        <option value="">-- All Areas --</option>
        <?php foreach ($areas as $area): ?>
          <option value="<?= $area ?>" <?= $selected_area == $area ? 'selected' : '' ?>>
            <?= htmlspecialchars($area) ?>
          </option>
        <?php endforeach; ?>
      </select>

      <label>User:</label>
      <select name="user_name">
        <option value="">-- All Users --</option>
        <?php foreach ($usernames as $user): ?>
          <option value="<?= $user ?>" <?= $selected_user == $user ? 'selected' : '' ?>>
            <?= htmlspecialchars($user) ?>
          </option>
        <?php endforeach; ?>
      </select>

      <button type="submit">Search</button>
    </form>

    <table>
      <tr>
        <th>User ID</th>
        <th>User</th>
        <th>Parking Area ID</th>
        <th>Slot No</th>
        <th>Date</th>
        <th>Booking Time</th>
        <th>Area</th>
      </tr>
      <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
          <td><?= $row['user_id']; ?></td>
          <td><?= $row['user_name']; ?></td>
          <td><?= $row['slot_number']; ?></td>
          <td><?= $row['booking_date']; ?></td>
          <td><?= $row['booking_time']; ?></td>
          <td><?= $row['area']; ?></td>
        </tr>
      <?php } ?>
    </table>
  </div>

  <!-- Particle canvas and JS stays untouched -->
<script src="particles.js"></script>
</body>
</html>
