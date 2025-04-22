<?php
session_start();
$conn = new mysqli("localhost", "root", "", "parkify");

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
$area_id=$_POST['area_id']?? '';
$booking_time = date('H:i:s');

$area_stmt = $conn->prepare("SELECT id FROM parkingspots WHERE name = ?");
$area_stmt->bind_param("s", $parking_name);
$area_stmt->execute();
$area_stmt->bind_result($area_id);
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
$stmt = $conn->prepare("INSERT INTO booking_history (user_id, slot_number, booking_date, booking_time, area, user_name, area_id) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("iissssi", $user_id, $slot, $date, $booking_time, $area, $user_name, $area_id);
$stmt->execute();
$stmt->close();

?>


<!DOCTYPE html>
<html>
<head>
    <title>Booking Invoice</title>
    <link rel="stylesheet" href="invoice.css">
</head>
<body>

<div class="invoice-box">
    <img src="logo.png" alt="Parkify Logo" class="logo">
    <h1>Parking Invoice</h1>

    <div class="details">
    <div class="detail-row"><span class="label">Full Name:</span> <span><?php echo htmlspecialchars($fullName); ?></span></div>
    <div class="detail-row"><span class="label">Email:</span> <span><?php echo htmlspecialchars($email); ?></span></div>
    <div class="detail-row"><span class="label">Phone:</span> <span><?php echo htmlspecialchars($phoneNo); ?></span></div>
    <div class="detail-row"><span class="label"><label>Parking Spot:</label></span> <?= htmlspecialchars($parking_name) ?></div>
  <div class="detail-row"><span class="label"><label>Area:</label></span> <?= htmlspecialchars($area) ?></div>
  <div class="detail-row"><span class="label"><label>City:</label></span> <?= htmlspecialchars($city) ?></div>
  <div class="detail-row"><span class="label"><label>Slot Number:</label></span> <?= htmlspecialchars($slot) ?></div>
  <div class="detail-row"><span class="label"><label>Time Slot:</label></span> <?= htmlspecialchars($timeSlotText) ?></div>
  <div class="detail-row"><span class="label"><label>Date:</label></span> <?= htmlspecialchars($date) ?></div>
  <div class="detail-row"><span class="label"><label>Booking Time:</label></span> <?= htmlspecialchars($booking_time) ?></div>
</div>

    <img src="qr_placeholder.png" alt="QR Code" class="qr-code"><br>
<div class="buttons">
    <button class="print-btn" onclick="window.print()">🖨️ Print Ticket</button>
    <!-- Add this where you want the button to appear in invoice.php -->
    <a href="../userboard/ub1.php">
    <button class="print-btn">🏠 Go to Home</button>
    </a>
</div>

</div>
<div id="congratsText">🎉 Congratulations! Booking Successful 🎉</div>

<canvas id="particles"></canvas>
    <div class="animated-bg"></div>
    <script>
        const canvas = document.getElementById('particles');
        const ctx = canvas.getContext('2d');
        let particles = [];
        let mouse = { x: null, y: null };

        function resizeCanvas() {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
        }

        window.addEventListener('resize', resizeCanvas);
        resizeCanvas();

        document.addEventListener('mousemove', (e) => {
            mouse.x = e.clientX;
            mouse.y = e.clientY;
        });

        class Particle {
            constructor() {
                this.reset();
            }

            reset() {
                this.x = Math.random() * canvas.width;
                this.y = Math.random() * canvas.height;
                this.size = Math.random() * 2 + 1;
                this.speedX = (Math.random() - 0.5) * 0.5;
                this.speedY = (Math.random() - 0.5) * 0.5;
                this.alpha = Math.random() * 0.5 + 0.3;
                this.color = ['#00f5ff', '#e600ff', '#7400ff'][Math.floor(Math.random() * 3)];
            }

            update() {
                this.x += this.speedX;
                this.y += this.speedY;

                // bounce
                if (this.x < 0 || this.x > canvas.width) this.speedX *= -1;
                if (this.y < 0 || this.y > canvas.height) this.speedY *= -1;
            }

            draw() {
                ctx.beginPath();
                ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
                ctx.fillStyle = this.color;
                ctx.globalAlpha = this.alpha;
                ctx.fill();
                ctx.globalAlpha = 1;
            }
        }

        function drawLines() {
            for (let i = 0; i < particles.length; i++) {
                for (let j = i + 1; j < particles.length; j++) {
                    const dx = particles[i].x - particles[j].x;
                    const dy = particles[i].y - particles[j].y;
                    const distance = Math.sqrt(dx * dx + dy * dy);

                    if (distance < 100) {
                        ctx.beginPath();
                        ctx.moveTo(particles[i].x, particles[i].y);
                        ctx.lineTo(particles[j].x, particles[j].y);
                        ctx.strokeStyle = 'rgba(255, 255, 255, 0.1)';
                        ctx.lineWidth = 1;
                        ctx.stroke();
                    }
                }

                // line from particle to cursor
                if (mouse.x && mouse.y) {
                    const dx = particles[i].x - mouse.x;
                    const dy = particles[i].y - mouse.y;
                    const dist = Math.sqrt(dx * dx + dy * dy);

                    if (dist < 120) {
                        ctx.beginPath();
                        ctx.moveTo(particles[i].x, particles[i].y);
                        ctx.lineTo(mouse.x, mouse.y);
                        ctx.strokeStyle = 'rgba(0, 255, 255, 0.2)';
                        ctx.lineWidth = 1;
                        ctx.stroke();
                    }
                }
            }
        }

        function initParticles(count) {
            particles = [];
            for (let i = 0; i < count; i++) {
                particles.push(new Particle());
            }
        }

        function animate() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            particles.forEach(p => {
                p.update();
                p.draw();
            });
            drawLines();
            requestAnimationFrame(animate);
        }

        initParticles(500);
        animate();
    </script>
    <!-- Confetti Script -->
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
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
