<?php
session_start();

// Controleer of de gebruiker ingelogd is
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

// Voeg de databaseverbinding toe
require_once 'db_connect.php';

// Haal de gebruiker-ID uit de sessie
$user_id = $_SESSION['user_id'];

// Query om de gebruiker-informatie op te halen
$sql = "SELECT username, digital_currency_units FROM Users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username, $digital_currency_units);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Welkom</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h1>Welkom, <?php echo htmlspecialchars($username); ?>!</h1>
            <p>Je bent succesvol ingelogd.</p>
            <p>Je hebt <?php echo htmlspecialchars($digital_currency_units); ?> digitale units.</p>
            <form action="logout.php" method="POST">
                <button type="submit">Logout</button>
            </form>
        </div>
    </div>
</body>
<footer>
    
</footer>
</html>