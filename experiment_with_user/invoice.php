<?php
session_start();
$conn = new mysqli("sql105.infinityfree.com", "if0_39017725", "jeZyqYSlUAhhmM", "if0_39017725_parkify_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
if (!isset($_SESSION['user_id'])) {
    die("User not logged in.");
}

$user_id = $_SESSION['user_id'];

$user_sql = "SELECT firstName, lastName, email, phoneNo FROM user_form WHERE id = ?";
$user_stmt = $conn->prepare($user_sql);
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_stmt->bind_result($firstName, $lastName, $email, $phoneNo);
$user_stmt->fetch();
$user_stmt->close();

$fullName = $firstName . ' ' . $lastName;

$parking_name = $_POST['parking_name'] ?? '';
$area = $_POST['area'] ?? '';
$city = $_POST['city'] ?? '';
$slot = $_POST['slot'] ?? '';
$area = $_POST['area'] ?? '';
$user_name=$_POST['username'] ?? '';
$parking_name = $_POST['parking_name'] ?? '';
$slot_time = $_POST['timeSlotText'] ?? '';
$date = $_POST['date'] ?? '';
$area=$_POST['area']?? '';
// $area_id=$_POST['area_id']?? '';
$booking_time = date('H:i:s');

$area_stmt = $conn->prepare("SELECT id FROM parkingspots WHERE name = ?");
$area_stmt->bind_param("s", $parking_name);
$area_stmt->execute();
$area_stmt->bind_result($area);
// $area_stmt->bind_result($area_id);
$area_stmt->fetch();
$area_stmt->close();
function getTimeSlot($slot) {
    $slots = [
        1 => "7:00 AM – 8:00 AM",
        2 => "8:00 AM – 9:00 AM",
        3 => "9:00 AM – 10:00 AM",
        4 => "10:00 AM – 11:00 AM",
        5 => "11:00 AM – 12:00 PM",
        6 => "12:00 PM – 1:00 PM",
        7 => "1:00 PM – 2:00 PM",
        8 => "2:00 PM – 3:00 PM",
        9 => "3:00 PM – 4:00 PM",
        10 => "4:00 PM – 5:00 PM",
        11 => "5:00 PM – 6:00 PM"
    ];
    return $slots[$slot] ?? "Unknown Slot";
}

$timeSlotText = getTimeSlot($slot);
$stmt = $conn->prepare("INSERT INTO booking_history (user_id, slot_number, booking_date, booking_time, area, user_name) 
                        VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("iissss", $user_id, $slot, $date, $booking_time, $area, $user_name);
// $stmt = $conn->prepare("INSERT INTO booking_history (user_id, slot_number, booking_date, booking_time, area, user_name, area_id) 
//                         VALUES (?, ?, ?, ?, ?, ?, ?)");
// $stmt->bind_param("iissssi", $user_id, $slot, $date, $booking_time, $area, $user_name, $area_id);
$stmt->execute();
$stmt->close();

?>


<!DOCTYPE html>
<html>
<head>
    <title>Parkify - Booking Invoice</title>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500&family=Montserrat:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="invoice.css">
          <link rel="shortcut icon" href="../registration/car.ico" type="image/x-icon">

</head>
<body>
  <!-- Background Elements -->
  <canvas id="particles"></canvas>
  <div class="animated-bg"></div>

  <div class="invoice-container">
    <div class="invoice-header">
      <div class="logo-header">
        <img src="logo.png" alt="Parkify Logo" class="logo">
        <div class="invoice-title">
          <h1>PARKING INVOICE</h1>
          <p>Transaction #<?= substr(md5(uniqid()), 0, 8) ?></p>
        </div>
      </div>
      <div class="invoice-meta">
        <p><strong>Issued:</strong> <?= date('F j, Y') ?></p>
        <p><strong>Due:</strong> <?= date('F j, Y') ?></p>
      </div>
    </div>

    <div class="divider"></div>

    <div class="invoice-body">
      <div class="client-info">
        <h2><i class="fas fa-user-tie"></i> CLIENT DETAILS</h2>
        <div class="info-grid">
          <div><strong>Name:</strong> <?php echo htmlspecialchars($fullName); ?></div>
          <div><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></div>
          <div><strong>Phone:</strong> <?php echo htmlspecialchars($phoneNo); ?></div>
        </div>
      </div>

      <div class="booking-details">
        <h2><i class="fas fa-parking"></i> BOOKING SUMMARY</h2>
        <table class="details-table">
          <tr>
            <th>Description</th>
            <th>Details</th>
          </tr>
          <tr>
            <td>Parking Location</td>
            <td><?= htmlspecialchars($parking_name) ?>, <?= htmlspecialchars($area) ?>, <?= htmlspecialchars($city) ?></td>
          </tr>
          <tr>
            <td>Reservation Date</td>
            <td><?= htmlspecialchars($date) ?></td>
          </tr>
          <tr>
            <td>Time Slot</td>
            <td><?= htmlspecialchars($timeSlotText) ?> (Slot <?= htmlspecialchars($slot) ?>)</td>
          </tr>
          <tr>
            <td>Booking Reference</td>
            <td>PKF-<?= substr(md5(uniqid()), 0, 8) ?></td>
          </tr>
        </table>
      </div>

      <div class="payment-summary">
        <h2><i class="fas fa-receipt"></i> PAYMENT</h2>
        <table class="payment-table">
          <tr>
            <td>Parking Fee (1 hour)</td>
            <td>₹50.00</td>
          </tr>
          <tr>
            <td>Service Charge</td>
            <td>₹5.00</td>
          </tr>
          <tr class="total-row">
            <td><strong>TOTAL PAID</strong></td>
            <td><strong>₹55.00</strong></td>
          </tr>
        </table>
      </div>

      <div class="qr-section">
        <img src="qr_placeholder.png" alt="QR Code" class="qr-code">
        <p>Scan QR for digital verification</p>
      </div>
    </div>

    <div class="divider"></div>

    <div class="invoice-footer">
      <div class="footer-actions">
        <button onclick="window.print()" class="print-btn">
          <i class="fas fa-print"></i> Print Invoice
        </button>
        <a href="../userboard/ub1.php" class="home-btn">
          <i class="fas fa-home"></i> Return Home
        </a>
      </div>
      <div class="footer-note">
        <p>Thank you for choosing Parkify! This is an automated invoice - no signature required.</p>
      </div>
    </div>
  </div>

  <div id="congratsText">🎉 Congratulations! Booking Successful 🎉</div>

  <!-- Confetti Script -->
  <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
  <script src="particles.js"></script>
  <script>
    window.onload = function() {
        const congrats = document.getElementById("congratsText");
        congrats.style.display = "block";
        setTimeout(() => {
            congrats.style.display = "none";
        }, 3000);

        // Run confetti for 2 seconds
        const duration = 2 * 1000;
        const end = Date.now() + duration;

        (function frame() {
            confetti({
                particleCount: 5,
                angle: 60,
                spread: 100,
                origin: { x: 0 },
                colors: ['#00f5ff', '#e600ff', '#7400ff']
            });
            confetti({
                particleCount: 5,
                angle: 120,
                spread: 100,
                origin: { x: 1 },
                colors: ['#00f5ff', '#e600ff', '#7400ff']
            });

            if (Date.now() < end) {
                requestAnimationFrame(frame);
            }
        }());
    };
  </script>
</body>
</html>