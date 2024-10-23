<?php
session_start();

// Include User class
require_once __DIR__ . "/classes/User.php";

// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve username and password from the POST request
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Create a User instance and attempt to login
    $user = new User();

    // Attempt to log the user in
    if ($user->loginUser($username, $password)) {
        // Successful login, redirect to index.php
        header("Location: index.php");
        exit;
    } else {
        // Handle failed login attempt
        $error = "Invalid username or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Login</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h1>Login</h1>
            <?php
            if (isset($error)) {
                echo "<p style='color: red;'>$error</p>";
            }
            ?>
            <form method="POST" action="login.php">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Enter your username" required>
                
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
                
                <button type="submit">Login</button>
            </form>
            <div class="signup-link">
                Don't have an account? <a href="create_account.php">Sign up</a>
            </div>
        </div>
    </div>
</body>
<footer>
    
</footer>
</html>