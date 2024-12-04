<?php

require_once __DIR__ . "/classes/Database.php";
require_once __DIR__ . "/classes/ReviewManager.php";
session_start();

// Check if the user is authenticated
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'User not authenticated.']);
    exit;
}

// Fetch product details from the database
$conn = Database::getInstance()->getConnection();
$product = null;

$productId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if ($productId === false) {
    echo json_encode(['success' => false, 'error' => 'Invalid product ID.']);
    exit;
}

$stmt = $conn->prepare('SELECT title, description, price, img_url FROM products WHERE id = ?');
$stmt->execute([$productId]);
$product = $stmt->fetch();

if (!$product) {
    error_log("Product not found for ID: $productId");
    echo json_encode(['success' => false, 'error' => 'Product not found.']);
    exit;
}

// Handle review submission via POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    // Simplified validation
    if (!empty($data['user_id']) && !empty($data['product_id']) && !empty(trim($data['comment']))) {
        $userId = (int)$data['user_id'];
        $productId = (int)$data['product_id'];
        $comment = trim($data['comment']);

        $reviewManager = new ReviewManager();
        try {
            $reviewManager->addReview($userId, $productId, $comment);
            echo json_encode(['success' => true, 'logs' => $reviewManager->getLogs()]);
            exit;
        } catch (Exception $e) {
            error_log($e->getMessage());
            echo json_encode(['success' => false, 'error' => $e->getMessage(), 'logs' => $reviewManager->getLogs()]);
            exit;
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid input.']);
        exit;
    }
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- Ensure jQuery is loaded -->
    <script src="classes/js/review_handling.js"></script> <!-- Include the external JavaScript file -->
</head>
<body>
    <!-- Product Details Section -->
    <div id="product-details">
        <h1><?php echo htmlspecialchars($product['title'], ENT_QUOTES, 'UTF-8'); ?></h1>
        <img src="<?php echo htmlspecialchars($product['img_url'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($product['title'], ENT_QUOTES, 'UTF-8'); ?>" />
        <p><?php echo htmlspecialchars($product['description'], ENT_QUOTES, 'UTF-8'); ?></p>
        <p><strong>Price:</strong> <?php echo htmlspecialchars($product['price'], ENT_QUOTES, 'UTF-8'); ?></p>
    </div>

    <!-- Add the review section here -->
    <div id="reviews">
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
            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($_SESSION['user_id'], ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($productId, ENT_QUOTES, 'UTF-8'); ?>">
            <label for="comment">Comment:</label>
            <textarea id="comment" name="comment" required></textarea>
            <button type="submit">Submit Review</button>
        </form>
    </div>
</body>
</html>