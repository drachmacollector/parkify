<?php
$host = "sql105.infinityfree.com";
$user = "if0_39017725";
$pass = "your_actual_vpanel_password"; // Use your InfinityFree account password
$db   = "if0_39017725_parkify_db";


// Connect to DB
$conn = new mysqli("sql105.infinityfree.com", "if0_39017725", "jeZyqYSlUAhhmM", "if0_39017725_parkify_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


// Get form data
$username = $_POST['Username'];
$firstName = $_POST['firstName'];
$lastName = $_POST['lastName'];
$phoneNo = $_POST['phoneNo'];
$email = $_POST['email'];
$state = $_POST['state'];
$city = $_POST['city'];
$address1 = $_POST['address1'];
$address2 = $_POST['address2'];
$dob = $_POST['dob'];
$aadharNumber = $_POST['aadharNumber'];
$carNumber = $_POST['carNumber'];
$dlNumber = $_POST['dlNumber'];

// Handle file uploads
$aadharFile = $_FILES['aadharFile']['name'];
$dlFile = $_FILES['dlFile']['name'];

move_uploaded_file($_FILES['aadharFile']['tmp_name'], "uploads/" . $aadharFile);
move_uploaded_file($_FILES['dlFile']['tmp_name'], "uploads/" . $dlFile);

// Insert into DB
$sql = "INSERT INTO user_form 
    (username, firstName, lastName, phoneNo, email, state, city, address1, address2, dob, 
    aadharNumber, aadharFile, carNumber, dlNumber, dlFile)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssssssssssss", $username, $firstName, $lastName, $phoneNo, 
$email, $state, $city, $address1, $address2, $dob, $aadharNumber, $aadharFile, $carNumber, $dlNumber, $dlFile);

// if ($stmt->execute()) {
//     echo "✅ Data saved successfully!";
// } else {
//     echo "❌ Error: " . $stmt->error;
// }

if ($stmt->execute()) {
    $last_id = $stmt->insert_id;
    header("Location: profile.php?id=$last_id");
    exit();
}


$stmt->close();
$conn->close();
?>