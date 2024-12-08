<?php
session_start();
require_once __DIR__ . "/classes/Database.php";
require_once __DIR__ . "/classes/User.php";
require_once __DIR__ . "/classes/Cart.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user = new User();
$cart = new Cart();

$userId = $_SESSION['user_id'];
$totalPrice = $cart->getTotalPrice($userId);
$userCurrency = $user->getDigitalCurrency($userId);
$cartItems = $cart->getCartItems($userId);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($userCurrency >= $totalPrice) {
        // Deduct the total price from the user's digital currency
        $user->deductDigitalCurrency($userId, $totalPrice);
        // Clear the cart
        $cart->clearCart($userId);
        // Redirect to success page
        header("Location: successfull.php");
        exit;
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
    <link rel="stylesheet" href="css/general.css">
    <link rel="stylesheet" href="css/checkout.css">
</head>
<body>
    <div class="container">
        <h1>Checkout</h1>
        <?php if (isset($message)): ?>
            <p class="message"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <p class="error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>
        <div class="total-price">
            <p><strong>Total Price:</strong> <?php echo htmlspecialchars($totalPrice, ENT_QUOTES, 'UTF-8'); ?> units</p>
            <p><strong>Your Digital Currency:</strong> <?php echo htmlspecialchars($userCurrency, ENT_QUOTES, 'UTF-8'); ?> units</p>
        </div>
        <div class="product-list">
            <?php foreach ($cartItems as $item): ?>
                <div class="product-item">
                    <img src="<?php echo htmlspecialchars($item['img_url'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8'); ?>">
                    <div>
                        <h3><?php echo htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8'); ?></h3>
                        <p><?php echo htmlspecialchars($item['description'], ENT_QUOTES, 'UTF-8'); ?></p>
                        <p><strong>Price:</strong> <?php echo htmlspecialchars($item['price'], ENT_QUOTES, 'UTF-8'); ?> units</p>
                        <p><strong>Quantity:</strong> <?php echo htmlspecialchars($item['quantity'], ENT_QUOTES, 'UTF-8'); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <form method="POST" action="">
            <button type="submit" class="checkout-button">Pay Now</button>
            <button type="button" class="back-button" onclick="window.location.href='cart.php'">Back to Cart</button>
        </form>
    </div>
</body>
</html>