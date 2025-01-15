<?php
$servername = "localhost"; // Your server
$username = "root";       // Your username
$password = "";           // Your password
$dbname = "Yoxo6";        // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>


