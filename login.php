<?php
// file path: /htdocs/login.php
include 'header.php'; // Includes session_start(), db connection, and fetching settings from the database.

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $conn->real_escape_string($_POST['password']);

    $sql = "SELECT id, password_hash FROM users WHERE username = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row['password_hash'])) {
                $_SESSION['user_id'] = $row['id'];
                header("Location: main.php"); // Redirect to main page after successful login
                exit();
            } else {
                $login_error = "Invalid password.";
            }
        } else {
            $login_error = "No user found with that username.";
        }
        $stmt->close();
    } else {
        $login_error = "Error preparing SQL statement.";
    }
    $conn->close();
}
?>

<div class="container">
    <h1>Login to Your Account</h1>
    <form action="login.php" method="post">
        Username: <input type="text" name="username" required><br>
        Password: <input type="password" name="password" required><br>
        <input type="submit" value="Login">
    </form>
    <?php if (!empty($login_error)): ?>
        <p class="error"><?php echo htmlspecialchars($login_error); ?></p>
    <?php endif; ?>
</div>

</body>
</html>
