<?php
session_start();
$conn = new mysqli("sql105.infinityfree.com", "if0_39017725", "jeZyqYSlUAhhmM", "if0_39017725_parkify_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if ($conn->connect_error) {
    echo "<p style='color: red;'>Database connection failed: " . $conn->connect_error . "</p>";
    exit;
}

if (!isset($_SESSION['user_id'])) {
    echo "<p style='color: orange;'>⚠️ User not logged in (session is missing).</p>";
    exit;
}

$id = $_SESSION['user_id'];
echo "<p style='color: #0f0;'>✅ Logged in as user_id: $id</p>";

$sql = "
  SELECT 
    bh.booking_date,
    bh.booking_time,
    bh.slot_number,
    ps.name AS parking_name,
    ps.area AS parking_area,
    ps.city AS parking_city
  FROM booking_history bh
  JOIN parkingspots ps ON bh.area_id = ps.id
  WHERE bh.user_id = ?
  ORDER BY bh.booking_date DESC, bh.booking_time DESC
";

$booking_history_html = "";

if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) {
        echo "<p style='color: yellow;'>No bookings found for this user.</p>";
    } else {
        $stmt->bind_result($booking_date, $booking_time, $slot_number, $parking_name, $parking_area, $parking_city);

        while ($stmt->fetch()) {
            $booking_history_html .= "
              <div style='margin-bottom: 10px; color: #ccc;'>
                <strong>Date:</strong> $booking_date<br>
                <strong>Time:</strong> $booking_time<br>
                <strong>Slot:</strong> $slot_number<br>
                <strong>Location:</strong> $parking_name, $parking_area<br>
                <strong>City:</strong> $parking_city
              </div><hr style='border-color: #555;'>";
        }
    }

    $stmt->close();
} else {
    echo "<p style='color: red;'>❌ SQL Prepare failed: " . $conn->error . "</p>";
}

if (!empty($booking_history_html)) {
    echo $booking_history_html;
}
?>
