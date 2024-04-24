<?php
// Filename: exercise.php
// Path: /htdocs/exercise.php
// Date Edited: 2024-04-22
// Revision Number: 1.8

include 'header.php'; // Includes the DB connection

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin.php');
    exit;
}

// Fetch available muscle groups
$muscleGroupsResult = $conn->query("SELECT DISTINCT muscle_groups FROM exercises");
$allMuscleGroups = [];
while ($row = $muscleGroupsResult->fetch_assoc()) {
    $parts = explode(',', $row['muscle_groups']);
    foreach ($parts as $part) {
        $trimmedPart = trim($part);
        if (!in_array($trimmedPart, $allMuscleGroups)) {
            $allMuscleGroups[] = $trimmedPart;
        }
    }
}

// Fetch unique intensity levels
$intensityLevelsResult = $conn->query("SELECT DISTINCT impact_level FROM exercises");
$intensityLevels = [];
while ($row = $intensityLevelsResult->fetch_assoc()) {
    $level = trim($row['impact_level']);
    if (!in_array($level, $intensityLevels)) {
        $intensityLevels[] = $level;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Exercise Selector</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h1>Select Exercises</h1>
    <form action="workout.php" method="post">
        <div class="form-section">
            <h3>Muscle Groups:</h3>
            <?php foreach ($allMuscleGroups as $group): ?>
            <label>
                <input type="checkbox" name="muscle_groups[]" value="<?= htmlspecialchars($group); ?>">
                <?= htmlspecialchars($group); ?>
            </label>
            <?php endforeach; ?>
        </div>
        <div class="form-section">
            <h3>Intensity Levels:</h3>
            <?php foreach ($intensityLevels as $level): ?>
            <label>
                <input type="checkbox" name="intensity[]" value="<?= htmlspecialchars($level); ?>">
                <?= htmlspecialchars($level); ?>
            </label>
            <?php endforeach; ?>
        </div>
        <div class="form-section">
            <label>Total Number of Unique Exercises:</label>
            <select name="total_exercises">
                <?php for ($i = 1; $i <= 50; $i++): ?>
                <option value="<?= $i; ?>"><?= $i; ?></option>
                <?php endfor; ?>
            </select>
            <label>Workout Duration (seconds):</label>
            <select name="workout_duration">
                <?php for ($i = 5; $i <= 300; $i += 5): ?>
                <option value="<?= $i; ?>"><?= $i; ?></option>
                <?php endfor; ?>
            </select>
            <label>Rest Duration (seconds):</label>
            <select name="rest_duration">
                <?php for ($i = 5; $i <= 300; $i += 5): ?>
                <option value="<?= $i; ?>"><?= $i; ?></option>
                <?php endfor; ?>
            </select>
            <label>Sets:</label>
            <select name="sets">
                <?php for ($i = 1; $i <= 20; $i++): ?>
                <option value="<?= $i; ?>"><?= $i; ?></option>
                <?php endfor; ?>
            </select>
            <input type="submit" value="Find Exercises">
        </div>
    </form>
    <!-- Include the random workout form at the bottom -->
    <?php include 'random_workout_form.php'; ?>
</div>
</body>
</html>
