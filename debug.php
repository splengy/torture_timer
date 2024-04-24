<?php
// A simple debugging script to output information about variables and the database state.

function debug_info($message, $variable = null) {
    echo '<pre><strong>Debug:</strong> ' . htmlspecialchars($message);
    if (!is_null($variable)) {
        echo "\n";
        print_r($variable); // Using print_r to display array or object structures
    }
    echo '</pre>';
}
?>
