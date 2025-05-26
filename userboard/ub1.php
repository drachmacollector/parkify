<?php
session_start();
$conn = new mysqli("sql105.infinityfree.com", "if0_39017725", "jeZyqYSlUAhhmM", "if0_39017725_parkify_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


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
      JOIN parkingspots ps ON bh.area = ps.id
      WHERE bh.user_id = ?
      ORDER BY bh.booking_date DESC, bh.booking_time DESC
    ";
  if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($booking_date, $booking_time, $slot_number, $parking_name, $parking_area, $parking_city);

    while ($stmt->fetch()) {
      $booking_history_html .= "
              <div style='margin-bottom: 10px; color: #ffffff;'>
                <strong>Date:</strong> $booking_date<br>
                <strong>Time:</strong> $booking_time<br>
                <strong>Slot:</strong> $slot_number<br>
                <strong>Location:</strong> $parking_name,<br> $parking_area<br>
                <strong>City:</strong> $parking_city
              </div><hr style='border-color: #555;'>
            ";
    }

    if (empty($booking_history_html)) {
      $booking_history_html = "<p style='color: #ffffff;'>No bookings found.</p>";
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
  <title>Parkify Dashboard</title>
  <link rel="stylesheet" href="ub.css">
        <link rel="shortcut icon" href="../registration/car.ico" type="image/x-icon">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@700&family=Michroma&family=Montserrat:wght@300;400;600&display=swap" rel="stylesheet">
  <script src="https://www.gstatic.com/dialogflow-console/fast/messenger/bootstrap.js?v=1"></script>
</head>

<body>
  <df-messenger
    intent="WELCOME"
    chat-title="SPARKY"
    agent-id="14ce0071-20a1-4f6c-bbfa-cbe0c2e7d2a7"
    language-code="en"></df-messenger>

  <canvas id="particles"></canvas>
  <div class="animated-bg"></div>
  <div class="grid-lines"></div>

  <div class="dashboard-container">

    <div class="cyber-sidebar">
      <div class="sidebar-header">
        <div class="user-avatar">
          <i class="fas fa-user-astronaut"></i>
          <div class="avatar-pulse"></div>
        </div>
        <span class="username"><?php echo $user_data['username']; ?></span>
      </div>

      <nav class="sidebar-nav">
        <div class="nav-item" onclick="openPanel('personal')">
          <i class="fas fa-id-card"></i>
          <span>Personal</span>
          <div class="nav-indicator"></div>
        </div>
        <div class="nav-item" onclick="openPanel('history')">
          <i class="fas fa-history"></i>
          <span>History</span>
          <div class="nav-indicator"></div>
        </div>
        <div class="nav-item" onclick="openPanel('settings')">
          <i class="fas fa-cogs"></i>
          <span>Settings</span>
          <div class="nav-indicator"></div>
        </div>
        <div class="nav-item" onclick="openPanel('About')">
          <i class="fas fa-heart"></i>
          <span>About</span>
          <div class="nav-indicator"></div>
        </div>
        <div class="nav-item" onclick="window.location.href='../load/loading.php';">
          <i class="fas fa-power-off"></i>
          <span>Logout</span>
          <div class="nav-indicator"></div>
        </div>
      </nav>

    </div>

    <!-- Main Content -->
    <main class="dashboard-main">
      <div class="hero-section">
        <div class="logo-container">
          <img src="logo.png" class="logo-img" alt="Parkify Logo">
        </div>

        <h2 class="hero-subtitle">SMART, EFFORTLESS & SECURE PARKING, ANYTIME</h2>

        <div class="cta-container">
          <a href="../experiment_with_user/userboard.html" class="cyber-button primary">
            <span class="button-text">FIND A PARKING SPOT</span>
            <span class="button-scanline"></span>
          </a>

        </div>
      </div>
    </main>
  </div>

  <!-- Slide Panel -->
  <div id="slidePanel" class="cyber-panel">
    <div class="panel-header">
      <h2 id="panelTitle">Panel</h2>
      <button class="close-btn" onclick="closePanel()">
        <i class="fas fa-times"></i>
      </button>
    </div>

    <div class="panel-content">
      <div id="panelContentPersonal" class="panel-tab">
        <p style="color: #ffffff;"><strong>Name:</strong> <?php echo $user_data['name']; ?></p>
        <p style="color: #ffffff;"><strong>Email:</strong> <?php echo $user_data['email']; ?></p>
        <p style="color: #ffffff;"><strong>Username:</strong> <?php echo $user_data['username']; ?></p>
        <p style="color: #ffffff;"><strong>Phone No:</strong> <?php echo $user_data['phoneNo']; ?></p>
        <p style="color: #ffffff;"><strong>State:</strong> <?php echo $user_data['state']; ?></p>
        <p style="color: #ffffff;"><strong>City:</strong> <?php echo $user_data['city']; ?></p>
        <p style="color: #ffffff;"><strong>Address:</strong> <?php echo $user_data['address1']; ?></p>
        <p style="color: #ffffff;"><strong>Car Number:</strong> <?php echo $user_data['carNumber']; ?></p>
      </div>
      <div id="panelContentHistory" class="panel-tab">
        <?php echo $booking_history_html; ?>
      </div>
      <div id="panelContentSettings" class="panel-tab">
        <p style="color: #ffffff;">(Customize your experience...)</p>
        <a href="edit_profile.php" style="
        display: inline-block;
        margin: 150px 182px 0px 0px;
        padding: 10px 20px;
        background-color: #3498db;
        color: white;
        border-radius: 5px;
        text-decoration: none;
        position: absolute;
        top: 20px;
        right: 20px;
        font-weight: bold;">Edit Profile</a>
      </div>
      <div id="panelContentAbout" class="panel-tab">

        <h3>OUR TEAM :</h3>
        <u>
          <a target="_blank" href="https://www.linkedin.com/in/lakshuki-hatwar-a80090324/">Lakshuki Hatwar ↗️</a> <br>
          <a target="_blank" href="https://www.linkedin.com/in/siddhi-dhoke-53b7b432b/">Siddhi Dhoke ↗️</a> <br>
          <a target="_blank" href="https://www.linkedin.com/in/nakul-bhadade">Nakul Bhadade ↗️</a> <br>
          <a target="_blank" href="https://www.linkedin.com/in/ness-dubey-461496326/">Ness Dubey ↗️</a> <br>
          <a target="_blank" href="https://www.linkedin.com/in/pranjal-baghel-b4aa8a283/">Pranjal Baghel ↗️</a> <br>
        </u>
      </div>
    </div>
  </div>

  <script src="particles.js"></script>
  <script>
    // Enhanced panel functions with animations
    function openPanel(type) {
      const panel = document.getElementById('slidePanel');
      const tabs = document.querySelectorAll('.panel-tab');

      // Hide all tabs
      tabs.forEach(tab => {
        tab.style.opacity = '0';
        tab.style.display = 'none';
      });

      // Show selected tab with animation
      setTimeout(() => {
        const titleMap = {
          'personal': '👤 User Info',
          'history': '📜 Booking History',
          'settings': '⚙ Settings',
          'About': 'About Us',
        };

        document.getElementById('panelTitle').textContent = titleMap[type];
        const activeTab = document.getElementById(`panelContent${type.charAt(0).toUpperCase() + type.slice(1)}`);
        activeTab.style.display = 'block';

        setTimeout(() => {
          activeTab.style.opacity = '1';
        }, 50);
      }, 300);

      // Slide in panel
      panel.classList.add('open');
    }

    function closePanel() {
      const panel = document.getElementById('slidePanel');
      panel.classList.remove('open');
    }

    // Add hover effects to nav items
    document.querySelectorAll('.nav-item').forEach(item => {
      item.addEventListener('mouseenter', function() {
        this.querySelector('.nav-indicator').style.width = '100%';
      });

      item.addEventListener('mouseleave', function() {
        this.querySelector('.nav-indicator').style.width = '0';
      });
    });
  </script>
</body>

</html>