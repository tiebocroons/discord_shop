<?php
$host = "localhost";   // Database host
$dbname = "discord_shop";  // database naam
$username = "Hypha";  // Database gebruikersnaam
$password = "Y03lyfIN4(V3EeFf";  // Database wachtwoord

// Verbinding maken met MySQL database
$conn = new mysqli($host, $username, $password, $dbname);

// Controleer de verbinding
if ($conn->connect_error) {
    die("Verbinding met database mislukt: " . $conn->connect_error);
}
?>