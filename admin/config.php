<?php
// Connect to the database
$conn = new mysqli("localhost", "root", "", "realstate_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>