<?php
session_start();
require_once 'classes/Database.php';
require_once 'classes/User.php';
require_once 'classes/Product.php';
require_once 'classes/Cart.php';

// Check if the user is authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Cart</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Your Cart</h1>
        <?php if (isset($message)): ?>
            <p class="success"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <p class="error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($cartItems) > 0): ?>
                    <?php foreach ($cartItems as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($item['price'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td>
                                <form method="POST" action="cart.php">
                                    <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($item['product_id'], ENT_QUOTES, 'UTF-8'); ?>">
                                    <input type="hidden" name="action" value="update">
                                    <input type="number" name="quantity" value="<?php echo htmlspecialchars($item['quantity'], ENT_QUOTES, 'UTF-8'); ?>" min="1">
                                    <button type="submit">Update</button>
                                </form>
                            </td>
                            <td><?php echo htmlspecialchars($item['total'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td>
                                <form method="POST" action="cart.php">
                                    <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($item['product_id'], ENT_QUOTES, 'UTF-8'); ?>">
                                    <input type="hidden" name="action" value="remove">
                                    <button type="submit">Remove</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">Your cart is empty.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <h2>Total Price: <?php echo htmlspecialchars($totalPrice, ENT_QUOTES, 'UTF-8'); ?> units</h2>
        <a href="index.php"><button>Back to Products</button></a>
        <a href="checkout.php"><button>Checkout</button></a>
    </div>
</body>
</html>