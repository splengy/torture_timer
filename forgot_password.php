// file path: /htdocs/forgot_password.php
<?php
// Start session and include database
session_start();
include 'db.php';

// Initialize variable
$email_error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $conn->real_escape_string($_POST['email']);

    // Check if email exists in the database
    $sql = "SELECT id FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Assuming a function sendPasswordReset($email) exists that handles email sending
        sendPasswordReset($email);
        echo "<p>Password reset link has been sent to your email.</p>";
    } else {
        $email_error = "No account found with that email address.";
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password - Exercise App</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Reset Your Password</h1>
        <form action="forgot_password.php" method="post">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required><br>
            <input type="submit" value="Send Reset Link">
        </form>
        <?php if (!empty($email_error)): ?>
            <p class="error"><?php echo $email_error; ?></p>
        <?php endif; ?>
    </div>
</body>
</html>
