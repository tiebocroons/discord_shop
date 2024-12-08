<?php
session_start();
require_once __DIR__ . "/../classes/Database.php";
require_once __DIR__ . "/../classes/Product.php";

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'User not authenticated.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!empty($data['id'])) {
    $productId = (int)$data['id'];

    $productManager = new Product();

    try {
        $productManager->deleteProduct($productId);
        echo json_encode(['success' => true]);
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