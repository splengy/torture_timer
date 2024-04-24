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

// Handling form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    switch ($_POST['action']) {
        case 'add':
        case 'update':
            $id = $_POST['id'] ?? null;
            $warmup_number = $_POST['warmup_number'] ?? ('U' . rand(100, 999));
            $name = $_POST['name'];
            $description = $_POST['description'];
            $long_description = $_POST['long_description'] ?? ''; // Handle long description
            $muscle_groups = isset($_POST['muscle_groups']) ? implode(',', $_POST['muscle_groups']) : '';
            $impact_level = isset($_POST['impact_level']) ? implode(',', $_POST['impact_level']) : '';
            $media_url = '/exmedia/default_blank_ex.png';

            if (isset($_FILES['media']) && $_FILES['media']['error'] == 0) {
                $mediaFile = '/exmedia/' . basename($_FILES['media']['name']);
                if (move_uploaded_file($_FILES['media']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . $mediaFile)) {
                    $media_url = $mediaFile;
                }
            }

            if ($id) {
                $stmt = $conn->prepare("UPDATE warmups SET warmup_number=?, name=?, description=?, long_description=?, muscle_groups=?, impact_level=?, media_url=? WHERE id=?");
                $stmt->execute([$warmup_number, $name, $description, $long_description, $muscle_groups, $impact_level, $media_url, $id]);
            } else {
                $stmt = $conn->prepare("INSERT INTO warmups (warmup_number, name, description, long_description, muscle_groups, impact_level, media_url) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$warmup_number, $name, $description, $long_description, $muscle_groups, $impact_level, $media_url]);
            }

            $message = $stmt->rowCount() ? ($id ? "Warmup updated successfully." : "New warmup added successfully.") : "Operation failed.";
            break;

        case 'delete':
            $id = $_POST['id'];
            $stmt = $conn->prepare("DELETE FROM warmups WHERE id=?");
            $stmt->execute([$id]);
            $message = $stmt->rowCount() ? "Warmup deleted successfully." : "Failed to delete warmup.";
            break;
    }
}

// Fetch all warmups to display
$sql = "SELECT * FROM warmups ORDER BY $sort $order LIMIT $view";
$warmups = $conn->query($sql)->fetch_all(MYSQLI_ASSOC);
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
	<li><a href="admin_exercises.php">Manage Exercises</a></li>
		<li><a href="admin_workouts.php">Manage workouts</a></li>
		<li><a href="admin_warmups.php">Manage warmups</a></li>
        <a href="?logout=true">Logout</a>
        <?php if (!empty($message)) echo "<p>$message</p>"; ?>

    <h1>Manage Warmups</h1>
    <p><?= htmlspecialchars($message) ?></p>

    <!-- Add/Edit Form -->
    <h2>Add/Edit Warmup</h2>
    <form action="admin_warmups.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $id ?? ''; ?>">
        U#: <input type="number" name="warmup_number" value="<?= $warmup_number ?? ''; ?>" required><br>
        Name: <input type="text" name="name" value="<?= $name ?? ''; ?>" required><br>
        Description: <textarea name="description" required><?= $description ?? ''; ?></textarea><br>
        Long Description: <textarea name="long_description" required><?= $long_description ?? ''; ?></textarea><br>
        Muscle Groups: <input type="text" name="muscle_groups" value="<?= $muscle_groups ?? ''; ?>"><br>
        Impact Level: <input type="text" name="impact_level" value="<?= $impact_level ?? ''; ?>"><br>
        Media (upload): <input type="file" name="media"><br>
        <input type="submit" name="action" value="add">
    </form>

    <!-- Warmup Viewing Options -->
    <form action="admin_warmups.php" method="get">
        View:
        <select name="view">
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="100">100</option>
            <option value="all">All</option>
        </select>
        <input type="submit" value="Go">
    </form>

    <!-- Display Table -->
    <h2>View Warmups</h2>
    <table>
        <thead>
            <tr>
                <th><a href="?sort=warmup_number&order=<?= $order === 'ASC' ? 'DESC' : 'ASC' ?>">U#</a></th>
                <th><a href="?sort=name&order=<?= $order === 'ASC' ? 'DESC' : 'ASC' ?>">Name</a></th>
                <th><a href="?sort=impact_level&order=<?= $order === 'ASC' ? 'DESC' : 'ASC' ?>">Impact</a></th>
                <th>Description</th>
                <th><a href="?sort=muscle_groups&order=<?= $order === 'ASC' ? 'DESC' : 'ASC' ?>">Muscle Groups</a></th>
                <th>Long Description</th>
                <th>Media URL</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($warmups as $warmup): ?>
            <tr>
                <td><?= htmlspecialchars($warmup['warmup_number']) ?></td>
                <td><?= htmlspecialchars($warmup['name']) ?></td>
                <td><?= htmlspecialchars($warmup['impact_level']) ?></td>
                <td class="scrollable-text"><?= htmlspecialchars($warmup['description']) ?></td>
                <td><?= htmlspecialchars($warmup['muscle_groups']) ?></td>
                <td class="scrollable-text"><?= htmlspecialchars($warmup['long_description']) ?></td>
                <td><img src="<?= htmlspecialchars($warmup['media_url']) ?>" alt="Warmup Media" style="width:100px;"></td>
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
</div>
</body>
</html>
