<?php
// Filename: admin_warmups.php
// Path: /htdocs/admin_warmups.php
// Date Edited: 2024-04-17
// Revision Number: 1

include 'header.php'; // Include the header, assuming it initializes the session and db connection

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin.php');
    exit;
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    switch ($_POST['action']) {
        case 'add':
        case 'update':
            $id = $_POST['id'] ?? null;
            $warmup_number = $_POST['warmup_number'] ?? ('U' . rand(100, 999));
            $name = $_POST['name'];
            $description = $_POST['description'];
            $muscle_groups = isset($_POST['muscle_groups']) ? implode(',', $_POST['muscle_groups']) : '';
            $impact_level = isset($_POST['impact_level']) ? implode(',', $_POST['impact_level']) : '';
            $media_url = '/exmedia/default_blank_ex.png';

            if (isset($_FILES['media']) && $_FILES['media']['error'] == 0) {
                $mediaFile = '/exmedia/' . basename($_FILES['media']['name']);
                if (!move_uploaded_file($_FILES['media']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . $mediaFile)) {
                    $media_url = '/exmedia/default_blank_ex.png';
                }
            }

            $stmt = $id ? $conn->prepare("UPDATE warmups SET warmup_number=?, name=?, description=?, muscle_groups=?, impact_level=?, media_url=? WHERE id=?")
                        : $conn->prepare("INSERT INTO warmups (warmup_number, name, description, muscle_groups, impact_level, media_url) VALUES (?, ?, ?, ?, ?, ?)");
            $params = $id ? [$warmup_number, $name, $description, $muscle_groups, $impact_level, $media_url, $id]
                          : [$warmup_number, $name, $description, $muscle_groups, $impact_level, $media_url];
            $stmt->bind_param($id ? "issssii" : "issssi", ...$params);

            if ($stmt->execute()) {
                $message = $id ? "Warmup updated successfully." : "New warmup added successfully.";
            } else {
                $message = "Error: " . $stmt->error;
            }
            $stmt->close();
            break;

        case 'delete':
            $id = $_POST['id'];
            $stmt = $conn->prepare("DELETE FROM warmups WHERE id=?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                $message = "Warmup deleted successfully.";
            } else {
                $message = "Error deleting warmup: " . $stmt->error;
            }
            $stmt->close();
            break;

        case 'upload_csv':
            if (isset($_FILES['warmup_csv']) && $_FILES['warmup_csv']['error'] == 0) {
                $file = fopen($_FILES['warmup_csv']['tmp_name'], 'r');
                while (($data = fgetcsv($file, 1000, ",")) !== FALSE) {
                    $warmup_number = 'U' . rand(100, 999); // Generate random U#
                    $name = $data[0];
                    $description = $data[1];
                    $muscle_groups = $data[2];
                    $impact_level = $data[3];
                    $media_url = '/exmedia/default_ex.png'; // Default image for all entries

                    $stmt = $conn->prepare("INSERT INTO warmups (warmup_number, name, description, muscle_groups, impact_level, media_url) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("isssss", $warmup_number, $name, $description, $muscle_groups, $impact_level, $media_url);
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
                    $warmup_number = 'U' . rand(100, 999); // Generate random U#
                    $name = $data[0];
                    $description = $data[1];
                    $muscle_groups = $data[2];
                    $impact_level = $data[3];
                    $media_url = '/exmedia/default_ex.png'; // Default image for all entries

                    $stmt = $conn->prepare("INSERT INTO warmups (warmup_number, name, description, muscle_groups, impact_level, media_url) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("isssss", $warmup_number, $name, $description, $muscle_groups, $impact_level, $media_url);
                    $stmt->execute();
                }
                $message = "Pasted CSV data processed successfully.";
            }
            break;
    }
}

// Fetch all warmups to display
$warmups = [];
$result = $conn->query("SELECT * FROM warmups ORDER BY warmup_number");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $warmups[] = $row;
    }
    $result->free();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Warmups - Admin</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h1>Manage Warmups</h1>
    <p><?= htmlspecialchars($message) ?></p>

    <table>
        <thead>
            <tr>
                <th>U#</th>
                <th>Name</th>
                <th>Description</th>
                <th>Muscle Groups</th>
                <th>Impact Level</th>
                <th>Media URL</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($warmups as $warmup): ?>
            <tr>
                <td><?= htmlspecialchars($warmup['warmup_number']) ?></td>
                <td><?= htmlspecialchars($warmup['name']) ?></td>
                <td><?= htmlspecialchars($warmup['description']) ?></td>
                <td><?= htmlspecialchars($warmup['muscle_groups']) ?></td>
                <td><?= htmlspecialchars($warmup['impact_level']) ?></td>
                <td><img src="<?= $warmup['media_url'] ?>" alt="Warmup Media" style="width:100px;"></td>
                <td>
                    <form method="post" action="admin_warmups.php">
                        <input type="hidden" name="id" value="<?= $warmup['id'] ?>">
                        <input type="submit" name="action" value="edit">
                        <input type="submit" name="action" value="delete" onclick="return confirm('Are you sure?');">
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h2>Add/Edit Warmup</h2>
    <form action="admin_warmups.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id" value="">
        U#: <input type="number" name="warmup_number" required><br>
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
        Media (upload to /htdocs/exmedia/): <input type="file" name="media"><br>
        <input type="submit" name="action" value="add"> <!-- Toggle to 'update' when editing -->
    </form>
    <!-- Dedicated form for CSV Upload -->
    <h3>Upload Warmups via CSV</h3>
    <form action="admin_warmups.php" method="post" enctype="multipart/form-data">
        <input type="file" name="warmup_csv" accept=".csv">
        <input type="submit" name="action" value="upload_csv">
    </form>
    <!-- Form for pasting CSV data -->
    <h3>Paste CSV Data</h3>
    <form action="admin_warmups.php" method="post">
        <textarea name="csv_data" rows="10" cols="50" placeholder="Paste CSV data here"></textarea>
        <input type="submit" name="action" value="paste_csv">
    </form>
</div>
</body>
</html>
