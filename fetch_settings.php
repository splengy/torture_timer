<?php
// file path: /htdocs/fetch_settings.php
function fetchAppSettings($conn) {
    $settings = [];
    $sql = "SELECT setting_key, setting_value FROM app_settings";
    $result = $conn->query($sql);
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
    }
    return $settings;
}
