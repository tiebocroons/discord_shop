<?php
session_start();
require_once __DIR__ . "/classes/Database.php";
require_once __DIR__ . "/classes/User.php";
require_once __DIR__ . "/classes/Product.php";
require_once __DIR__ . "/classes/Cart.php";

$user = new User();
$productManager = new Product();
$cart = new Cart();

$userId = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['product_id']) && isset($_POST['action'])) {
        $productId = $_POST['product_id'];
        $action = $_POST['action'];

        try {
            if ($action === 'add') {
                $product = $productManager->getProductById($productId);
                if ($product) {
                    $cart->addToCart($userId, $productId);
                    $message = "Product added to cart successfully.";
                } else {
                    $error = "Product not found.";
                }
            } elseif ($action === 'remove') {
                $cart->removeFromCart($userId, $productId);
                $message = "Product removed from cart successfully.";
            } elseif ($action === 'update') {
                $quantity = $_POST['quantity'];
                $cart->updateQuantity($userId, $productId, $quantity);
                $message = "Product quantity updated successfully.";
            }
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
}

// Fetch cart items for the user
$cartItems = $cart->getCartItems($userId);
$totalPrice = $cart->getTotalPrice($userId);
$cartIsEmpty = empty($cartItems);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="css/general.css">
    <link rel="stylesheet" href="css/cart.css">
</head>
<body>
    <div class="container">
        <h1>Your Shopping Cart</h1>
        <a href="index.php" class="back-button">Go Back to Homepage</a>
        <?php if (isset($message)): ?>
            <p class="message"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <p class="error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>
        
        <div class="cart-list">
            <?php foreach ($cartItems as $item): ?>
                <div class="cart-item">
                    <img src="<?php echo htmlspecialchars($item['img_url'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8'); ?>">
                    <div>
                        <h3><?php echo htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8'); ?></h3>
                        <p><?php echo htmlspecialchars($item['description'], ENT_QUOTES, 'UTF-8'); ?></p>
                        <p><strong>Price:</strong> <?php echo htmlspecialchars($item['price'], ENT_QUOTES, 'UTF-8'); ?> units</p>
                        <form method="POST" action="">
                            <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($item['product_id'], ENT_QUOTES, 'UTF-8'); ?>">
                            <input type="number" name="quantity" value="<?php echo htmlspecialchars($item['quantity'], ENT_QUOTES, 'UTF-8'); ?>" min="1">
                            <button type="submit" name="action" value="update">Update Quantity</button>
                            <button type="submit" name="action" value="remove">Remove</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="total-price">
            <p><strong>Total Price:</strong> <?php echo htmlspecialchars($totalPrice, ENT_QUOTES, 'UTF-8'); ?> units</p>
        </div>

        <form action="checkout.php" method="GET">
            <button type="submit" class="checkout-button" <?php echo $cartIsEmpty ? 'disabled' : ''; ?>>Go to Checkout</button>
        </form>
    </div>
</body>
</html>