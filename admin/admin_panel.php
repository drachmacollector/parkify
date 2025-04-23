<?php
// Start session
session_start();
$conn = new mysqli("localhost", "root", "", "parkify");



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
      padding: 40px 30px;
      width: 320px;
      color: white;
      margin: 20px auto;
    }

    .panel h2 {
      margin-bottom: 25px;
      color: #333;
    }
    h1{
      text-align: center;
    margin-bottom: 25px;
    font-weight: 600;
    font-size: 2em;
    color: #00f5ff;
    text-shadow: 0 0 10px #00f5ff99;
    }

    #new_date{
      width: auto;
      margin-top: 10px;
      margin-left: 20px;
      margin-right: 20px;
      flex: 1;
      padding: 10px;
      border-radius: 10px;
      border: none;
    font-size: 17px;
    background: rgba(0, 0, 0, 0.4);
    color: rgb(255, 255, 255);
    box-shadow: 0 0 10px #0ff;
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
    color: rgb(255, 0, 0);
    margin-left: 25px;
    margin-right: 25px;
    }

    .btn {
      display: block;
      width: 220px;
      margin: 15px auto;
      padding: 14px;
      background-color: #3498db;
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
      width: 100px;
      margin: -2px auto;
      margin-bottom: 27px;
      padding: 14px;
      background-color:rgb(0, 248, 132);
      color: black;
      font-size: 16px;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      transition: background-color 0.3s;
      text-decoration: none;
    }
    .btn2:hover {
      background-color:rgb(3, 78, 18);
      color:white;
    }

    .btn:hover {
      background-color:rgb(10, 36, 53);
      
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
      <div id="add"><b>Add Parking Slots for New Date</b></div>
    <form method="POST">
            <label for="new_date" id="date_label">Select Date:</label>
            <br>
            <div id="date"><input type="date" id="new_date" name="new_date" required></div>
            <br>
            <input  class="btn2" type="submit" name="add_slots" value="Add Slots">
    </form>
</div>

      <a href="all_users.php" class="btn">All Users</a>
      <a href="admin.php" class="btn">Add Parking Spot</a>
      <a href="booking_history.php" class="btn">Show Booking History</a>
      <a href="statistics.php" class="btn">Statistics</a>
    </div>
  </div>
<canvas id="particles"></canvas>
<script src="particles.js"></script>

</body>
</html>
