<?php
// Filename: workout.php
// Path: /htdocs/workout.php
// Date Edited: 2024-04-23
// Time Edited: 4:00 PM
// Revision Number: 1.3

include 'header.php'; // Includes the DB connection

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin.php');
    exit;
}

$totalExercises = $_POST['total_exercises'] ?? 1;
$workoutDuration = $_POST['workout_duration'] ?? 30;
$restDuration = $_POST['rest_duration'] ?? 30;
$sets = $_POST['sets'] ?? 1;
$selectedMuscleGroups = $_POST['muscle_groups'] ?? [];
$selectedIntensities = $_POST['intensity'] ?? [];

$muscleCondition = implode(" OR ", array_map(function($item) use ($conn) { return "muscle_groups LIKE '%" . mysqli_real_escape_string($conn, $item) . "%'"; }, $selectedMuscleGroups));
$intensityCondition = implode(" OR ", array_map(function($item) use ($conn) { return "impact_level = '" . mysqli_real_escape_string($conn, $item) . "'"; }, $selectedIntensities));

$query = $conn->prepare("SELECT * FROM exercises WHERE ($muscleCondition) AND ($intensityCondition) ORDER BY RAND() LIMIT ?");
$query->bind_param("i", $totalExercises);
$query->execute();
$result = $query->get_result();

$exercises = [];
while ($row = $result->fetch_assoc()) {
    $exercises[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Workout Schedule</title>
    <link rel="stylesheet" href="style.css">
    <script src="timer.js"></script>
</head>
<body>
<div class="container">
    <h1>Workout Schedule</h1>
    <?php if (empty($exercises)): ?>
        <p>No exercises selected or found. Please go back and select exercises.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Set Number</th>
                    <th>Exercise</th>
                    <th>Workout Time (seconds)</th>
                    <th>Rest Time (seconds)</th>
                </tr>
            </thead>
            <tbody>
                <?php for ($set = 1; $set <= $sets; $set++): ?>
                    <?php foreach ($exercises as $exercise): ?>
                    <tr>
                        <td>Set <?= $set; ?></td>
                        <td><?= htmlspecialchars($exercise['name']); ?></td>
                        <td><?= $workoutDuration; ?></td>
                        <td><?= $restDuration; ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endfor; ?>
            </tbody>
        </table>
        <!-- Button to start the workout timer -->
        <button onclick="initializeTimer(<?php echo htmlspecialchars(json_encode($exercises)); ?>, <?php echo $workoutDuration; ?>, <?php echo $restDuration; ?>, <?php echo $sets; ?>)">Start Workout</button>
    <?php endif; ?>
</div>
<script src="timer.js"></script>
</body>
</html>
