<?php
// file path: /htdocs/register.php
include 'header.php'; // Includes fetching settings via fetch_settings.php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Handle form submission
    $username = $conn->real_escape_string($_POST['username']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = $conn->real_escape_string($_POST['password']);
    $password_verify = $conn->real_escape_string($_POST['password_verify']);
    $height = floatval($_POST['height']);
    $weight = floatval($_POST['weight']);
    $age = intval($_POST['age']);
    $gender = $conn->real_escape_string($_POST['gender']);

    // Check if passwords match
    if ($password != $password_verify) {
        echo "<p>Passwords do not match.</p>";
        exit();
    }

    // Check for existing username or email
    $checkUser = "SELECT id FROM users WHERE username = '$username' OR email = '$email'";
    $userResult = $conn->query($checkUser);

    if ($userResult->num_rows > 0) {
        echo "<p>Username or email already exists.</p>";
        exit();
    } 

    // Insert new user
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO users (username, email, password_hash, height, weight, age, gender) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("sssdidi", $username, $email, $password_hash, $height, $weight, $age, $gender);
        if ($stmt->execute()) {
            echo "<div class='container'>
                    <h1>Thank You for Registering!</h1>
                    <p>You will be <a href='index.php'>redirected</a> to the login page shortly. If you do not wish to wait, click <a href='index.php'>here</a>.</p>
                    <img src='" . htmlspecialchars($settings['image_url']) . "' alt='Thank You' style='width:100%; max-width:600px;'>
                  </div>";
            header("Refresh:30; url=index.php");
            exit();
        } else {
            echo "<p>Error: " . htmlspecialchars($stmt->error) . "</p>";
        }
        $stmt->close();
    } else {
        echo "<p>Error preparing statement: " . htmlspecialchars($conn->error) . "</p>";
    }

    $conn->close();
}
?>
</body>
</html>
