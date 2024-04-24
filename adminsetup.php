<?php
// Database credentials
$hostname = 'sql212.infinityfree.com';
$username = 'if0_36342993';
$password = '74EszCbbCJCwi';
$database = 'if0_36342993_Wothing';

// Create connection
$conn = new mysqli($hostname, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Admin credentials
$adminUsername = 'admin';
$adminPassword = 'admin';  // Strongly recommend changing this to a more secure password

// Hash the password
$hashedPassword = password_hash($adminPassword, PASSWORD_DEFAULT);

// SQL to insert admin user
$sql = "INSERT INTO admin_users (username, password_hash) VALUES (?, ?)";

// Prepare and bind
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $adminUsername, $hashedPassword);

// Execute the statement
if ($stmt->execute()) {
    echo "Admin user created successfully.";
} else {
    echo "Error: " . $stmt->error;
}

// Close statement and connection
$stmt->close();
$conn->close();
?>
