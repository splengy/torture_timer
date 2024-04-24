<?php
// Handle POST request for adding or editing exercises
if ($_SERVER['REQUEST_METHOD'] == 'POST' && ($_POST['action'] == 'add' || $_POST['action'] == 'edit')) {
    // Collect input data
    $exercise_number = $_POST['exercise_number'] ?? null;
    $name = $_POST['name'];
    $description = $_POST['description'];
    $detailed_description = $_POST['detailed_description'] ?? '';
    $muscle_groups = isset($_POST['muscle_groups']) ? implode(',', $_POST['muscle_groups']) : '';
    $impact_level = isset($_POST['impact_level']) ? implode(',', $_POST['impact_level']) : '';
    $recommended_equipment = isset($_POST['recommended_equipment']) ? implode(',', $_POST['recommended_equipment']) : '';
    $mediaFile = '/exmedia/default_blank_ex.png';  // Default media file

    if (isset($_FILES['media']) && $_FILES['media']['error'] == 0) {
        $mediaFile = '/exmedia/' . basename($_FILES['media']['name']);
        if (!move_uploaded_file($_FILES['media']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . $mediaFile)) {
            $mediaFile = '/exmedia/default_blank_ex.png';  // Use default if upload fails
        }
    }

    if ($_POST['action'] == 'add') {
        $stmt = $conn->prepare("INSERT INTO exercises (exercise_number, name, description, detailed_description, muscle_groups, impact_level, recommended_equipment, media_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssssss", $exercise_number, $name, $description, $detailed_description, $muscle_groups, $impact_level, $recommended_equipment, $mediaFile);
    } else {
        $id = $_POST['id'];
        $stmt = $conn->prepare("UPDATE exercises SET exercise_number=?, name=?, description=?, detailed_description=?, muscle_groups=?, impact_level=?, recommended_equipment=?, media_url=? WHERE id=?");
        $stmt->bind_param("isssssssi", $exercise_number, $name, $description, $detailed_description, $muscle_groups, $impact_level, $recommended_equipment, $mediaFile, $id);
    }

    if ($stmt->execute()) {
        $message = "Exercise " . ($_POST['action'] == 'add' ? "added" : "updated") . " successfully.";
    } else {
        $message = "Error: " . $stmt->error;
    }
    $stmt->close();
}
?>
