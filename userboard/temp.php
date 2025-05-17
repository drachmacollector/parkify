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
      JOIN parkingspots ps ON bh.area = ps.id
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
  <title>Parkify Dashboard</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@700&family=Michroma&family=Montserrat:wght@300;400;600&display=swap" rel="stylesheet">
  <script src="https://www.gstatic.com/dialogflow-console/fast/messenger/bootstrap.js?v=1"></script>
  <style>
    /* === BASE STYLES === */
:root {
  --primary: #00eaff;
  --secondary: #ff00ff;
  --dark-bg: #0a0a1a;
  --darker-bg: #050510;
  --glass-bg: rgba(15, 15, 35, 0.6);
  --text-light: rgba(255, 255, 255, 0.9);
  --text-muted: rgba(255, 255, 255, 0.6);
}

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: 'Montserrat', sans-serif;
  background-color: var(--darker-bg);
  color: var(--text-light);
  min-height: 100vh;
  overflow-x: hidden;
  position: relative;
}

/* === BACKGROUND EFFECTS === */
.animated-bg {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: -3;
  background: radial-gradient(circle at 20% 30%, rgba(0, 136, 255, 0.15) 0%, transparent 40%),
              radial-gradient(circle at 70% 60%, rgba(230, 0, 255, 0.15) 0%, transparent 40%),
              radial-gradient(circle at 40% 80%, rgba(116, 0, 255, 0.1) 0%, transparent 40%);
  animation: bgPulse 25s infinite alternate;
}

.grid-lines {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: -2;
  background-image: 
    linear-gradient(rgba(0, 234, 255, 0.03) 1px, transparent 1px),
    linear-gradient(90deg, rgba(0, 234, 255, 0.03) 1px, transparent 1px);
  background-size: 40px 40px;
  opacity: 0.5;
}

#particles {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: -1;
  pointer-events: none;
}

/* === DASHBOARD LAYOUT === */
.dashboard-container {
  display: flex;
  min-height: 100vh;
  position: relative;
  z-index: 1;
}

/* === CYBER SIDEBAR === */
.cyber-sidebar {
  width: 90px;
  height: 100vh;
  background: rgb(10 10 30 / 0%);
  backdrop-filter: blur(4px);
  border-right: 1px solid rgba(0, 234, 255, 0.1);
  display: flex;
  flex-direction: column;
  padding: 30px 0;
  transition: width 0.4s ease;
  overflow: hidden;
  z-index: 2;
}

.cyber-sidebar:hover {
  width: 250px;
}

.sidebar-header {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 0 20px 30px;
  border-bottom: 1px solid rgba(0, 234, 255, 0.1);
  margin-bottom: 30px;
}

.user-avatar {
  position: relative;
  width: 50px;
  height: 50px;
  background: linear-gradient(135deg, var(--primary), var(--secondary));
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 24px;
  margin-bottom: 15px;
}

.avatar-pulse {
  position: absolute;
  width: 100%;
  height: 100%;
  border-radius: 50%;
  border: 2px solid var(--primary);
  animation: pulse 3s infinite ease-out;
  pointer-events: none;
}

.username {
  font-family: 'Orbitron', sans-serif;
  font-size: 14px;
  letter-spacing: 1px;
  opacity: 0;
  white-space: nowrap;
  transition: opacity 0.3s ease;
}

.cyber-sidebar:hover .username {
  opacity: 1;
}

.sidebar-nav {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 10px;
  padding: 0 15px;
}

.nav-item {
  position: relative;
  display: flex;
  align-items: center;
  padding: 15px;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s ease;
  overflow: hidden;
}

.nav-item i {
  font-size: 20px;
  min-width: 30px;
  text-align: center;
}

.nav-item span {
  margin-left: 15px;
  white-space: nowrap;
  opacity: 0;
  transition: opacity 0.3s ease;
}

.cyber-sidebar:hover .nav-item span {
  opacity: 1;
}

.nav-indicator {
  position: absolute;
  bottom: 0;
  left: 0;
  width: 0;
  height: 2px;
  background: linear-gradient(90deg, var(--primary), var(--secondary));
  transition: width 0.3s ease;
}

.nav-item:hover {
  background: rgba(0, 234, 255, 0.1);
}

.nav-item:hover .nav-indicator {
  width: 100%;
}

.sidebar-footer {
  padding: 20px;
  border-top: 1px solid rgba(0, 234, 255, 0.1);
}

.connection-status {
  display: flex;
  align-items: center;
  gap: 10px;
}

.status-indicator {
  width: 12px;
  height: 12px;
  border-radius: 50%;
  background: #0f0;
  box-shadow: 0 0 10px #0f0;
}

.status-indicator.connected {
  background: #0f0;
  box-shadow: 0 0 10px #0f0;
  animation: pulse 2s infinite;
}

/* === MAIN CONTENT === */
.dashboard-main {
  flex: 1;
  padding: 0px 0px 8rem;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
}

.hero-section {
  text-align: center;
  margin: 0 auto;
}

.logo-container {
  position: relative;
  margin: 0 auto 0rem;
  width: 300px;
  height: 300px;
  display: flex;
  align-items: center;
  justify-content: center;
  animation: float 6s ease-in-out infinite;
}

.logo-img {
  width: 120%;
  height: auto;
  filter: drop-shadow(0 0 20px rgba(0, 234, 255, 0.7));
  z-index: 3;
}

.logo-glow {
  position: absolute;
  width: 100%;
  height: 100%;
  border-radius: 50%;
  background: radial-gradient(circle, rgba(0, 234, 255, 0.4) 0%, transparent 60%);
  animation: pulse 3s infinite alternate;
  z-index: 1;
}

.logo-pulse {
  position: absolute;
  width: 100%;
  height: 100%;
  border-radius: 50%;
  border: 2px solid rgba(0, 234, 255, 0.3);
  z-index: 2;
  animation: pulseRing 4s infinite ease-out;
}

.hero-subtitle {
  font-size: 1rem;
  letter-spacing: 4px;
  color: var(--text-light);
  text-shadow: 0 0 15px rgba(0, 234, 255, 0.7);
  margin: 0 0 3rem;
  margin-top: -17px;
  text-transform: uppercase;
  animation: textGlow 2s infinite alternate;
  position: relative;
  z-index: 2;
  background: rgba(0, 0, 0, 0.3);
  padding: 15px 30px;
  border-radius: 30px;
  display: inline-block;
  backdrop-filter: blur(5px);
  border: 1px solid rgba(0, 234, 255, 0.2);
  animation: fadeIn 1.2s ease-in-out;

}
        @keyframes fadeIn {
      from {
        opacity: 0;
        transform: scale(0.2);
      }
      to {
        opacity: 1;
        transform: scale(1);
      }
    }

.cta-container {
  position: relative;
  margin-top: 3rem;
}

/* === CYBER BUTTON === */
.cyber-button {
  position: relative;
  display: inline-block;
  padding: 1.5rem 3rem;
  background: linear-gradient(135deg, var(--primary), var(--secondary));
  color: black;
  font-family: 'Orbitron', sans-serif;
  font-weight: bold;
  text-transform: uppercase;
  letter-spacing: 3px;
  border: none;
  cursor: pointer;
  overflow: hidden;
  transition: all 0.3s ease;
  box-shadow: 0 0 20px rgba(0, 234, 255, 0.7);
  border-radius: 8px;
  text-decoration: none;
  z-index: 1;
}

.cyber-button.primary {
  padding: 1.8rem 4rem;
  font-size: 1.2rem;
  animation: pulseGlow 2s infinite ease-in-out;
}

.button-text {
  position: relative;
  z-index: 3;
}

.button-glitch {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(135deg, var(--secondary), var(--primary));
  opacity: 0;
  z-index: 2;
  animation: glitch 2s infinite linear;
}

.button-scanline {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 2px;
  background: rgba(255, 255, 255, 0.8);
  transform: translateY(-100%);
  animation: scanline 3s infinite linear;
  z-index: 4;
}

.cyber-button:hover {
  transform: translateY(-5px);
  box-shadow: 0 0 30px rgba(0, 234, 255, 0.9);
}

.cyber-button:hover .button-glitch {
  opacity: 0.2;
}

.cta-decoration {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 120%;
  height: 120%;
  pointer-events: none;
  z-index: -1;
}

.cta-decoration .circle {
  position: absolute;
  border-radius: 50%;
  border: 2px solid rgba(0, 234, 255, 0.3);
  animation: rotate 20s linear infinite;
}

.cta-decoration .circle:nth-child(1) {
  width: 100%;
  height: 100%;
  animation-delay: 0s;
}

.cta-decoration .circle:nth-child(2) {
  width: 80%;
  height: 80%;
  animation-delay: -5s;
  animation-direction: reverse;
}

.cta-decoration .circle:nth-child(3) {
  width: 60%;
  height: 60%;
  animation-delay: -10s;
}

/* === CYBER PANEL === */
.cyber-panel {
  position: fixed;
  top: 0;
  right: -450px;
  width: 400px;
  height: 100vh;
  background: rgb(10 10 30 / 0%);
  backdrop-filter: blur(4px);
  border-left: 1px solid rgba(0, 234, 255, 0.2);
  box-shadow: -5px 0 30px rgba(0, 0, 0, 0.5);
  transition: right 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
  z-index: 10;
  display: flex;
  flex-direction: column;
}

.cyber-panel.open {
  right: 0;
}

.panel-header {
  padding: 25px;
  border-bottom: 1px solid rgba(0, 234, 255, 0.2);
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.panel-header h2 {
  font-family: 'Orbitron', sans-serif;
  letter-spacing: 2px;
  color: var(--primary);
  text-shadow: 0 0 10px rgba(0, 234, 255, 0.5);
}

.close-btn {
  background: none;
  border: none;
  color: var(--text-muted);
  font-size: 24px;
  cursor: pointer;
  transition: color 0.3s ease;
}

.close-btn:hover {
  color: var(--primary);
}

.panel-content {
  flex: 1;
  padding: 25px;
  overflow-y: auto;
}

.panel-tab {
  opacity: 0;
  transition: opacity 0.3s ease;
  display: none;
}

.panel-tab h3 {
  color: var(--primary);
  margin-bottom: 20px;
  font-family: 'Orbitron', sans-serif;
  letter-spacing: 1px;
}

.panel-tab p {
  margin-bottom: 15px;
  line-height: 1.6;
}

/* === ANIMATIONS === */
@keyframes float {
  0%, 100% { transform: translateY(0px); }
  50% { transform: translateY(-20px); }
}

@keyframes pulse {
  0% { transform: scale(0.95); opacity: 0.7; }
  100% { transform: scale(1.05); opacity: 0.3; }
}

@keyframes pulseRing {
  0% { transform: scale(0.9); opacity: 1; }
  70% { transform: scale(1.3); opacity: 0; }
  100% { opacity: 0; }
}

@keyframes textGlow {
  from { text-shadow: 0 0 10px rgba(0, 234, 255, 0.7); }
  to { text-shadow: 0 0 20px rgba(0, 234, 255, 0.9), 0 0 30px rgba(255, 0, 255, 0.6); }
}

@keyframes pulseGlow {
  0%, 100% { box-shadow: 0 0 20px rgba(0, 234, 255, 0.7); transform: scale(1); }
  50% { box-shadow: 0 0 40px rgba(0, 234, 255, 0.9); transform: scale(1.05); }
}

@keyframes glitch {
  0% { transform: translate(0); }
  20% { transform: translate(-5px, 5px); }
  40% { transform: translate(-5px, -5px); }
  60% { transform: translate(5px, 5px); }
  80% { transform: translate(5px, -5px); }
  100% { transform: translate(0); }
}

@keyframes scanline {
  0% { transform: translateY(-100%); }
  100% { transform: translateY(100vh); }
}

@keyframes rotate {
  from { transform: translate(-50%, -50%) rotate(0deg); }
  to { transform: translate(-50%, -50%) rotate(360deg); }
}

@keyframes bgPulse {
  0% { opacity: 0.3; transform: scale(1); }
  50% { opacity: 0.5; transform: scale(1.05); }
  100% { opacity: 0.3; transform: scale(1); }
}

/* === RESPONSIVE ADJUSTMENTS === */
@media (max-width: 768px) {
  .cyber-sidebar {
    width: 70px;
  }
  
  .cyber-sidebar:hover {
    width: 200px;
  }
  
  .logo-container {
    width: 250px;
    height: 250px;
  }
  
  .hero-subtitle {
    font-size: 1rem;
  }
  
  .cyber-button.primary {
    padding: 1.2rem 2.5rem;
    font-size: 1rem;
  }
  
  .cyber-panel {
    width: 320px;
  }
}

@media (max-width: 480px) {
  .dashboard-main {
    padding: 20px;
  }
  
  .logo-container {
    width: 200px;
    height: 200px;
  }
  
  .hero-subtitle {
    font-size: 0.9rem;
    padding: 10px 20px;
  }
}
  </style>
</head>
<body>
  <df-messenger
    intent="WELCOME"
    chat-title="SPARKY"
    agent-id="14ce0071-20a1-4f6c-bbfa-cbe0c2e7d2a7"
    language-code="en"
  ></df-messenger>

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
            <span class="button-glitch"></span>
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
      <div id="panelContentHistory" class="panel-tab">
          <h2 style="color: #fff;">📜 Booking History</h2>
          <?php echo $booking_history_html; ?>
      </div>
      <div id="panelContentSettings" class="panel-tab">
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
      <div id="panelContentAbout" class="panel-tab">
            <h2 style="color: #fff;">⚙ Settings</h2>
      <p style="color: #ccc;">(Customize your experience...)</p>
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
          'personal': 'USER PROFILE',
          'history': 'BOOKING HISTORY', 
          'settings': 'SYSTEM SETTINGS',
          'About': 'ABOUT TEAM'
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