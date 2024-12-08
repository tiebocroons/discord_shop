<?php
session_start();
require_once __DIR__ . "/classes/User.php"; // Include User class

// Check if the user is authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user = new User();
$userId = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $newUsername = $_POST['new_username'];
    $newPassword = $_POST['new_password'];

    try {
        if (!empty($newUsername)) {
            $user->changeUsername($userId, $newUsername);
        }
        if (!empty($newPassword)) {
            $user->changePassword($userId, $newPassword);
        }
        $message = "Credentials updated successfully.";
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Change Credentials</title>
    <link rel="stylesheet" href="css/general.css">
    <link rel="stylesheet" href="css/credentials.css">
</head>
<body>
    <div class="container">
        <h1>Change Credentials</h1>
        <?php if (isset($message)): ?>
            <p class="message"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <p class="error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>
        <form method="POST" action="">
            <label for="new_username">New Username:</label>
            <input type="text" id="new_username" name="new_username">
            <label for="new_password">New Password:</label>
            <input type="password" id="new_password" name="new_password">
            <button type="submit">Update Credentials</button>
            <button type="button" class="back-button" onclick="window.location.href='index.php'">Go to Homepage</button>
        </form>
    </div>
</body>
</html>