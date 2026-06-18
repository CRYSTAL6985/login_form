<?php
// Database connection settings - edit these to match your setup
$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "final_project";

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
    