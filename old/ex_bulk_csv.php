<?php
// Check for the 'upload_csv' action from the form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['action'] == 'upload_csv') {
    if (isset($_FILES['exercise_csv']) && $_FILES['exercise_csv']['error'] == 0) {
        $file = $_FILES['exercise_csv']['tmp_name'];
        $handle = fopen($file, 'r');

        // Skip the header row if your CSV includes headers
        if ($handle !== FALSE) {
            fgetcsv($handle);  // Assuming the first row is headers

            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                // Assign each part of the CSV row to a variable
                $name = $data[0];
                $muscle_groups = $data[1];
                $impact = $data[2];
                $equipment = $data[3];
                $short_description = $data[4];
                $short_explanation = $data[5];
                $long_explanation = $data[6];

                // Prepare and bind parameters to the SQL statement for inserting data
                $stmt = $conn->prepare("INSERT INTO exercises (name, muscle_groups, impact_level, recommended_equipment, description, short_explanation, detailed_description) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("sssssss", $name, $muscle_groups, $impact, $equipment, $short_description, $short_explanation, $long_explanation);

                // Execute the statement and handle any errors
                if (!$stmt->execute()) {
                    echo "Error inserting data: " . $stmt->error;
                }

                $stmt->close();
            }

            fclose($handle);
            echo "CSV data uploaded successfully.";
        } else {
            echo "Failed to open the file.";
        }
    } else {
        echo "Failed to upload file.";
    }
}
?>
