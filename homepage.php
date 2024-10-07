<?php
session_start(); // Start the session

// Check if the user is logged in; if not, redirect to the login page
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Academic Resource Hub</title>
    <link rel="icon" type="image/png" href="logo.png">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap');

        :root {
            --bg-light: #F0F4FC;  /* Light blue background */
            --text-light: #3E2723;  /* Dark maroon text for contrast */
            --primary-light: #1565C0;  /* Blue as primary color */
            --secondary-light: #D32F2F;  /* Maroon as secondary color */
            --header-bg-light: #BBDEFB;  /* Lighter blue for header */
            --bg-dark: #2C2721;  /* Dark maroon for dark mode */
            --text-dark: #E3F2FD;  /* Light blue text for dark mode */
            --primary-dark: #1565C0;  /* Keep blue as primary */
            --secondary-dark: #E53935;  /* Lighter maroon for dark mode */
            --header-bg-dark: #1A237E;  /* Darker blue for header in dark mode */
        }

        @media (prefers-color-scheme: light) {
            :root {
                --bg: var(--bg-light);
                --text: var(--text-light);
                --primary: var(--primary-light);
                --secondary: var(--secondary-light);
                --header-bg: var(--header-bg-light);
            }
        }

        @media (prefers-color-scheme: dark) {
            :root {
                --bg: var(--bg-dark);
                --text: var(--text-dark);
                --primary: var(--primary-dark);
                --secondary: var(--secondary-dark);
                --header-bg: var(--header-bg-dark);
            }
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen-Sans, Ubuntu, Cantarell, 'Helvetica Neue', sans-serif;
            transition: background-color 0.3s, color 0.3s;
        }

        body {
            background-color: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            line-height: 1.6;
        }

        header {
            background: var(--header-bg);
            padding: 20px;
            text-align: center;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        h1 {
            font-size: 28px;
            font-weight: 700; /* Changed to bold */
            color: #FFFFFF; /* White color */
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
            animation: glow 2s ease-in-out infinite alternate;
        }

        @keyframes glow {
            from {
                text-shadow: 0 0 5px var(--primary), 0 0 10px var(--primary);
            }
            to {
                text-shadow: 0 0 10px var(--primary), 0 0 20px var(--primary);
            }
        }

        nav {
            margin: 0 20px;
        }

        nav a {
            color: var(--text);
            text-decoration: none;
            margin: 0 15px;
            font-size: 16px;
            padding: 8px 12px;
            border-radius: 5px;
            transition: all 0.3s;
            position: relative;
            font-weight: 500;
        }

        nav a:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }

        nav a::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--primary);
            transition: width 0.3s;
        }

        nav a:hover::after {
            width: 100%;
        }

        .content {
            flex-grow: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 40px;
            background: url('https://source.unsplash.com/1600x900/?library,education') no-repeat center center fixed;
            background-size: cover;
            position: relative;
        }

        .content::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(240, 244, 252, 0.8);  /* Light blue overlay */
        }

        .content-inner {
            position: relative;
            z-index: 1;
            background: rgba(255, 255, 255, 0.9);  /* Slightly transparent white */
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            width: 100%;
            animation: fadeIn 1s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .content h2 {
            font-size: 32px;
            margin-bottom: 20px;
            color: var(--primary);
            font-weight: 700;
        }

        .content p {
            font-size: 18px;
            margin-bottom: 30px;
            line-height: 1.6;
            color: #3E2723;;
        }

        .btn {
            background: var(--primary);
            border: none;
            padding: 12px 24px;
            font-size: 18px;
            color: white;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
            font-weight: 500;
        }

        .btn:hover {
            background: var(--secondary);
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(21, 101, 192, 0.3);
        }

        footer {
            text-align: center;
            padding: 15px;
            background: var(--header-bg);
            margin-top: auto;
            font-size: 14px;
        }

        .user-info {
        display: flex;
        align-items: center; /* Center items vertically */
        gap: 10px; /* Space between icon, username, and logout button */
    }

    .user-info strong {
        font-size: 16px; /* Ensure the username font size is consistent */
    }

    .logout-btn {
        background: none;
        border: none;
        cursor: pointer;
        color: var(--primary);
        font-size: 24px;
        transition: color 0.1s, transform 0.3s;
        display: flex;
        align-items: center; /* Center icon in button */
    }

    .logout-btn:hover {
        color: var(--secondary);
        transform: scale(1.1);
    }

    /* Responsive adjustments */
    @media screen and (max-width: 768px) {
        h1 {
            font-size: 24px;
        }

        nav a {
            margin: 0 10px;
            font-size: 14px;
        }

        .content h2 {
            font-size: 28px;
        }

        .content p {
            font-size: 16px;
        }

        .btn {
            padding: 10px 20px;
            font-size: 16px;
        }

        .logout-btn {
            font-size: 20px;
        }
    }
</style>
</head>
<body>

<header>
    <h1>Academic Resource Hub</h1>
    <nav>
        <a href="college_urls.html">Quick Links</a>
        <a href="semester_results.php">GPA Calculator</a>
        <a href="about.html">About Us</a>
        <a href="contact.html">Contact</a>
    </nav>
    <div class="user-info">
        <i class='bx bx-user' style="font-size: 24px;"></i> <!-- Profile Icon -->
        <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>
        <button class="logout-btn" onclick="location.href='logout.php'" title="Logout">
            <i class='bx bx-log-out'></i> 
        </button>
    </div>
</header>

<div class="content">
        <div class="content-inner">
            <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
            <p>This platform is designed to assist you in your academic journey by providing essential resources and information.</p>
            <p>Explore our offerings and take charge of your academic progress!</p>
            <a href="resources.html" class="btn">Explore Resources</a>
        </div>
    </div>
    <footer>
        <p>&copy; <?php echo date("Y"); ?> Academic Resource Hub. All Rights Reserved.</p>
    </footer>
</body>
</html>