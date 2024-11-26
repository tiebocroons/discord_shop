<?php

require_once __DIR__ . "/classes/Database.php";
require_once __DIR__ . "/classes/ReviewManager.php";
session_start();

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'User not authenticated.']);
    exit;
}

// Fetch product details from the database
$productId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]);
if ($productId === false) {
    echo json_encode(['success' => false, 'error' => 'Invalid product ID.']);
    exit;
}

$conn = Database::getInstance()->getConnection();
$product = null;

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
    error_log("Received data: " . print_r($data, true)); // Log the received data for debugging
    if (isset($data['csrf_token'], $_SESSION['csrf_token']) && $data['csrf_token'] === $_SESSION['csrf_token']) {
        if (isset($data['user_id'], $data['product_id'], $data['comment']) && 
            is_int($data['user_id']) && 
            is_int($data['product_id']) && 
            is_string($data['comment']) && 
            !empty(trim($data['comment']))) {
            
            $reviewManager = new ReviewManager();
            try {
                $reviewManager->addReview($data['user_id'], $data['product_id'], $data['comment']);
                echo json_encode(['success' => true]);
                exit;
            } catch (Exception $e) {
                error_log($e->getMessage());
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
                exit;
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'Invalid input.']);
            exit;
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid CSRF token.']);
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
            <input type="hidden" id="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
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
            const csrfToken = $('#csrf_token').val();

            console.log({
                user_id: userId,
                product_id: productId,
                comment: comment,
                csrf_token: csrfToken
            }); // Log the data being sent for debugging

            $.ajax({
                url: 'details.php',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({
                    user_id: userId,
                    product_id: productId,
                    comment: comment,
                    csrf_token: csrfToken
                }),
                success: function(response) {
                    console.log(response); // Log the response for debugging
                    if (response.success) {
                        const reviewList = $('#review-list');
                        const reviewItem = $('<div>').addClass('review-item');
                        reviewItem.html(`<strong>Comment:</strong> ${comment}`);
                        reviewList.append(reviewItem);
                        $('#review-form')[0].reset();
                    } else {
                        console.error('Failed to submit review: ' + response.error); // Log the error for debugging
                        alert('Failed to submit review: ' + response.error);
                    }
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText); // Log the error response for debugging
                    alert('An error occurred: ' + error);
                }
            });
        });
    });
    </script>
</body>
</html>