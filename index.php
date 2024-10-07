<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Initialize error message
$errorMessage = "";

// Database connection
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "csea(aiml)";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['email']) && isset($_POST['password'])) {
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);
        
        // Prepare and bind
        $stmt = $conn->prepare("SELECT username, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        
        // Execute the statement
        $stmt->execute();
        
        // Store the result
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Fetch the user's data
            $user = $result->fetch_assoc();

            // Check if the password is correct
            if ($user['password'] === $password) {
                // Set session variables
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $email;

                // Redirect to homepage
                header("Location: homepage.php");
                exit();
            } else {
                // Incorrect password, set error message
                $errorMessage = "Incorrect password.";
            }
        } else {
            // Email not found, set error message
            $errorMessage = "Email not found.";
        }

        // Close the statement
        $stmt->close();
    } else {
        $errorMessage = "Email or password not set.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Form</title>
  <link rel="icon" type="image/png" href="logo.png">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      background: url('veltech.jpg') no-repeat;
      background-size: cover;
      background-position: center;
      font-family: nunito, roboto, proxima-nova, "proxima nova", sans-serif;
    }

    .wrapper {
      width: 420px;
      background: rgba(0, 0, 0, 0.7);
      border: 2px solid rgba(255, 255, 255, .2);
      backdrop-filter: blur(9px);
      color: #fff;
      border-radius: 12px;
      padding: 30px 40px;
      text-align: center;
    }

    h1 {
      font-size: 36px;
      text-align: center;
    }

    .input-box {
      position: relative;
      width: 100%;
      height: 50px;
      margin: 30px 0;
    }

    .input-box input {
      width: 100%;
      height: 100%;
      background: transparent;
      border: none;
      outline: none;
      border: 2px solid rgba(255, 255, 255, .2);
      border-radius: 40px;
      font-size: 16px;
      color: #fff;
      padding: 20px 45px 20px 20px;
    }

    .input-box input::placeholder {
      color: #fff;
    }

    .input-box i {
      position: absolute;
      right: 20px;
      top: 30%;
      transform: translate(-50%);
      font-size: 20px;
      cursor: pointer;
    }

    .remember-forgot {
      display: flex;
      justify-content: space-between;
      font-size: 14.5px;
      margin: -15px 0 15px;
    }

    .remember-forgot label input {
      accent-color: #fff;
      margin-right: 3px;
    }

    .remember-forgot a {
      color: #fff;
      text-decoration: none;
    }

    .remember-forgot a:hover {
      text-decoration: underline;
    }

    .button-nomad {
      background: #FF4742;
      border: 1px solid #FF4742;
      border-radius: 6px;
      box-shadow: rgba(0, 0, 0, 0.1) 1px 2px 4px;
      box-sizing: border-box;
      color: #FFFFFF;
      cursor: pointer;
      display: inline-block;
      font-family: nunito, roboto, proxima-nova, "proxima nova", sans-serif;
      font-size: 16px;
      font-weight: 800;
      line-height: 16px;
      min-height: 40px;
      outline: 0;
      padding: 12px 14px;
      text-align: center;
      text-rendering: geometricprecision;
      text-transform: none;
      user-select: none;
      touch-action: manipulation;
      vertical-align: middle;
      width: 100%;
    }

    .button-nomad:hover,
    .button-nomad:active {
      background-color: initial;
      background-position: 0 0;
      color: #FF4742;
    }

    .button-nomad:active {
      opacity: .5;
    }

    .error {
      color: red;
      font-size: 14px;
      margin-top: 10px;
      display: <?php echo !empty($errorMessage) ? 'block' : 'none'; ?>; /* Show only if there's an error */
    }

    .register-link {
      font-size: 14.5px;
      text-align: center;
      margin: 20px 0 15px;
    }

    .register-link p a {
      color: #fff;
      text-decoration: none;
      font-weight: 600;
    }

    .register-link p a:hover {
      text-decoration: underline;
    }

    img.logo {
      width: 100px;
      margin-bottom: 20px;
    }
  </style>
</head>
<body>

  <!-- Login Form -->
  <div class="wrapper" id="login-section">
    <img src="logo.png" alt="Institute Logo" class="logo">
    <h1>Login</h1>
    <form id="loginForm" action="" method="POST">
      <div class="input-box">
        <input type="text" id="email" name="email" placeholder="Email" required>
        <i class='bx bxs-user'></i>
      </div>
      <div class="input-box">
        <input type="password" id="password" name="password" placeholder="Password" required>
        <i class='bx bxs-lock-alt' id="togglePassword" onclick="togglePassword()"></i>
      </div>
      <div class="remember-forgot">
        <label><input type="checkbox"> Remember Me</label>
        <a href="forgot_password.html">Forgot Password</a>
      </div>
      <button type="submit" class="button-nomad">Login</button>
      <div class="error"><?php echo $errorMessage; ?></div> <!-- Display error message if set -->
      <div class="register-link">
        <p>Don't have an account? <a href="register.php">Register</a></p>
      </div>
    </form>
  </div>

  <script>
    // Toggle password visibility
    function togglePassword() {
      const passwordInput = document.getElementById('password');
      const toggleIcon = document.getElementById('togglePassword');

      if (passwordInput.type === "password") {
        passwordInput.type = "text";
        toggleIcon.classList.remove('bxs-lock-alt');
        toggleIcon.classList.add('bxs-lock-open-alt');
      } else {
        passwordInput.type = "password";
        toggleIcon.classList.remove('bxs-lock-open-alt');
        toggleIcon.classList.add('bxs-lock-alt');
      }
    }
  </script>

</body>
</html>