<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = new mysqli("sql105.infinityfree.com", "if0_39017725", "jeZyqYSlUAhhmM", "if0_39017725_parkify_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = $_POST['name'];
  $city = $_POST['city'];
  $area = $_POST['area'];
  $lat = $_POST['lat'];
  $lon = $_POST['lon'];
  $total = $_POST['total_slots'];
  $available = $_POST['available_slots'];

  $sql = "INSERT INTO parkingspots (name, lat, lon, total_slots, available_slots, city, area)
          VALUES ('$name', '$lat', '$lon', '$total', '$available', '$city', '$area')";

  if ($conn->query($sql)) {
    echo "✅ Parking spot added!";
  } else {
    echo "❌ Error: " . $conn->error;
  }
} else {
  echo "DONT OPEN THIS FILE JUST ENTER YOUR PARKING SPOT DATA INTO ADMIN.PHP";
}
?>
