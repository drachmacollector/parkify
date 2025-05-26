<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Parkify - User Registration</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link rel="stylesheet" href="userName.css" />
          <link rel="shortcut icon" href="../registration/car.ico" type="image/x-icon">

  <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@600&family=Rajdhani:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>
  <div class="container">
    <h1>User Registration</h1>
    <form action="save_form.php" method="POST" enctype="multipart/form-data">
      
      <div class="form-group">
        <label>Create a Username</label>
        <input type="text" name="Username">
      </div>

      <div class="form-row">
  <div class="form-group">
    <label>First Name</label>
    <input type="text" name="firstName">
  </div>

  <div class="form-group">
    <label>Last Name</label>
    <input type="text" name="lastName">
  </div>
</div>


      <div class="form-group">
        <label>Phone Number</label>
        <input type="text" name="phoneNo">
      </div>

      <div class="form-group">
        <label>Email</label>
        <input type="text" name="email">
      </div>

      <div class="form-group">
        <label>State</label>
        <input type="text" name="state">
      </div>

      <div class="form-group">
        <label>City</label>
        <input type="text" name="city">
      </div>

      <div class="form-group">
        <label>Address 1 (optional)</label>
        <input type="text" name="address1">
      </div>

      <div class="form-group">
        <label>Address 2 (optional)</label>
        <input type="text" name="address2">
      </div>

      <div class="form-group">
        <label>Date of Birth</label>
        <input type="date" name="dob" placeholder="dd/mm/yy">
      </div>

      <div class="form-group">
        <label>Aadhar Number (optional)</label>
        <input type="text" name="aadharNumber">
      </div>

      <div class="form-group">
        <label>Insert Aadhar Card (optional)</label>
        <input type="file" name="aadharFile">
      </div>

      <div class="form-group">
        <label>Car Number (optional)</label>
        <input type="text" name="carNumber">
      </div>

      <div class="form-group">
        <label>Driving Licence Number (optional)</label>
        <input type="text" name="dlNumber">
      </div>

      <div class="form-group">
        <label>Driving Licence Picture (optional)</label>
        <input type="file" name="dlFile">
      </div>

      <button type="submit">Submit</button>
    </form>
  </div>
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
</body>
</html>
