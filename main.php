<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Main Page - Exercise App</title>
    <link rel="stylesheet" href="style.css">
    <script src="script.js" defer></script>
</head>
<body>
    <div class="container">
        <h1>Main Dashboard</h1>
        <p>Welcome to your main dashboard. Here, you can manage your workouts, goals, and achievements.</p>

        <!-- Workout Widget -->
        <div class="widget">
            <h2>Your Workouts</h2>
            <p>Track and manage your workouts.</p>
            <button onclick="location.href='workouts.php'">View Workouts</button>
        </div>

        <!-- Goals Widget -->
        <div class="widget">
            <h2>Your Goals</h2>
            <p>Set and track your fitness goals.</p>
            <button onclick="location.href='goals.php'">View Goals</button>
        </div>

        <!-- Achievements Widget -->
        <div class="widget">
            <h2>Your Achievements</h2>
            <p>View your achievements and milestones.</p>
            <button onclick="location.href='achievements.php'">View Achievements</button>
        </div>
    </div>
</body>
</html>
