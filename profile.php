<?php
// file path: /htdocs/header.php
session_start();
include 'db.php';  // Assumes db.php setups the database connection
include 'fetch_settings.php';  // Assumes this script fetches app settings like app name

$settings = fetchAppSettings($conn);
$appName = $settings['app_name'] ?? 'Exercise App';  // Default name if not set in db
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($appName); ?></title>
    <link rel="stylesheet" href="style.css">
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
