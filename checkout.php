<?php
session_start();
require_once 'classes/Database.php';
require_once 'classes/User.php';
require_once 'classes/Cart.php';

// Check if the user is authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user = new User();
$cart = new Cart();

$userId = $_SESSION['user_id'];
$totalPrice = $cart->getTotalPrice($userId);
$userCurrency = $user->getDigitalCurrency($userId);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($userCurrency >= $totalPrice) {
        // Deduct the total price from the user's digital currency
        $user->deductDigitalCurrency($userId, $totalPrice);
        // Clear the cart
        $cart->clearCart($userId);
        $message = "Payment successful. Thank you for your purchase!";
    } else {
        $error = "Insufficient digital currency.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Checkout</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Checkout</h1>
        <?php if (isset($message)): ?>
            <p class="success"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <p class="error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>
        <h2>Total Price: <?php echo htmlspecialchars($totalPrice, ENT_QUOTES, 'UTF-8'); ?> units</h2>
        <h2>Your Digital Currency: <?php echo htmlspecialchars($userCurrency, ENT_QUOTES, 'UTF-8'); ?> units</h2>
        <form method="POST" action="checkout.php">
            <button type="submit">Pay Now</button>
        </form>
        <a href="cart.php"><button>Back to Cart</button></a>
    </div>
</body>
</html>