<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: ../registration/register.php");
    exit();
}

$username = $_SESSION['username'];

// Connect to database
$conn = new mysqli("sql105.infinityfree.com", "if0_39017725", "jeZyqYSlUAhhmM", "if0_39017725_parkify_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$slot = $_GET['slot'] ?? '';
$area = $_GET['area'] ?? '';
$city = $_GET['city'] ?? '';
$parkingName = $_GET['name'] ?? '';
$date = $_GET['date'] ?? '';

// Fetch user and booking details
$sql = "
SELECT 
    u.firstName, 
    u.lastName, 
    u.phoneNo, 
    u.email, 
    u.carNumber, 
    bh.booking_date, 
    bh.booking_time, 
    bh.slot_number, 
    ps.name AS parking_name, 
    ps.area AS parking_area, 
    ps.city 
FROM user_form u
LEFT JOIN booking_history bh ON bh.user_name = u.username
LEFT JOIN parkingspots ps ON bh.area = ps.name
WHERE u.username = ?
ORDER BY bh.booking_time DESC LIMIT 1";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("SQL error: " . $conn->error);
}
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();

    $fullName = $row['firstName'] . " " . $row['lastName'];
    $phone = $row['phoneNo'];
    $email = $row['email'];
    $carNumber = $row['carNumber'];
    $bookingTime = $row['booking_time'];

   

    // Time slot mapping
    function getTimeSlot($slot) {
        $slots = [
            1 => "7:00 AM – 8:00 AM",
            2 => "8:00 AM – 9:00 AM",
            3 => "9:00 AM – 10:00 AM",
            4 => "10:00 AM – 11:00 AM",
            5 => "11:00 AM – 12:00 PM",
            6 => "12:00 PM – 1:00 PM",
            7 => "1:00 PM – 2:00 PM",
            8 => "2:00 PM – 3:00 PM",
            9 => "3:00 PM – 4:00 PM",
            10 => "4:00 PM – 5:00 PM",
            11 => "5:00 PM – 6:00 PM"
        ];
        return $slots[$slot] ?? "Unknown Slot";
    }

    $timeSlotText = getTimeSlot($slot);
} else {
    echo "User or booking details not found.";
    exit();
}
$slotNumber = $_POST['slot'] ?? $_GET['slot'] ?? null;
$date = $_POST['date'] ?? $_GET['date'] ?? null;
$area_id = $_POST['area_id'] ?? $_GET['area_id'] ?? null;



$slotNumber = intval($slotNumber);
$area_id = intval($area_id);
$slotColumn = "slot" . $slotNumber;

// Validate slot column
$allowedSlots = ["slot1", "slot2", "slot3", "slot4", "slot5", "slot6", "slot7", "slot8", "slot9", "slot10", "slot11"];
if (!in_array($slotColumn, $allowedSlots)) {
    die("Invalid slot selected.");
}

$area_id = $_POST['area_id'] ?? $_GET['area_id'] ?? null;

$check = $conn->prepare("SELECT $slotColumn FROM daily_slot_availability WHERE area_id = ? AND date = ?");
if (!$check) {
    die("Query failed: " . $conn->error);
}
$check->bind_param("is", $area_id, $date);
$check->execute();
$result = $check->get_result();
$row = $result->fetch_assoc();

if ($row && $row[$slotColumn] > 0) {
    $update = $conn->prepare("UPDATE daily_slot_availability SET $slotColumn = $slotColumn - 1 WHERE area_id = ? AND date = ?");
    $update->bind_param("is", $area_id, $date);
    if ($update->execute()) {
        echo "✅ Slot $slotNumber booked for $date.";
    } else {
        echo "❌ Failed to update slot.";
    }
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Booking Confirmation - Parkify</title>
  <link rel="stylesheet" href="booking.css">
        <link rel="shortcut icon" href="../registration/car.ico" type="image/x-icon">

  <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@700&family=Michroma&family=Montserrat:wght@300;400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>
<body>
  <!-- Background Elements -->
  <canvas id="particles"></canvas>
  <div class="animated-bg"></div>
  <div class="grid-lines"></div>

  <div class="booking-container">
    <div class="user-status">
      <i class="fas fa-user-astronaut"></i>
      <span>SESSION ACTIVE: <strong><?php echo htmlspecialchars($username); ?></strong></span>
    </div>

    <div class="cyber-card">
      <div class="card-header">
        <h2><i class="fas fa-parking"></i> BOOKING CONFIRMATION</h2>
        <div class="card-glow"></div>
      </div>

      <form method="POST" action="invoice.php" class="cyber-form">
        <div class="form-grid">
          <div class="input-group">
            <label><i class="fas fa-user"></i> USERNAME</label>
            <input type="text" name="username" value="<?= htmlspecialchars($username) ?>" readonly>
          </div>

          <div class="input-group">
            <label><i class="fas fa-id-card"></i> FULL NAME</label>
            <input type="text" name="fullname" value="<?= htmlspecialchars($fullName) ?>" readonly>
          </div>

          <div class="input-group">
            <label><i class="fas fa-envelope"></i> EMAIL</label>
            <input type="text" name="email" value="<?= htmlspecialchars($email) ?>" readonly>
          </div>

          <div class="input-group">
            <label><i class="fas fa-phone"></i> PHONE</label>
            <input type="text" name="phone" value="<?= htmlspecialchars($phone) ?>" readonly>
          </div>

          <div class="input-group">
            <label><i class="fas fa-car"></i> CAR NUMBER</label>
            <input type="text" name="car_number" value="<?= htmlspecialchars($carNumber) ?>" readonly>
          </div>

          <div class="input-group">
            <label><i class="fas fa-map-marker-alt"></i> PARKING SPOT</label>
            <input type="text" name="parking_name" value="<?= htmlspecialchars($parkingName) ?>" readonly>
          </div>

          <div class="input-group">
            <label><i class="fas fa-location-dot"></i> AREA</label>
            <input type="text" name="area" value="<?= htmlspecialchars($area) ?>" readonly>
          </div>

          <div class="input-group">
            <label><i class="fas fa-city"></i> CITY</label>
            <input type="text" name="city" value="<?= htmlspecialchars($city) ?>" readonly>
          </div>

          <div class="input-group">
            <label><i class="fas fa-hashtag"></i> SLOT NUMBER</label>
            <input type="text" name="slot" value="<?= htmlspecialchars($slot) ?>" readonly>
          </div>

          <div class="input-group">
            <label><i class="fas fa-clock"></i> TIME SLOT</label>
            <input type="text" name="time_slot_text" value="<?= htmlspecialchars($timeSlotText) ?>" readonly>
          </div>

          <div class="input-group">
            <label><i class="fas fa-calendar-day"></i> BOOKING DATE</label>
            <input type="text" name="date" value="<?= htmlspecialchars($date) ?>" readonly>
          </div>
        </div>

        <button type="submit" class="cyber-button">
          <span class="button-text"><i class="fas fa-check-circle"></i> CONFIRM BOOKING</span>
          <span class="button-glitch"></span>
        </button>
      </form>
    </div>
  </div>

  <script src="particles.js"></script>
</body>
</html>