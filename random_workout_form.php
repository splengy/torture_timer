<?php
// Filename: random_workout_form.php
// Path: /htdocs/random_workout_form.php
// Date Edited: 2024-04-22
// Time Edited: 3:00 PM

// Ensure the database connection is passed from the including file.
?>

<div class="form-section">
    <h2>Randomize Exercises</h2>
    <form action="workout.php" method="post">
        <label>Random Number of Exercises:</label>
        <select name="random_number_of_exercises">
            <?php for ($i = 1; $i <= 50; $i++): ?>
                <option value="<?= $i; ?>"><?= $i; ?></option>
            <?php endfor; ?>
        </select><br>

        <label>Total Workout Time (minutes):</label>
        <select name="total_workout_time">
            <?php for ($i = 150; $i <= 9000; $i += 150): // Increments of 2:30 minutes (150 seconds) ?>
                <option value="<?= $i; ?>"><?= intdiv($i, 60); ?>:<?= sprintf("%02d", $i % 60); ?></option>
            <?php endfor; ?>
        </select><br>

        <label>Sets:</label>
        <select name="sets">
            <?php for ($i = 1; $i <= 20; $i++): ?>
                <option value="<?= $i; ?>"><?= $i; ?></option>
            <?php endfor; ?>
        </select><br>

        <input type="submit" name="random" value="Randomize">
    </form>
</div>
