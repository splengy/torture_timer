<?php
include 'header.php'; // Includes the DB connection

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin.php');
    exit;
}

$message = '';
$sort = $_GET['sort'] ?? 'warmup_number'; // Default sort column
$order = $_GET['order'] ?? 'ASC'; // Default sort order
$view = $_GET['view'] ?? 25; // Default number of rows to show
$view = $view === 'all' ? 1000000 : $view; // If "all" is selected, set a high limit

$muscleGroupsResult = $conn->query("SELECT DISTINCT muscle_groups FROM warmups");
$muscleGroups = [];
while ($row = $muscleGroupsResult->fetch_assoc()) {
    $muscleGroups = array_merge($muscleGroups, explode(',', $row['muscle_groups']));
}
$muscleGroups = array_unique($muscleGroups);

$intensityLevelsResult = $conn->query("SELECT DISTINCT impact_level FROM warmups");
$intensityLevels = [];
while ($row = $intensityLevelsResult->fetch_assoc()) {
    $intensityLevels[] = $row['impact_level'];
}

$selectedWarmups = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['random'])) {
    $numberOfWarmups = $_POST['random_number_of_warmups'] ?? 1;
    $selectedWarmups = $conn->query("SELECT * FROM warmups ORDER BY RAND() LIMIT $numberOfWarmups")->fetch_all(MYSQLI_ASSOC);
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $selectedMuscleGroups = $_POST['muscle_groups'] ?? [];
    $selectedIntensity = $_POST['intensity'] ?? '';
    $numberOfWarmups = $_POST['number_of_warmups'] ?? 1;

    $muscleGroupCondition = implode("', '", $selectedMuscleGroups);
    $selectedWarmups = $conn->query("SELECT * FROM warmups WHERE impact_level = '$selectedIntensity' AND FIND_IN_SET(muscle_groups, '$muscleGroupCondition') LIMIT $numberOfWarmups")->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Warmup Selector</title>
    <link rel="stylesheet" href="style.css">
    <script>
    // Function to toggle the state of all checkboxes
    function toggleCheckboxes(source) {
        checkboxes = document.querySelectorAll('input[type="checkbox"][name="muscle_groups[]"]');
        for (var i = 0, n = checkboxes.length; i < n; i++) {
            checkboxes[i].checked = source.checked;
        }
    }
    </script>
</head>
<body>
<div class="container">
    <h1>Select Warmups</h1>
    <form action="warmup.php" method="post">
        <div class="form-section">
            <label><input type="checkbox" onclick="toggleCheckboxes(this)"> Select All</label>
            <?php foreach ($muscleGroups as $group): ?>
                <label style="display: inline;">
                    <input type="checkbox" name="muscle_groups[]" value="<?= htmlspecialchars($group); ?>">
                    <?= htmlspecialchars($group); ?>
                </label>
            <?php endforeach; ?>
        </div>

        <div class="form-section">
            <label>Intensity Level:</label>
            <select name="intensity">
                <?php foreach ($intensityLevels as $level): ?>
                    <option value="<?= htmlspecialchars($level); ?>"><?= htmlspecialchars($level); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-section">
            <label>Number of Warmups:</label>
            <select name="number_of_warmups">
                <?php for ($i = 1; $i <= 50; $i++): ?>
                    <option value="<?= $i; ?>"><?= $i; ?></option>
                <?php endfor; ?>
            </select>
        </div>

        <input type="submit" value="Get Warmups">
    </form>

    <form action="warmup.php" method="post">
        <label>Random Number of Warmups:</label>
        <select name="random_number_of_warmups">
            <?php for ($i = 1; $i <= 50; $i++): ?>
                <option value="<?= $i; ?>"><?= $i; ?></option>
            <?php endfor; ?>
        </select>
        <input type="submit" name="random" value="Randomize">
    </form>

    <?php if (!empty($selectedWarmups)): ?>
        <h2>Selected Warmups</h2>
        <table>
            <thead>
                <tr>
                    <th>U#</th>
                    <th>Name</th>
                    <th>Impact</th>
                    <th>Description</th>
                    <th>Muscle Groups</th>
                    <th>Long Description</th>
                    <th>Media URL</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($selectedWarmups as $warmup): ?>
                    <tr>
                        <td><?= htmlspecialchars($warmup['warmup_number']); ?></td>
                        <td><?= htmlspecialchars($warmup['name']); ?></td>
                        <td><?= htmlspecialchars($warmup['impact_level']); ?></td>
                        <td><?= htmlspecialchars($warmup['description']); ?></td>
                        <td><?= htmlspecialchars($warmup['muscle_groups']); ?></td>
                        <td><?= htmlspecialchars($warmup['long_description']); ?></td>
                        <td><img src="<?= htmlspecialchars($warmup['media_url']); ?>" alt="Warmup Media" style="width:100px;"></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
</body>
</html>
