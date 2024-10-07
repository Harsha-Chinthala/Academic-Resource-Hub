<?php
$servername = "localhost";
$username = "root"; // or your MySQL username
$password = "root"; // or your MySQL password
$dbname = "csea(aiml)"; // Replace with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>