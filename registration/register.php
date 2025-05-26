<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Parkify</title>
  <link rel="stylesheet" href="reg.css" />
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
    integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
    crossorigin="anonymous"
    referrerpolicy="no-referrer"
  />
  <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@700&family=Michroma&family=Montserrat:wght@300;400;600&display=swap" rel="stylesheet">
      <link rel="shortcut icon" href="car.ico" type="image/x-icon">

</head>
<body>

  
  <script src="https://www.gstatic.com/dialogflow-console/fast/messenger/bootstrap.js?v=1"></script>
  <df-messenger
    intent="WELCOME"
    chat-title="SPARKY"
    agent-id="15538dd3-8130-4a97-a1dc-26b4854c4880"
    language-code="en"
  ></df-messenger>
  
    <div class="top">
      <header class="hero">
        <div class="logo-container">
          <img src="logo.png" class="logo-img" alt="Parkify Logo" />
          <div class="logo-glow"></div>
          <div class="logo-pulse"></div>
        </div>
        <h2 class="hero-subtitle">SMART, EFFORTLESS & SECURE PARKING, ANYTIME</h2>
      </header>
    </div>
    
    <form action="ifok.php" method="POST" class="login-form">
      <div class="form-container">
        <div class="input-group">
          <input type="text" name="userName" id="userName" required />
          <label for="userName">USERNAME</label>
          <div class="input-highlight"></div>
        </div>
        
        <div class="input-group">
          <input type="password" name="password" id="password" required />
          <label for="password">PASSWORD</label>
          <div class="input-highlight"></div>
        </div>
        
        <p class="forgot-password"><a href="..\forgetpassword page\fg.php">Forgot Password?</a></p>
        
        <button id="submit" class="cyber-button">
          <span class="cyber-button-text">SUBMIT</span>
        </button>
      </div>
      
      <div class="signup-section">
        <p class="signup">
          <span class="new">NEW USER?</span>
          <a href="..\userName\userName.php" class="signup-link">SIGN UP HERE</a>
        </p>
        
        <span class="or">OR LOGIN USING</span>
        
        <div class="social-icons">
          <a href="https://google.com/" target="_blank" class="social-icon"><i class="fa-brands fa-google"></i></a>
          <a href="https://facebook.com/" target="_blank" class="social-icon"><i class="fa-brands fa-facebook-f"></i></a>
          <a href="https://apple.com/" target="_blank" class="social-icon"><i class="fa-brands fa-apple"></i></a>
        </div>
      </div>
    </form>
    
    <a href="../admin/admin_login.php" class="admin-button">
      <button id="admin" class="cyber-button small">
        <span class="cyber-button-text">ADMIN</span>
      </button>
    </a>

  
  <div class="corner-decoration top-left"></div>
  <div class="corner-decoration top-right"></div>
  <div class="corner-decoration bottom-left"></div>
  <div class="corner-decoration bottom-right"></div>
  
    <!-- Canvas background moved to top of body -->
  <canvas id="particles"></canvas>
  <script src="particles.js"></script>
  <script src="animations.js"></script>
</body>
</html>