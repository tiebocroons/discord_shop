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

// Haal producten op uit de database
$sql = "SELECT id, title, description, price, img_url, category FROM products";
$result = $conn->query($sql);
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
    <div class="product-container">
        <h1>Producten</h1>
        
        <?php
        if ($result->num_rows > 0) {
            // Loop door de producten en toon ze op de pagina
            while ($row = $result->fetch_assoc()) {
                ?>
                <div class="product">
                    <img src="<?php echo htmlspecialchars($row['img_url']); ?>" alt="<?php echo htmlspecialchars($row['title']); ?>">
                    <h2><?php echo htmlspecialchars($row['title']); ?></h2>
                    <p><?php echo htmlspecialchars($row['description']); ?></p>
                    <p>Prijs: €<?php echo htmlspecialchars(number_format($row['price'], 2)); ?></p>
                    <p>Categorie: <?php echo htmlspecialchars($row['category']); ?></p>
                    <a href="product_detail.php?id=<?php echo $row['id']; ?>">Meer informatie</a>
                </div>
                <?php
            }
        } else {
            echo "<p>Geen producten gevonden.</p>";
        }
        
        // Sluit de databaseverbinding
        $conn->close();
        ?>
    </div>
</body>
<footer>
    
</footer>
</html>