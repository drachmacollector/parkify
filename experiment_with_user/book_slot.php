<?php
$conn = new mysqli("sql105.infinityfree.com", "if0_39017725", "jeZyqYSlUAhhmM", "if0_39017725_parkify_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

header('Content-Type: application/json');

if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "DB connection failed"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$username = $conn->real_escape_string($data['username']);
$date = $conn->real_escape_string($data['date']);
$area_id = (int)$data['area_id'];
$slot_key = $conn->real_escape_string($data['slot_key']);
$area_name = $conn->real_escape_string($data['area_name']);

// Step 1: Get user_id from username
$user_sql = "SELECT id FROM user_form WHERE username = '$username'";
$user_result = $conn->query($user_sql);

if (!$user_result || $user_result->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "Username not found"]);
    exit;
}

$user_id = $user_result->fetch_assoc()['id'];

// Step 2: Decrease slot availability
$update = $conn->query("UPDATE daily_slot_availability SET $slot_key = $slot_key - 1 WHERE area_id = $area_id AND date = '$date'");
if (!$update) {
    echo json_encode(["success" => false, "message" => "Failed to update slot count"]);
    exit;
}

// Step 3: Insert into booking history
$time = date("H:i:s");
$insert = $conn->query("INSERT INTO booking_history (user_id, booking_date, booking_time, slot_number, area_name, area_id,username)
                        VALUES ($user_id, '$date', '$time', '$slot_key', '$area_name', $area_id,'$username')");

if ($insert) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to log booking"]);
}

$conn->close();
?>
