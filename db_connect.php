<?php
$host = "mysql://root:rLCyCNYAXMcMPUeNfjeAgyjxLPdOihTk@autorack.proxy.rlwy.net:21990/railway";   // Database host
$dbname = "railway";  // database naam
$username = "root";  // Database gebruikersnaam
$password = "rLCyCNYAXMcMPUeNfjeAgyjxLPdOihTk";  // Database wachtwoord

// Verbinding maken met MySQL database
$conn = new mysqli($host, $username, $password, $dbname);

// Controleer de verbinding
if ($conn->connect_error) {
    die("Verbinding met database mislukt: " . $conn->connect_error);
}
?>