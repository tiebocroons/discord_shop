<?php
session_start();
require_once __DIR__ . "/../classes/Database.php";
require_once __DIR__ . "/../classes/Comment.php";

// Check if the user is authenticated
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'User not authenticated.']);
    exit;
}

// Get the JSON payload
$data = json_decode(file_get_contents('php://input'), true);

// Log the received data for debugging
error_log("Received data: " . print_r($data, true));

// Validate the input
if (!empty($data['user_id']) && !empty($data['product_id']) && !empty(trim($data['comment']))) {
    $userId = (int)$data['user_id'];
    $productId = (int)$data['product_id'];
    $commentText = trim($data['comment']);

    $comment = new Comment();
    $comment->setComment($commentText);
    $comment->setUserId($userId);
    $comment->setProductId($productId);

    try {
        $comment->save();
        echo json_encode(['success' => true, 'comment' => $commentText]);
        exit;
    } catch (Exception $e) {
        error_log($e->getMessage());
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        exit;
    }
} else {
    error_log("Invalid input: " . print_r($data, true)); // Log the invalid input for debugging
    echo json_encode(['success' => false, 'error' => 'Invalid input.']);
    exit;
}