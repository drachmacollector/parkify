<?php
$conn = new mysqli("sql105.infinityfree.com", "if0_39017725", "jeZyqYSlUAhhmM", "if0_39017725_parkify_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_POST['id'];
$password = $_POST['password'];
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Step 1: Get username from user_form
$username = "";
$get_user_sql = "SELECT username FROM user_form WHERE id = ?";
$stmt = $conn->prepare($get_user_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username);
$stmt->fetch();
$stmt->close();

// Step 2: Insert into user_passwords table
$sql = "INSERT INTO user_passwords (user_id, username, password) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iss", $user_id, $username, $hashed_password);

if ($stmt->execute()) {
    echo "
    <script>
      alert('✅ Password saved successfully!');
      window.location.href = '../load/loading.php';
    </script>";
} else {
    echo "❌ Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
