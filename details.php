<?php
require_once __DIR__ . "/classes/Database.php";
require_once __DIR__ . "/classes/ReviewManager.php";

// Fetch product details
$productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$product = null;

$conn = Database::getInstance()->getConnection();

if ($productId > 0) {
    $stmt = $conn->prepare('SELECT title, description, price, img_url FROM products WHERE id = ?');
    $stmt->execute([$productId]);
    $product = $stmt->fetch();
}

if (!$product) {
    echo "Product not found.";
    exit;
}

// Handle review submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $reviewManager = new ReviewManager();
    try {
        $reviewManager->addReview($data['user_id'], $data['product_id'], $data['comment']);
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        error_log($e->getMessage());
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
            <input type="hidden" id="user_id" value="<?php echo htmlspecialchars($_SESSION['user_id'], ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" id="product_id" value="<?php echo htmlspecialchars($productId, ENT_QUOTES, 'UTF-8'); ?>">
            <label for="comment">Comment:</label>
            <textarea id="comment" name="comment" required></textarea>
            <button type="submit">Submit Review</button>
        </form>
    </div>

    <script>
    $(document).ready(function() {
        const userId = $('#user_id').val();
        const productId = $('#product_id').val();

        // Handle review form submission
        $('#review-form').on('submit', function(event) {
            event.preventDefault();
            const comment = $('#comment').val();

            $.ajax({
                url: 'details.php',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({ user_id: userId, product_id: productId, comment: comment }),
                success: function(data) {
                    if (data.success) {
                        const reviewList = $('#review-list');
                        const reviewItem = $('<div>').addClass('review-item');
                        reviewItem.html(`<strong>Comment:</strong> ${comment}`);
                        reviewList.append(reviewItem);
                        $('#review-form')[0].reset();
                    } else {
                        alert('Failed to submit review: ' + data.error);
                    }
                },
                error: function(xhr, status, error) {
                    alert('An error occurred: ' + error);
                }
            });
        });
    });
    </script>
</body>
</html>