<?php
session_start();

// Voeg de databaseverbinding toe
require_once 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Zoek naar de ingevoerde gebruikersnaam in de database
    $sql = "SELECT user_id, username, password FROM Users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    // Controleer of de gebruikersnaam bestaat in de database
    if ($stmt->num_rows == 1) {
        // Bind de resultaten aan variabelen
        $stmt->bind_result($user_id, $db_username, $db_password);
        $stmt->fetch();

        // Controleer het wachtwoord (hash checken, indien nodig)
        if (password_verify($password, $db_password)) {
            // Bewaar de loginstatus en de gebruikers-ID in de sessie
            $_SESSION['loggedin'] = true;
            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $db_username;

            // Stuur de gebruiker naar de homepage
            header("Location: index.php");
            exit;
        } else {
            $error = "Ongeldig wachtwoord!";
        }
    } else {
        $error = "Gebruikersnaam bestaat niet!";
    }

    // Sluit de statement
    $stmt->close();
}

// Sluit de databaseverbinding
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Discord Login</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h1>Discord Login</h1>
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
                Don't have an account? <a href="#">Sign up</a>
            </div>
        </div>
    </div>
</body>
<footer>
    
</footer>
</html>