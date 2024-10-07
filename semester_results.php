<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "csea(aiml)";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables
$courses = [];
$sgpa = null;
$semester_name = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['semester_name'])) {
        $semester_name = $_POST['semester_name'];
    }

    if (isset($_POST['course_code']) && isset($_POST['grades'])) {
        $course_codes = $_POST['course_code'];
        $grades = $_POST['grades'];
        $total_credits = 0;
        $total_points = 0;

        foreach ($course_codes as $index => $course_code) {
            $query = "
            SELECT course_code, course_name, credits FROM foundation_courses WHERE course_code = ?
            UNION
            SELECT course_code, course_name, credits FROM independent_learning_courses WHERE course_code = ?
            UNION
            SELECT course_code, course_name, credits FROM openelective_courses WHERE course_code = ?
            UNION
            SELECT course_code, course_name, credits FROM professional_proficiency_courses WHERE course_code = ?
            UNION
            SELECT course_code, course_name, credits FROM program_core_courses WHERE course_code = ?
            ";
        
            $stmt = $conn->prepare($query);
            $params = array_fill(0, 5, $course_code);
            $stmt->bind_param(str_repeat('s', 5), ...$params);
            $stmt->execute();
            $result = $stmt->get_result();
        
            if ($result->num_rows > 0) {
                $course = $result->fetch_assoc();
                $credit = $course['credits'];
        
                $points = match ($grades[$index]) {
                    'S' => 10,
                    'A' => 9,
                    'B' => 8,
                    'C' => 7,
                    'D' => 6,
                    'RA' => 0,
                    default => 0,
                };
        
                $total_points += $points * $credit;
                $total_credits += $credit;
        
                $courses[] = [
                    'course_code' => $course['course_code'],
                    'course_name' => $course['course_name'],
                    'credits' => $course['credits'],
                    'grade' => $grades[$index]
                ];
            }
            $stmt->close();
        }

        // Calculate SGPA
        $sgpa = $total_credits > 0 ? ($total_points / $total_credits) : 0;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Semester GPA Calculator</title>
    <link rel="icon" type="image/png" href="logo.png">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        html, body {
    height: 100%;
}

body {
    display: flex;
    flex-direction: column;
}

.content {
    flex: 1; /* This allows the content to expand and push the footer to the bottom */
}
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen-Sans, Ubuntu, Cantarell, 'Helvetica Neue', sans-serif;
        }

        body {
            background-color: #f0f2f5;
            color: #333;
            line-height: 1.6;
        }

        header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
            text-align: center;
            color: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        h1 {
            font-size: 32px;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
        }

        .content {
            background: #fff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            margin: 30px auto;
            max-width: 800px;
            transition: all 0.3s ease;
        }

        .content:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .content h2 {
            font-size: 24px;
            margin-bottom: 20px;
            text-align: center;
            color: #4a4a4a;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #555;
        }

        input, select {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #ddd;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }

        input:focus, select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 2px rgba(102, 126, 234, 0.2);
        }

        button {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px 20px;
            color: white;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 16px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-top: 30px;
            overflow: hidden;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        th {
            background-color: #667eea;
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:nth-child(even) {
            background-color: #f8f9ff;
        }

        .sgpa-result {
            text-align: center;
            margin-top: 30px;
            font-size: 24px;
            font-weight: 700;
            color: #4a4a4a;
        }

        .home-button {
            position: absolute;
            top: 20px;
            left: 20px;
            background: none;
            border: none;
            cursor: pointer;
            color: #fff;
            font-size: 24px;
            transition: transform 0.3s ease;
        }

        .home-button:hover {
            transform: scale(1.1);
        }

        .semester-name {
            font-size: 20px;
            font-weight: 600;
            text-align: center;
            margin-bottom: 20px;
            color: #4a4a4a;
        }

        @media (max-width: 768px) {
            .content {
                padding: 20px;
                margin: 20px 10px;
            }

            input, select, button {
                font-size: 14px;
            }

            th, td {
                padding: 10px;
            }
            footer {
    margin-top: auto; /* Push the footer to the bottom */
}
        }
    </style>
</head>
<body>

<header>
    <button onclick="location.href='homepage.php'" class="home-button">
        <i class='bx bx-home'></i>
    </button>
    <h1>Semester GPA Calculator</h1>
</header>

<div class="content">
    <h2>Enter Semester Details</h2>
    <form id="subjectForm" method="POST">
        <div class="form-group">
            <label for="semesterName">Semester Name:</label>
            <input type="text" name="semester_name" id="semesterName" required placeholder="e.g., Summer 2023">
        </div>
        <div class="form-group">
            <label for="numSubjects">Number of Subjects:</label>
            <input type="number" name="num_subjects" id="numSubjects" required min="1" placeholder="Enter number of subjects">
        </div>
        <button type="button" onclick="generateForms()">Generate Subject Forms</button>

        <div id="dynamicForms"></div>

        <button id="submitBtn" style="display:none;" type="submit">Calculate SGPA</button>
    </form>

    <?php if (!empty($courses)): ?>
        <div class="semester-name">
            Semester: <?= htmlspecialchars($semester_name) ?>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Course Code</th>
                    <th>Course Name</th>
                    <th>Credits</th>
                    <th>Grade</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($courses as $course): ?>
                    <tr>
                        <td><?= htmlspecialchars($course['course_code']) ?></td>
                        <td><?= htmlspecialchars($course['course_name']) ?></td>
                        <td><?= htmlspecialchars($course['credits']) ?></td>
                        <td><?= htmlspecialchars($course['grade']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <?php if ($sgpa !== null): ?>
        <div class="sgpa-result">
            <h2>Your SGPA: <?= number_format($sgpa, 2) ?></h2>
        </div>
    <?php endif; ?>
</div>

<script>
    function generateForms() {
        const numSubjects = document.getElementById('numSubjects').value;
        const dynamicForms = document.getElementById('dynamicForms');
        dynamicForms.innerHTML = '';

        for (let i = 0; i < numSubjects; i++) {
            dynamicForms.innerHTML += `
                <div class="form-group">
                    <label for="courseCode${i}">Course Code:</label>
                    <input type="text" name="course_code[]" id="courseCode${i}" required placeholder="Enter course code">
                </div>
                <div class="form-group">
                    <label for="grade${i}">Grade:</label>
                    <select name="grades[]" id="grade${i}" required>
                        <option value="">Select Grade</option>
                        <option value="S">S</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                        <option value="D">D</option>
                        <option value="RA">RA</option>
                    </select>
                </div>
            `;
        }

        document.getElementById('submitBtn').style.display = 'block';
    }
</script>
<footer style="background-color: #667eea; color: white; text-align: center; padding: 20px;">
    <p>&copy; <?php echo date("Y"); ?> Vel Tech University. All rights reserved.</p>
</footer>

</body>
</html>