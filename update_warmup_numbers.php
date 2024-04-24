<?php
include 'db.php';  // Include your database connection setup

// Function to generate a unique warmup number that is not already in the exercises table
function generateUniqueWarmupNumber($conn) {
    $unique = false;
    $number = 0;
    while (!$unique) {
        $number = rand(150, 5999);
        $query = "SELECT COUNT(*) FROM exercises WHERE exercise_number = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $number);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        if ($count == 0) {
            $unique = true;
        }
        $stmt->close();
    }
    return $number;
}

// Start transaction
$conn->begin_transaction();
try {
    $selectQuery = "SELECT id, warmup_number FROM warmups WHERE warmup_number = '0'";
    $result = $conn->query($selectQuery);

    while ($row = $result->fetch_assoc()) {
        $newNumber = generateUniqueWarmupNumber($conn);
        $updateQuery = "UPDATE warmups SET warmup_number = ? WHERE id = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("ii", $newNumber, $row['id']);
        $updateStmt->execute();
        $updateStmt->close();

        echo "Updated warmup ID " . $row['id'] . " with new warmup_number: " . $newNumber . "<br>";
    }

    $conn->commit();
} catch (Exception $e) {
    $conn->rollback();
    echo "Failed to update warmup numbers: " . $e->getMessage();
}

$conn->close();
?>
