<?php
// Start session
session_start();
$conn = new mysqli("sql105.infinityfree.com", "if0_39017725", "jeZyqYSlUAhhmM", "if0_39017725_parkify_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}




// Check if the button was clicked and process the query
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add_slots'])) {
    // Get the selected date from the form
    $new_date = $_POST['new_date'];

    // SQL query to add the slots for the new date
    $set_sql = "SET @new_date = ?";
    $insert_sql = "
        INSERT INTO daily_slot_availability (
            area_id, date,
            slot1, slot2, slot3, slot4, slot5, slot6,
            slot7, slot8, slot9, slot10, slot11
        )
        SELECT 
            id, @new_date,
            total_slots, total_slots, total_slots, total_slots, total_slots, total_slots,
            total_slots, total_slots, total_slots, total_slots, total_slots
        FROM parkingspots
        WHERE id NOT IN (
            SELECT area_id FROM daily_slot_availability WHERE date = @new_date
        );
    ";

    // First, execute the SET statement
    if ($stmt = $conn->prepare($set_sql)) {
        $stmt->bind_param("s", $new_date);
        $stmt->execute();
        $stmt->close();
    }

    // Then, execute the INSERT statement
    if ($stmt = $conn->prepare($insert_sql)) {
        if ($stmt->execute()) {
            echo "<script>alert('Slots added successfully for $new_date!');</script>";
        } else {
            echo "<script>alert('Error: Could not add slots for $new_date.');</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('SQL preparation failed.');</script>";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Panel - Smart Parking</title>
  <style>
      body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        margin: 0;
        padding: 0;
        background: linear-gradient(135deg, #0f0c29, #302b63, #24243e);
        color: #FFFFFF;
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
    .animated-bg {
      position: fixed;
      top: 0;
      left: 0;
      z-index: -10;
      width: 200%;
      height: 200%;
      background: radial-gradient(circle at 20% 30%, #00f5ff88 0%, transparent 40%),
                  radial-gradient(circle at 70% 60%, #e600ff88 0%, transparent 40%),
                  radial-gradient(circle at 40% 80%, #ff000066 0%, transparent 40%);
      animation: moveGradient 15s infinite linear;
      filter: blur(100px);
      opacity: 0.7;
    }

    @keyframes moveGradient {
      0% { transform: translate(0, 0) scale(1); }
      50% { transform: translate(-25%, -25%) scale(1.2); }
      100% { transform: translate(0, 0) scale(1); }
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
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 80vh;
    }

    .panel {
      background: rgba(255, 255, 255, 0.05);
      backdrop-filter: blur(3px);
      box-shadow: 0 4px 30px rgba(0, 0, 0, 0.4);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 16px;
      padding: 20px 30px;
      width: 320px;
      color: white;
      margin: 20px auto;
      animation: fadeInUp 1s ease;
    }

    .panel h2 {
      margin-bottom: 25px;
      color: #333;
    }

    h1{
      text-align: center;
      letter-spacing: 1px;
      margin-bottom: 25px;
      font-weight: 600;
      font-size: 2em;
      color:rgb(255, 255, 255);
      animation: fadeInDown 1s ease;
      text-shadow: 0 0 5px rgb(255, 255, 255);
    }

    #new_date{
      width: auto;
      margin: 0px 20px -2px 20px;
      flex: 1;
      padding: 10px;
      border-radius: 10px;
      border: none;
    font-size: 17px;
    background: rgba(0, 0, 0, 0.4);
    color: rgb(255, 255, 255);
    }

    #date{
      display: flex;
      align-items: center;
    }

    #date_label {
      color: #0ff;
      font-size: 16px;
      margin-bottom: 10px;
      margin-left: 25px;
      text-align: center;
      font-weight: 500;
    }

    #add{
      display: block;
    margin-bottom: 10px;
    font-size: 18px;
    color: rgb(0, 255, 17);
    margin-left: 25px;
    margin-right: 25px;
    }

    .btn {
      display: block;
      width: 220px;
      margin: 15px auto;
      padding: 14px;
      background-color: #345fdb;
      box-shadow:#345fdb;
      color: white;
      font-size: 16px;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      transition: background-color 0.3s;
      text-decoration: none;
    }

    .btn2 {
      display: block;
      width: auto;
      margin: -2px auto;
      margin-bottom: 27px;
      padding: 11px;
      background: linear-gradient(135deg, #56ab2f, #a8e063);
      color: white;
      font-weight: bold;
      cursor: pointer;
      box-shadow: 0 0 20px rgba(76, 175, 80, 0.4);
      animation: pulse 2s infinite ease-in-out;
      font-size: 16px;
      border: none;
      border-radius: 8px;
      transition: background-color 0.3s;
      text-decoration: none;
    }
    .btn2:hover {
      transform: scale(1.05);
      box-shadow: 0 0 25px rgba(144, 238, 144, 0.6);
    }

    .btn:hover {
      background-color:rgb(0, 153, 255);
    }

    #logout {
      display: block;
      width: auto;
      margin: 25px auto;
      margin-bottom: 7px;
      padding: 14px;
      background: linear-gradient(145deg, #00f5ff, #e600ff);
      color: white;
      font-weight: bold;
      cursor: pointer;
      box-shadow: 0 0 20px rgba(76, 112, 175, 0.4);
      font-size: 16px;
      border: none;
      border-radius: 8px;
      transition: background-color 0.3s;
      text-decoration: none;
    }
    #logout:hover {
      background: linear-gradient(145deg, #e600ff, #00f5ff);
      transform: scale(1.03);
    }

    a{
      text-decoration: none;
    }

    @keyframes pulse {
    0% {
      box-shadow: 0 0 0 0 rgba(144, 238, 144, 0.4);
    }

    70% {
      box-shadow: 0 0 0 12px rgba(144, 238, 144, 0);
    }

    100% {
      box-shadow: 0 0 0 0 rgba(144, 238, 144, 0);
    }
  }

  @keyframes fadeInDown {
    from {
      opacity: 0;
      transform: translateY(-30px);
    }

    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  @keyframes fadeInUp {
    from {
      opacity: 0;
      transform: translateY(30px);
    }

    to {
      opacity: 1;
      transform: translateY(0);
    }
  }
  </style>
</head>
<body>
<!-- <div class="animated-bg"></div> -->

  <header>👨🏻‍💻  Admin Panel</header>

  <div class="container">
    <div class="panel">
      <h1>Choose an Action</h1>
      <div class="form-box">
    <form method="POST">
            <!-- <label for="new_date" id="date_label">Select Date:</label> -->
            <div id="add"><b>Add Parking Slots for New Date</b></div>
            
            <div id="date"><input type="date" id="new_date" name="new_date" required></div>
            <br>
            <button type="submit" class="btn2" name="add_slots">➕ Add Slots</button>
    </form>
</div>

      <a href="all_users.php" class="btn">All Users</a>
      <a href="admin.php" class="btn">Add Parking Spot</a>
      <a href="booking_history.php" class="btn">Show Booking History</a>
      <a href="statistics.php" class="btn">Statistics</a>

      <a href="admin_login.php"><button type="submit" name="add_slots" id="logout" >⬅️ Logout</button></a>

    </div>
  </div>
<canvas id="particles"></canvas>
<script src="particles.js"></script>

</body>
</html>
