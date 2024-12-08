<?php

require_once __DIR__ . "/classes/Database.php";
require_once __DIR__ . "/classes/ReviewManager.php";
session_start();

// Check if the user is authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch product details from the database
$conn = Database::getInstance()->getConnection();
$product = null;

$productId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if ($productId === false) {
    echo "Invalid product ID.";
    exit;
}

$stmt = $conn->prepare('SELECT title, description, price, img_url FROM products WHERE id = ?');
$stmt->execute([$productId]);
$product = $stmt->fetch();

if (!$product) {
    error_log("Product not found for ID: $productId");
    echo "Product not found.";
    exit;
}

// Fetch reviews
$reviewManager = new ReviewManager();
$reviews = $reviewManager->fetchReviews($productId);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Details</title>
    <link rel="stylesheet" href="css/general.css">
    <link rel="stylesheet" href="css/details.css">
</head>
<body>
    <div class="container">
        <a href="index.php" class="back-arrow">&larr; Back to Products</a>
        <div class="product-details">
            <h1><?php echo htmlspecialchars($product['title'], ENT_QUOTES, 'UTF-8'); ?></h1>
            <img src="<?php echo htmlspecialchars($product['img_url'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($product['title'], ENT_QUOTES, 'UTF-8'); ?>" />
            <p><?php echo htmlspecialchars($product['description'], ENT_QUOTES, 'UTF-8'); ?></p>
            <p><strong>Price:</strong> <?php echo htmlspecialchars($product['price'], ENT_QUOTES, 'UTF-8'); ?> units</p>
        </div>

        <h2>Reviews</h2>
        <div id="review-list">
            <?php foreach ($reviews as $review): ?>
                <div class="review-item">
                    <strong>Comment:</strong> <?php echo htmlspecialchars($review['comment'], ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endforeach; ?>
        </div>
        <h3>Add a Review</h3>
        <form id="review-form">
            <input type="hidden" class="user_id" name="user_id" value="<?php echo htmlspecialchars($_SESSION['user_id'], ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" class="product_id" name="product_id" value="<?php echo htmlspecialchars($productId, ENT_QUOTES, 'UTF-8'); ?>">
            <label for="comment">Comment:</label>
            <textarea id="comment" name="comment" required></textarea>
            <button type="submit">Submit Review</button>
        </form>
    </div>

    <script src="classes/js/review_handling.js"></script>
</body>
</html>