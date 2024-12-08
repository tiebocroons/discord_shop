<?php
session_start();
require_once __DIR__ . "/../classes/Database.php";
require_once __DIR__ . "/../classes/Comment.php";

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'User not authenticated.']);
    exit;
}

// Get the JSON payload
$data = json_decode(file_get_contents('php://input'), true);

// Validate the input
if (!empty($data['product_id']) && !empty(trim($data['comment']))) {
    $userId = $_SESSION['user_id'];
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
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        exit;
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid input.']);
    exit;
}
?>