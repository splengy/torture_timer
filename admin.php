<?php
// file path: /htdocs/admin.php
session_start();
require 'db.php';  // This should link to your database connection script

// CSRF token generation and check
if (empty($_SESSION['token'])) {
    $_SESSION['token'] = bin2hex(random_bytes(32));
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();  // Destroy all session data
    header('Location: admin.php');  // Redirect to login page
    exit();
}

// Process login
$login_error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['username'], $_POST['password'])) {
    if (!empty($_POST['token']) && hash_equals($_SESSION['token'], $_POST['token'])) {  // CSRF token check
        $username = $_POST['username'];
        $password = $_POST['password'];

        $stmt = $conn->prepare("SELECT password_hash FROM admin_users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row['password_hash'])) {
                $_SESSION['admin_logged_in'] = true;
            } else {
                $login_error = 'Incorrect username or password.';
            }
        } else {
            $login_error = 'Incorrect username or password.';
        }
        $stmt->close();
    } else {
        $login_error = 'Invalid request. Please try again.';
    }
}

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    echo '<form action="admin.php" method="post">
            Username: <input type="text" name="username" required><br>
            Password: <input type="password" name="password" required><br>
            <input type="hidden" name="token" value="' . htmlspecialchars($_SESSION['token']) . '">
            <input type="submit" value="Login">
          </form>';
    if (!empty($login_error)) {
        echo "<p>$login_error</p>";
    }
    exit;
}

// Process settings update
$message = '';
if (isset($_POST['update_settings'], $_POST['token']) && hash_equals($_SESSION['token'], $_POST['token'])) {
    $appName = $_POST['app_name'];
    $headerImageUrl = $_POST['header_image_url'];
    $backgroundImageUrl = $_POST['background_image_url'];

    $stmt = $conn->prepare("REPLACE INTO app_settings (setting_key, setting_value) VALUES 
                            ('app_name', ?), 
                            ('header_image_url', ?), 
                            ('background_image_url', ?)");
    $stmt->bind_param("sss", $appName, $headerImageUrl, $backgroundImageUrl);
    $stmt->execute();
    $stmt->close();

    $message = "Settings updated successfully.";
}

// Fetch current settings
$currentSettings = [];
$result = $conn->query("SELECT setting_key, setting_value FROM app_settings");
while ($row = $result->fetch_assoc()) {
    $currentSettings[$row['setting_key']] = $row['setting_value'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel - Exercise App</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            background-image: url('<?php echo htmlspecialchars($currentSettings['background_image_url'] ?? ''); ?>');
            background-size: cover;
            background-position: center;
        }
        .header-image {
            height: 100px;
            background-image: url('<?php echo htmlspecialchars($currentSettings['header_image_url'] ?? ''); ?>');
            background-repeat: no-repeat;
            background-size: contain;
            background-position: center;
        }
    </style>
</head>
<body>
	
	
	
    <div class="container">
        <h1>Admin Panel</h1>
        <!--<div class="header-image"></div> -->
		<li><a href="admin_exercises.php">Manage Exercises</a></li>
		<li><a href="admin_workouts.php">Manage workouts</a></li>
		<li><a href="admin_warmups.php">Manage warmups</a></li>
        <a href="?logout=true">Logout</a>
        <?php if (!empty($message)) echo "<p>$message</p>"; ?>

        <form action="admin.php" method="post">
            <label for="app-name">App Name:</label>
            <input type="text" id="app-name" name="app_name" value="<?php echo htmlspecialchars($currentSettings['app_name'] ?? 'Default App Name'); ?>">
            <label for="header-image-url">Header Image URL:</label>
            <input type="text" id="header-image-url" name="header_image_url" value="<?php echo htmlspecialchars($currentSettings['header_image_url'] ?? ''); ?>"><br>
            <label for="background-image-url">Background Image URL:</label>
            <input type="text" id="background-image-url" name="background_image_url" value="<?php echo htmlspecialchars($currentSettings['background_image_url'] ?? ''); ?>"><br>
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($_SESSION['token']); ?>">
            <input type="submit" name="update_settings" value="Update Settings">
        </form>
    </div>
</body>
</html>
