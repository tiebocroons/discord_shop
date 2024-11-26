<?php

require_once __DIR__ . "/classes/Database.php";
require_once __DIR__ . "/classes/ReviewManager.php";
session_start();

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Check if the user is authenticated
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

try {
    $conn = Database::getInstance()->getConnection();
} catch (Exception $e) {
    error_log("Database connection failed: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Database connection failed.']);
    exit;
}
$product = null;

$stmt = $conn->prepare('SELECT title, description, price, img_url FROM products WHERE id = ?');
if (!$stmt) {
    error_log("Prepare statement failed: " . $conn->errorInfo()[2]);
    echo json_encode(['success' => false, 'error' => 'Prepare statement failed.']);
    exit;
}
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
    <title>Document</title>
    <!-- Add your CSS and other head elements here -->
</head>
<body>
    <!-- Your body content goes here -->

    <script>
        $(document).ready(function() {
            $('#submitReview').click(function(event) {
                event.preventDefault();
                $.ajax({
                    type: 'POST',
                    url: 'submit_review.php',
                    data: $('#reviewForm').serialize(),
                    success: function(response) {
                        if (response.success) {
                            alert('Review submitted successfully!');
                        } else {
                            response.logs.forEach(log => console.error('Server log: ' + log)); // Log the server logs for debugging
                        }
                        alert('Failed to submit review: ' + response.error);
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX error: ' + error); // Log the AJAX error for debugging
                        console.error('Response text: ' + xhr.responseText); // Log the response text for debugging
                        alert('An error occurred: ' + error);
                    }
                });
            });
        });
    </script>
</body>
</html>