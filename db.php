<?php

// file path: /htdocs/db.php

$servername = "sql212.infinityfree.com";
$username = "if0_36342993";
$password = "74EszCbbCJCwi";
$dbname = "if0_36342993_Wothing";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
