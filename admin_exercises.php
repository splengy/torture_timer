<?php
// Filename: admin_exercises.php
// Path: /htdocs/admin_exercises.php
// Date Edited: 2024-04-17
// Revision Number: 1

include 'header.php'; // Include the header
require 'db.php'; // Database connection

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin.php');
    exit;
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    switch ($_POST['action']) {
        case 'add':
        case 'update':
            // Collect and sanitize input data
            $id = $_POST['id'] ?? null;
            $exercise_number = $_POST['exercise_number'] ?? rand(100, 5999);
            $name = $_POST['name'];
            $description = $_POST['description'];
            $muscle_groups = isset($_POST['muscle_groups']) ? implode(',', $_POST['muscle_groups']) : '';
            $impact_level = isset($_POST['impact_level']) ? implode(',', $_POST['impact_level']) : '';
            $recommended_equipment = isset($_POST['recommended_equipment']) ? implode(',', $_POST['recommended_equipment']) : '';
            $media_url = '/exmedia/default_blank_ex.png'; // Default media file

            if (isset($_FILES['media']) && $_FILES['media']['error'] == 0) {
                $mediaFile = '/exmedia/' . basename($_FILES['media']['name']);
                if (!move_uploaded_file($_FILES['media']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . $mediaFile)) {
                    $media_url = '/exmedia/default_blank_ex.png'; // Use default if upload fails
                }
            }

            $stmt = $id ? $conn->prepare("UPDATE exercises SET exercise_number=?, name=?, description=?, muscle_groups=?, impact_level=?, recommended_equipment=?, media_url=? WHERE id=?")
                        : $conn->prepare("INSERT INTO exercises (exercise_number, name, description, muscle_groups, impact_level, recommended_equipment, media_url) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $params = $id ? [$exercise_number, $name, $description, $muscle_groups, $impact_level, $recommended_equipment, $media_url, $id]
                          : [$exercise_number, $name, $description, $muscle_groups, $impact_level, $recommended_equipment, $media_url];
            $stmt->bind_param($id ? "issssssi" : "issssss", ...$params);

            if ($stmt->execute()) {
                $message = $id ? "Exercise updated successfully." : "New exercise added successfully.";
            } else {
                $message = "Error: " . $stmt->error;
            }
            $stmt->close();
            break;

        case 'delete':
            $id = $_POST['id'];
            $stmt = $conn->prepare("DELETE FROM exercises WHERE id=?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                $message = "Exercise deleted successfully.";
            } else {
                $message = "Error deleting exercise: " . $stmt->error;
            }
            $stmt->close();
            break;

        case 'upload_csv':
            if (isset($_FILES['exercise_csv']) && $_FILES['exercise_csv']['error'] == 0) {
                $file = fopen($_FILES['exercise_csv']['tmp_name'], 'r');
                while (($data = fgetcsv($file, 1000, ",")) !== FALSE) {
                    $exercise_number = rand(100, 5999); // Generate random X#
                    $name = $data[0];
                    $muscle_groups = $data[1];
                    $impact_level = $data[2];
                    $recommended_equipment = $data[3];
                    $description = $data[4];
                    $media_url = '/exmedia/default_ex.png'; // Default image for all entries

                    $stmt = $conn->prepare("INSERT INTO exercises (exercise_number, name, description, muscle_groups, impact_level, recommended_equipment, media_url) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("issssss", $exercise_number, $name, $description, $muscle_groups, $impact_level, $recommended_equipment, $media_url);
                    $stmt->execute();
                }
                fclose($file);
                $message = "CSV data uploaded successfully.";
            } else {
                $message = "Failed to upload file.";
            }
            break;

        case 'paste_csv':
            if (!empty($_POST['csv_data'])) {
                $csv_data = trim($_POST['csv_data']);
                $csv_rows = explode("\n", $csv_data);
                foreach ($csv_rows as $row) {
                    $data = str_getcsv($row);
                    $exercise_number = rand(100, 5999); // Generate random X#
                    $name = $data[0];
                    $muscle_groups = $data[1];
                    $impact_level = $data[2];
                    $recommended_equipment = $data[3];
                    $description = $data[4];
                    $media_url = '/exmedia/default_ex.png'; // Default image for all entries

                    $stmt = $conn->prepare("INSERT INTO exercises (exercise_number, name, description, muscle_groups, impact_level, recommended_equipment, media_url) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("issssss", $exercise_number, $name, $description, $muscle_groups, $impact_level, $recommended_equipment, $media_url);
                    $stmt->execute();
                }
                $message = "Pasted CSV data processed successfully.";
            }
            break;
    }
}

// Fetch all exercises to display
$exercises = [];
$result = $conn->query("SELECT * FROM exercises ORDER BY exercise_number");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $exercises[] = $row;
    }
    $result->free();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Exercises - Admin</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h1>Manage Exercises</h1>
    <p><?= htmlspecialchars($message) ?></p>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Description</th>
                <th>Muscle Groups</th>
                <th>Impact Level</th>
                <th>Recommended Equipment</th>
                <th>Media URL</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($exercises as $exercise): ?>
            <tr>
                <td><?= htmlspecialchars($exercise['exercise_number']) ?></td>
                <td><?= htmlspecialchars($exercise['name']) ?></td>
                <td><?= htmlspecialchars($exercise['description']) ?></td>
                <td><?= htmlspecialchars($exercise['muscle_groups']) ?></td>
                <td><?= htmlspecialchars($exercise['impact_level']) ?></td>
                <td><?= htmlspecialchars($exercise['recommended_equipment']) ?></td>
                <td><img src="<?= $exercise['media_url'] ?>" alt="Exercise Media" style="width:100px;"></td>
                <td>
                    <form method="post" action="admin_exercises.php">
                        <input type="hidden" name="id" value="<?= $exercise['id'] ?>">
                        <input type="submit" name="action" value="edit">
                        <input type="submit" name="action" value="delete" onclick="return confirm('Are you sure?');">
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h2>Add/Edit Exercise</h2>
    <form action="admin_exercises.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id" value="">
        X#: <input type="number" name="exercise_number" required><br>
        Name: <input type="text" name="name" required><br>
        Description: <input type="text" name="description" required><br>
        Muscle Groups: (select all that apply)<br>
        <div style="display: flex; flex-wrap: wrap;">
            <label><input type="checkbox" name="muscle_groups[]" value="Chest"> Chest</label>
            <label><input type="checkbox" name="muscle_groups[]" value="Back"> Back</label>
            <label><input type="checkbox" name="muscle_groups[]" value="Arms"> Arms</label>
            <label><input type="checkbox" name="muscle_groups[]" value="Shoulders"> Shoulders</label>
            <label><input type="checkbox" name="muscle_groups[]" value="Legs"> Legs</label>
            <label><input type="checkbox" name="muscle_groups[]" value="Core"> Core</label>
        </div>
        Impact Level: (select all that apply)<br>
        <div style="display: flex; flex-wrap: wrap;">
            <label><input type="checkbox" name="impact_level[]" value="Low"> Low</label>
            <label><input type="checkbox" name="impact_level[]" value="Medium"> Medium</label>
            <label><input type="checkbox" name="impact_level[]" value="High"> High</label>
        </div>
        Recommended Equipment: (select all that apply)<br>
        <div style="display: flex; flex-wrap: wrap;">
            <label><input type="checkbox" name="recommended_equipment[]" value="Dumbbells"> Dumbbells</label>
            <label><input type="checkbox" name="recommended_equipment[]" value="Resistance Bands"> Resistance Bands</label>
            <label><input type="checkbox" name="recommended_equipment[]" value="Weight Bench"> Weight Bench</label>
            <label><input type="checkbox" name="recommended_equipment[]" value="Chair"> Chair</label>
            <label><input type="checkbox" name="recommended_equipment[]" value="Yoga Mat"> Yoga Mat</label>
            <label><input type="checkbox" name="recommended_equipment[]" value="Kettlebell"> Kettlebell</label>
            <label><input type="checkbox" name="recommended_equipment[]" value="Barbell"> Barbell</label>
            <label><input type="checkbox" name="recommended_equipment[]" value="Step Blocks"> Step Blocks</label>
        </div>
        Media (upload to /htdocs/exmedia/): <input type="file" name="media"><br>
        <input type="submit" name="action" value="add"> <!-- Toggle to 'update' when editing -->
    </form>
    <!-- Dedicated form for CSV Upload -->
    <h3>Upload Exercises via CSV</h3>
    <form action="admin_exercises.php" method="post" enctype="multipart/form-data">
        <input type="file" name="exercise_csv" accept=".csv">
        <input type="submit" name="action" value="upload_csv">
    </form>
    <!-- Form for pasting CSV data -->
    <h3>Paste CSV Data</h3>
    <form action="admin_exercises.php" method="post">
        <textarea name="csv_data" rows="10" cols="50" placeholder="Paste CSV data here"></textarea>
        <input type="submit" name="action" value="paste_csv">
    </form>
</div>

</body>
</html>
