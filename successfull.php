<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Payment Successful</title>
    <link rel="stylesheet" href="css/general.css">
    <link rel="stylesheet" href="css/successfull.css">
</head>
<body>
    <div class="container">
        <h1>Payment Successful</h1>
        <p class="message">Thank you for your purchase! Your payment has been successfully processed.</p>
        <a href="index.php" class="button">Go to Homepage</a>
    </div>
</body>
</html>