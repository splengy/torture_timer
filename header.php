<?php
// file path: /htdocs/header.php
session_start();
include 'db.php';  // Assumes db.php sets up the database connection
include 'fetch_settings.php';  // Assumes this script fetches app settings

$settings = fetchAppSettings($conn);
$appName = $settings['app_name'] ?? 'Exercise App';  // Default app name if not set in db
$headerImageUrl = $settings['header_image_url'] ?? '';  // Default header image URL if not set
$backgroundImageUrl = $settings['background_image_url'] ?? '';  // Default background image URL if not set
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($appName); ?></title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            background-image: url('<?php echo htmlspecialchars($backgroundImageUrl); ?>');
            background-size: cover;
            background-position: center;
        }
        header {
            background-image: url('<?php echo htmlspecialchars($headerImageUrl); ?>');
            background-size: cover;
            background-position: center;
            height: 150px; /* Adjust the height as needed */
        }
    </style>
    <script src="script.js" defer></script>
</head>
<body>
    <header>
        <div id="hamburger-menu" onclick="toggleMenu()">☰</div>
        <div id="header-info"><?php echo htmlspecialchars($appName); ?></div>
        <div id="current-time"></div>
    </header>
    <nav id="side-menu" style="display:none;">
        <ul>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="profile.php">My Profile</a></li>
                <li><a href="logout.php">Logout</a></li>
            <?php else: ?>
                <li><a href="#" onclick="toggleLoginForm(); return false;">Login</a></li>
                <li><a href="#" onclick="toggleRegisterForm(); return false;">Register</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</body>
</html>
