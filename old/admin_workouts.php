<?php
// file path: /htdocs/admin_workouts.php
include 'header.php'; // Include the header

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin.php'); // Redirect to the login page if not logged in
    exit;
}

require 'db.php';  // Database connection

$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        $user_id = $_POST['user_id'];  // Assuming user ID comes from form input or session
        $date = $_POST['date'];
        $duration = $_POST['duration'];
        $intensity = $_POST['intensity'];

        switch ($_POST['action']) {
            case 'add':
                // Add a new workout
                $stmt = $conn->prepare("INSERT INTO workouts (user_id, date, duration, intensity) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("isii", $user_id, $date, $duration, $intensity);
                if ($stmt->execute()) {
                    $message = "Workout added successfully.";
                } else {
                    $message = "Error adding workout: " . $stmt->error;
                }
                $stmt->close();
                break;
            case 'edit':
                // Assuming workout_id is also coming from the form
                $workout_id = $_POST['workout_id'];
                $stmt = $conn->prepare("UPDATE workouts SET user_id=?, date=?, duration=?, intensity=? WHERE workout_id=?");
                $stmt->bind_param("isiii", $user_id, $date, $duration, $intensity, $workout_id);
                if ($stmt->execute()) {
                    $message = "Workout updated successfully.";
                } else {
                    $message = "Error updating workout: " . $stmt->error;
                }
                $stmt->close();
                break;
            case 'delete':
                $workout_id = $_POST['workout_id'];
                $stmt = $conn->prepare("DELETE FROM workouts WHERE workout_id=?");
                $stmt->bind_param("i", $workout_id);
                if ($stmt->execute()) {
                    $message = "Workout deleted successfully.";
                } else {
                    $message = "Error deleting workout: " . $stmt->error;
                }
                $stmt->close();
                break;
        }
    }
}

// Fetch all workouts to display
$workouts = [];
$result = $conn->query("SELECT * FROM workouts");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $workouts[] = $row;
    }
    $result->free();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Workouts - Admin</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h1>Manage Workouts</h1>
    <?php if ($message): ?>
        <p><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <!-- Display all workouts -->
    <table>
        <thead>
            <tr>
                <th>User ID</th>
                <th>Date</th>
                <th>Duration (minutes)</th>
                <th>Intensity</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($workouts as $workout): ?>
            <tr>
                <td><?= htmlspecialchars($workout['user_id']) ?></td>
                <td><?= htmlspecialchars($workout['date']) ?></td>
                <td><?= htmlspecialchars($workout['duration']) ?></td>
                <td><?= htmlspecialchars($workout['intensity']) ?></td>
                <td>
                    <form method="post">
                        <input type="hidden" name="workout_id" value="<?= $workout['workout_id'] ?>">
                        <input type="submit" name="action" value="edit">
                        <input type="submit" name="action" value="delete" onclick="return confirm('Are you sure?');">
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h2>Add/Edit Workout</h2>
    <form action="admin_workouts.php" method="post">
        <input type="hidden" name="workout_id" value=""> <!-- Fill dynamically when editing -->
        User ID: <input type="number" name="user_id" required><br>
        Date: <input type="date" name="date" required><br>
        Duration: <input type="number" name="duration" required><br>
        Intensity: <input type="text" name="intensity" required><br>
        <input type="submit" name="action" value="add"> <!-- Toggle to 'update' for edits -->
    </form>
</div>
</body>
</html>
