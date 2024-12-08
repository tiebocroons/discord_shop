<?php
session_start();
require_once __DIR__ . "/classes/Database.php";
require_once __DIR__ . "/classes/User.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $user = new User();
    if ($user->createUser($username, $password)) {
        header("Location: login.php");
        exit;
    } else {
        // Handle failed account creation
        $error = "Failed to create account. Username may already exist. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Create Account</title>
    <link rel="stylesheet" href="css/general.css">
    <link rel="stylesheet" href="css/create_account.css">
</head>
<body>
    <div class="signup-container">
        <h1>Create Account</h1>
        <?php if (isset($error)): ?>
            <p class="error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>
        <form method="POST" action="">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <button type="submit">Create Account</button>
        </form>
        <p class="login-link">Already have an account? <a href="login.php">Login here</a></p>
    </div>
</body>
</html>