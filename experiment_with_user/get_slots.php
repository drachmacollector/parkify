<?php
$conn = new mysqli("sql105.infinityfree.com", "if0_39017725", "jeZyqYSlUAhhmM", "if0_39017725_parkify_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$area_id = $_GET['area_id'];
$date = $_GET['date'];

$result = $conn->query("SELECT * FROM daily_slot_availability WHERE area_id = $area_id AND date = '$date'");
if ($result->num_rows > 0) {
    echo json_encode($result->fetch_assoc());
} else {
    // Return empty slots if not found
    echo json_encode([
        "slot1" => 0, "slot2" => 0, "slot3" => 0, "slot4" => 0,
        "slot5" => 0, "slot6" => 0, "slot7" => 0, "slot8" => 0,
        "slot9" => 0, "slot10" => 0, "slot11" => 0
    ]);
}
$conn->close();
?>
