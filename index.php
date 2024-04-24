<?php
// Include the common header and navigation
include 'header.php';
?>

<div class="container">
    <h1>Welcome to the Exercise App!</h1>
    <p>Get ready to track and enhance your workouts with precision.</p>
    
    <button id="loginButton" onclick="toggleLoginForm()">Login</button>
    <form id="loginForm" style="display:none;" method="post" action="login.php">
        Username: <input type="text" name="username" required><br>
        Password: <input type="password" name="password" required><br>
        <input type="submit" value="Login">
    </form>

    <button id="registerButton" onclick="toggleRegisterForm()">Register</button>
    <form id="registrationForm" style="display:none;" method="post" action="register.php">
        Username: <input type="text" name="username" required><br>
        Password: <input type="password" name="password" required><br>
        Verify Password: <input type="password" name="password_verify" required><br>
        Email: <input type="email" id="email" name="email" required><br>
        Height: <input type="text" name="height" required><br>
        Weight: <input type="text" name="weight" required><br>
        Age: <input type="number" name="age" required><br>
        Gender: <select name="gender" required>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select><br>
        <input type="submit" value="Register">
    </form>
</div>
</body>
</html>
