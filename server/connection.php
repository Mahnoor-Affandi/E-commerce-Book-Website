<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "final-project";

// Create connection
$conn = new mysqli("localhost", "root", "", "final-project");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>