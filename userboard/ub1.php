<?php
session_start();
$conn = new mysqli("localhost", "root", "", "parkify");

$user_data = [
  'username' => 'JohnDoe',
  'name' => 'John Doe',
  'email' => 'johndoe@email.com',
  'phoneNo' => '1234567890'
];

if (isset($_SESSION['user_id'])) {
    $id = $_SESSION['user_id'];
    $sql = "SELECT username, firstName, lastName, email, phoneNo, state, city, address1, carNumber FROM user_form WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($username, $firstName, $lastName, $email, $phoneNO, $state, $city, $address1, $carNumber);
    if ($stmt->fetch()) {
        $user_data['username'] = $username;
        $user_data['name'] = $firstName . ' ' . $lastName;
        $user_data['email'] = $email;
        $user_data['phoneNo'] = $phoneNO;
        $user_data['state'] = $state;
        $user_data['city'] = $city;
        $user_data['address1'] = $address1;
        $user_data['carNumber'] = $carNumber;
    }
    
    $stmt->close();

    // FETCH BOOKING HISTORY
    $booking_history_html = "";
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
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->bind_result($booking_date, $booking_time, $slot_number, $parking_name, $parking_area,$parking_city);
        
        while ($stmt->fetch()) {
            $booking_history_html .= "
              <div style='margin-bottom: 10px; color: #ccc;'>
                <strong>Date:</strong> $booking_date<br>
                <strong>Time:</strong> $booking_time<br>
                <strong>Slot:</strong> $slot_number<br>
                <strong>Location:</strong> $parking_name,<br> $parking_area<br>
                <strong>City:</strong> $parking_city
              </div><hr style='border-color: #555;'>
            ";
        }

        if (empty($booking_history_html)) {
            $booking_history_html = "<p style='color: #ccc;'>No bookings found.</p>";
        }

        $stmt->close();
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Parkify</title>
  <link rel="stylesheet" href="ub.css">
  <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@600&family=Poppins&display=swap" rel="stylesheet">
  <style>
    .center-wrapper {
      position: absolute;
      top: 200px; /* Adjusted for below rear view mirror */
      left: 780px;
      transform: translate(-50%, -50%);
      text-align: center;
      z-index: 1;
    }

    .brand-title {
      color: #b000f0;
      font-weight: 900;
      font-size: 42px;
      font-family: 'Orbitron', sans-serif;
      letter-spacing: 2px;
      text-shadow: 0 0 6px #9f00ff, 0 0 12px #cc33ff;
      animation: fadeIn 1.2s ease-in-out;
    }

    .brand-subtext {
      font-family: 'Poppins', sans-serif;
      font-size: 25px;
      color: #bed1ff;
      opacity: 0;
      transform: translateY(20px);
      animation: slideUp 1.2s ease forwards;
      animation-delay: 0.8s;
      text-shadow: 0 0 6px #bed1ff;
      font-weight: 600;
    }

    .map-button {
      margin-top: 25px;
      padding: 16px 36px;
      background: linear-gradient(135deg, #ff00cc, #3333ff);
      color: #fff;
      font-weight: bold;
      font-size: 18px;
      border: 2px solid rgba(255, 255, 255, 0.2);
      border-radius: 16px;
      box-shadow: 0 0 10px #ff00cc, 0 0 20px #6600cc;
      letter-spacing: 1px;
      transition: all 0.35s ease;
      animation: pulseRacer 2s infinite ease-in-out;
    }

    .map-button:hover {
      background: linear-gradient(135deg, #e600ac, #2200cc);
      box-shadow: 0 0 15px #ff00cc, 0 0 25px #6600cc;
      transform: scale(1.1);
    }

    @keyframes pulseRacer {
      0%, 100% {
        transform: scale(1);
      }
      50% {
        transform: scale(1.05) translateY(-2px);
      }
    }

    @keyframes slideUp {
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: scale(0.9);
      }
      to {
        opacity: 1;
        transform: scale(1);
      }
    }
  </style>
</head>
<body>  
<script src="https://www.gstatic.com/dialogflow-console/fast/messenger/bootstrap.js?v=1"></script>
<df-messenger
  intent="WELCOME"
  chat-title="SPARKY"
  agent-id="14ce0071-20a1-4f6c-bbfa-cbe0c2e7d2a7"
  language-code="en"
></df-messenger>
<div class="animated-bg"></div>

  <div class="dashboard-wrapper">
  <div class="sidebar">
  <div class="menu-item tooltip-container" onclick="openPanel('personal')">
    👤
    <span class="tooltip-text">Personal</span>
  </div>
  <div class="menu-item tooltip-container" onclick="openPanel('history')">
    🕰️
    <span class="tooltip-text">History</span>
  </div>
  <div class="menu-item tooltip-container" onclick="openPanel('settings')">
    ⚙️
    <span class="tooltip-text">Settings</span>
  </div>

  <div class="menu-item tooltip-container" onclick="openPanel('About')">
    ❤️
    <span class="tooltip-text">About </span>
  </div>

  <div class="menu-item tooltip-container" onclick="window.location.href='../load/loading.php';">
    🔓
    <span class="tooltip-text">Logout</span>
  </div>
</div>


    <div class="center-wrapper">
    <img src="logo.png" class="logo-img" alt="Parkify Logo" />
      <p class="brand-subtext">Smart, Effortless & Secure Parking, Anytime</p>
      <a target="_blank" href="../experiment_with_user/userboard.html" class="map-button">Find a Parking Spot</a>
    </div>
  </div>

  <div id="slidePanel" class="slide-panel">
    <span class="close-btn" onclick="closePanel()">&times;</span>
    <div id="panelContentPersonal" class="panelTab" style="display: none;">
  <h2 style="color: #fff;">👤 User Info</h2>
  <p style="color: #ccc;"><strong>Name:</strong> <?php echo $user_data['name']; ?></p>
  <p style="color: #ccc;"><strong>Email:</strong> <?php echo $user_data['email']; ?></p>
  <p style="color: #ccc;"><strong>Username:</strong> <?php echo $user_data['username']; ?></p>
  <p style="color: #ccc;"><strong>Phone No:</strong> <?php echo $user_data['phoneNo']; ?></p>
  <p style="color: #ccc;"><strong>State:</strong> <?php echo $user_data['state']; ?></p>
  <p style="color: #ccc;"><strong>City:</strong> <?php echo $user_data['city']; ?></p>
  <p style="color: #ccc;"><strong>Address:</strong> <?php echo $user_data['address1']; ?></p>
  <p style="color: #ccc;"><strong>Car Number:</strong> <?php echo $user_data['carNumber']; ?></p>
</div>


<div id="panelContentHistory" class="panelTab" style="display: none;">
  <h2 style="color: #fff;">📜 Booking History</h2>
  <?php echo $booking_history_html; ?>
</div>

<div id="panelContentAbout" class="panelTab" style="display: none;">
  <h2 style="color: #fff;">ABOUT US</h2>
  <h3>OUR TEAM :</h3>
  <u>
  <a target="_blank" href="https://www.linkedin.com/in/lakshuki-hatwar-a80090324/">Lakshuki Hatwar ↗️</a> <br>
  <a target="_blank" href="https://www.linkedin.com/in/siddhi-dhoke-53b7b432b/">Siddhi Dhoke ↗️</a> <br>
  <a target="_blank" href="https://www.linkedin.com/in/nakul-bhadade">Nakul Bhadade ↗️</a> <br>
  <a target="_blank" href="https://www.linkedin.com/in/ness-dubey-461496326/">Ness Dubey ↗️</a> <br>
  <a target="_blank" href="https://www.linkedin.com/in/pranjal-baghel-b4aa8a283/">Pranjal Baghel ↗️</a> <br>
  </u>
</div>

<div id="panelContentSettings" class="panelTab" style="display: none;">
  <h2 style="color: #fff;">⚙ Settings</h2>
  <p style="color: #ccc;">(Customize your experience...)</p>
  <a href="edit_profile.php" style="
    display: inline-block;
    padding: 10px 20px;
    background-color: #3498db;
    color: white;
    border-radius: 5px;
    text-decoration: none;
    position: absolute;
    top: 20px;
    right: 20px;
    font-weight: bold;
">Edit Profile</a>

</div>

  </div>

  <script>
function openPanel(type) {
  const panel = document.getElementById('slidePanel');
  document.querySelectorAll('.panelTab').forEach(tab => tab.style.display = 'none');

  if (type === 'personal') {
    document.getElementById('panelContentPersonal').style.display = 'block';
  } else if (type === 'history') {
    document.getElementById('panelContentHistory').style.display = 'block';
  } else if (type === 'settings') {
    document.getElementById('panelContentSettings').style.display = 'block';
  } else if (type === 'About') {
    document.getElementById('panelContentAbout').style.display = 'block';
  }

  panel.style.right = '0';
}

document.getElementById("history-icon").addEventListener("click", () => {
  const panel = document.getElementById("history-panel");
  const content = document.getElementById("history-content");

  if (panel.style.display === "none" || panel.style.display === "") {
    panel.style.display = "block";
    content.innerHTML = "Loading...";

    fetch("get_booking_history.php")
      .then(res => res.text())
      .then(data => {
        content.innerHTML = data;
      })
      .catch(err => {
        content.innerHTML = "<p style='color: red;'>Failed to load booking history.</p>";
        console.error(err);
      });
  } else {
    panel.style.display = "none";
  }
});

function closePanel() {
  document.getElementById('slidePanel').style.right = '-400px';
}
</script>

<canvas id="particles"></canvas>
<script src="particles.js"></script>
</body>
</html>