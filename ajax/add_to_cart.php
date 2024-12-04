<?php
session_start();
require_once __DIR__ . "/../classes/Database.php";
require_once __DIR__ . "/../classes/Cart.php";

// Check if the user is authenticated
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'User not authenticated.']);
    exit;
}

// Get the JSON payload
$data = json_decode(file_get_contents('php://input'), true);

// Validate the input
if (isset($data['product_id'], $data['quantity']) && filter_var($data['product_id'], FILTER_VALIDATE_INT) !== false && filter_var($data['quantity'], FILTER_VALIDATE_INT) !== false) {
    $productId = (int)$data['product_id'];
    $quantity = (int)$data['quantity'];
    $userId = $_SESSION['user_id'];

    $cart = new Cart();
    try {
        $cart->addToCart($userId, $productId, $quantity);
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid input.']);
}