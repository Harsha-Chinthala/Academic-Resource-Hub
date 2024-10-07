<?php
// Start session
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "csea(aiml)";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables
$error = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validate email format (backend validation in addition to JS)
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/@veltech\.edu\.in$/', $email)) {
        $error = "Invalid email address!";
    } else {
        // Check if the email already exists
        $checkEmail = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $checkEmail->bind_param("s", $email);
        $checkEmail->execute();
        $checkEmail->store_result();

        if ($checkEmail->num_rows > 0) {
            // Email already exists
            $error = "This email is already registered. Please use a different email.";
        } else {
            // Insert the new user into the database
            $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $email, $password);

            // Execute query and check for errors
            if ($stmt->execute()) {
                // Registration successful, redirect to acknowledgment page
                header("Location: acknowledgement.html");
                exit();
            } else {
                // Display error message
                $error = "Error: Could not register user.";
            }

            // Close statement
            $stmt->close();
        }

        // Close the email check
        $checkEmail->close();
    }
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register Form</title>
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

    .button-nomad {
      background: #FF4742;
      border: 1px solid #FF4742;
      border-radius: 6px;
      box-shadow: rgba(0, 0, 0, 0.1) 1px 2px 4px;
      color: #FFFFFF;
      cursor: pointer;
      font-family: nunito, roboto, proxima-nova, "proxima nova", sans-serif;
      font-size: 16px;
      font-weight: 800;
      padding: 12px 14px;
      width: 100%;
      margin-top: 20px;
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
      display: block;
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

  <!-- Register Form -->
  <div class="wrapper" id="register-section">
    <img src="logo.png" alt="Institute Logo" class="logo">
    <h1>Register</h1>
    <form id="registerForm" action="register.php" method="POST">
      <div class="input-box">
        <input type="text" id="username" name="username" placeholder="Username" required>
        <i class='bx bxs-user'></i>
      </div>
      <div class="input-box">
        <input type="email" id="email" name="email" placeholder="Email" required>
        <i class='bx bxs-envelope'></i>
      </div>
      <div class="input-box">
        <input type="password" id="password" name="password" placeholder="Password" required>
        <i class='bx bxs-lock-alt' id="togglePassword" onclick="togglePassword()"></i>
      </div>
      <button type="submit" class="button-nomad">Register</button>
      <?php if (!empty($error)): ?>
        <div class="error"><?php echo $error; ?></div>
      <?php endif; ?>
      <div class="register-link">
        <p>Already have an account? <a href="index.php">Login</a></p>
      </div>
    </form>
  </div>

  <script>
    // Validate the registration form
    function validateForm() {
      const emailInput = document.getElementById('email').value.trim();
      const errorMessage = document.getElementById('error-message');
      const emailPattern = /^[a-zA-Z0-9._%+-]+@veltech\.edu\.in$/; // Ensures it ends with @veltech.edu.in

      if (!emailPattern.test(emailInput)) {
        errorMessage.style.display = 'block'; // Show error message
        return false; // Prevent form submission
      }

      errorMessage.style.display = 'none'; // Hide error message
      return true; // Allow form submission
    }

    // Ensure form calls validateForm on submit
    document.getElementById('registerForm').onsubmit = function(event) {
      if (!validateForm()) {
        event.preventDefault(); // Prevent form submission if invalid
      }
    };

    // Toggle password visibility
    function togglePassword() {
      const passwordInput = document.getElementById('password');
      const toggleIcon = document.getElementById('togglePassword');

      if (passwordInput.type === "password") {
        passwordInput.type = "text"; // Change to text to show password
        toggleIcon.classList.remove('bxs-lock-alt'); // Change icon
        toggleIcon.classList.add('bxs-lock-open-alt'); // Change to open lock icon
      } else {
        passwordInput.type = "password"; // Change back to password
        toggleIcon.classList.remove('bxs-lock-open-alt'); // Change icon back
        toggleIcon.classList.add('bxs-lock-alt'); // Change to locked icon
      }
    }
  </script>

</body>
</html>