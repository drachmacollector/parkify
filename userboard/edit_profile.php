<?php
session_start();
$conn = new mysqli("sql105.infinityfree.com", "if0_39017725", "jeZyqYSlUAhhmM", "if0_39017725_parkify_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


// Make sure user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: ub1.php");
    exit();
}

$username = $_SESSION['username'];

// Fetch current user data
$sql = "SELECT * FROM user_form WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Handle update
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST['username'];
    $email = $_POST['email'];
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $phoneNo = $_POST['phoneNo'];
    $address1 = $_POST['address1'];
    $address2 = $_POST['address2'];
    $carNumber = $_POST['carNumber'];

    $update_sql = "UPDATE user_form SET username= ?, email = ?, firstName = ?, lastName = ?, phoneNo = ?, address1 = ?, address2 = ?, carNumber = ? WHERE username = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("sssssssss", $name, $email, $firstName, $lastName, $phoneNo, $address1, $address2, $carNumber, $username);
    $update_stmt->execute();

    echo "<script>alert('Profile updated successfully!'); window.location.href='ub1.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <style>
        /* === Spider Particle Background + Animated Gradient Background === */
        body {
        margin: 0;
        font-family: 'Poppins', sans-serif;
        background: #000;
        background-size: cover;
        background-repeat: no-repeat;
        background-attachment: fixed;
        color: #eee;
        overflow-x: hidden;
        position: relative;
        }
        .form-box {
        background: rgba(255, 255, 255, 0);
        backdrop-filter: blur(5px);
        border-radius: 20px;
        width: 500px;
        padding: 30px 40px;
        margin-top: 20px;
        box-shadow: 0 0 25px rgba(0, 255, 255, 0.3);
        display: flex;
        flex-direction: column;
        /* text-align: center; */
        margin-left: 33%;
    }
            /* === FORM STYLES === */
    form label {
        font-family: 'bahnschrift semibold', Times, serif;
        display: block;
        margin: 10px 0 5px;
        font-weight: 600;
        letter-spacing: 0.1em;
        margin: 10px 0 8px;
        /* margin-left: -423px; */
    }

    form input {
        width: 96%;
        padding: 10px;
        background: linear-gradient(to left, rgba(9, 148, 255, 0.15), rgba(255, 0, 255, 0.15));
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 1rem;
        /* cursor: pointer; */
    }

    form button {
        margin-top: 20px;
        width: 100%;
        padding: 12px;
        background: linear-gradient(to left, rgb(9, 148, 255), rgb(255, 0, 255));
        color: black;
        font-weight: bold;
        border: none;
        border-radius: 8px;
        transition: transform 0.3s;
        box-shadow: 0 0 10px rgba(255, 0, 255, 0.5);
    }

    form button:hover {
        transform: scale(1.05);
    }

    /* === LINKS & SIGNUP SECTION === */
    form a {
        color: #00eaff;
        font-weight: 500;
        text-decoration: none;
    }

    #submit {
        cursor: pointer;
        background: linear-gradient(to left, rgb(9, 148, 255), rgb(255, 0, 255));
        margin-top: 20px;
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
    h2{
        text-align: center;
    } 
    </style>
</head>
<body>

<div class="form-box">
    <h2>Edit Your Profile</h2>
    <form method="POST">
        <div class="input-field">
            <label for="name">Name:</label>
            <input type="text" id="name" name="username" value="<?= $user['username'] ?>" required>
        </div>

        <div class="input-field">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?= $user['email'] ?>" required>
        </div>

        <div class="input-field">
            <label for="firstName">  First Name:</label>
            <input type="text" id="firstName" name="firstName" value="<?= $user['firstName'] ?>" required>
        </div>

        <div class="input-field">
            <label for="lastName">Last Name:</label>
            <input type="text" id="lastName" name="lastName" value="<?= $user['lastName'] ?>" required>
        </div>

        <div class="input-field">
            <label for="phoneNo">Phone Number:</label>
            <input type="text" id="phoneNo" name="phoneNo" value="<?= $user['phoneNo'] ?>" required>
        </div>

        <div class="input-field">
            <label for="address1">Address 1:</label>
            <input type="text" id="address1" name="address1" value="<?= $user['address1'] ?>" required>
        </div>

        <div class="input-field">
            <label for="address2">Address 2:</label>
            <input type="text" id="address2" name="address2" value="<?= $user['address2'] ?>" required>
        </div>

        <div class="input-field">
            <label for="carNumber">Car Number:</label>
            <input type="text" id="carNumber" name="carNumber" value="<?= $user['carNumber'] ?>" required>
        </div>
        <br><br>
        <input type="submit" id="submit" value="Update Profile">
    </form>
</div>
<canvas id="particles"></canvas>
<script src="particles.js"></script>

</body>
</html>
