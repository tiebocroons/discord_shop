<?php
session_start();
require_once 'classes/Database.php'; // Include DB connection
require_once 'classes/User.php'; // Include User class
require_once 'classes/Product.php'; // Include Product class

// Create a User object to check if the user is an admin
$user = new User();

// Check if the user is logged in and is an admin
if (!$user->isLoggedIn() || !$user->isAdmin()) {
    header("Location: login.php"); // Redirect if not logged in or not admin
    exit;
}

// Get the database connection
$conn = Database::getInstance()->getConnection();
$productManager = new Product();

// Handle form submission for adding, updating, or deleting a product
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = isset($_POST['id']) ? $_POST['id'] : null;
    $title = $_POST['title'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $img_url = $_POST['img_url'];
    $category = $_POST['category'];

    try {
        if (isset($_POST['delete'])) {
            // Delete product
            $productManager->deleteProduct($id);
            $message = "Product deleted successfully.";
        } elseif ($id) {
            // Update product
            $productManager->updateProduct($id, $title, $description, $price, $img_url, $category);
            $message = "Product updated successfully.";
        } else {
            // Add product
            $productManager->addProduct($title, $description, $price, $img_url, $category);
            $message = "Product added successfully.";
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Fetch all products
$products = $productManager->getProducts();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Add Product</title>
    <link rel="stylesheet" href="css/general.css">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <div class="container">
        <h1>Add / Update Product</h1>
        <?php if (isset($message)): ?>
            <p class="message"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <p class="error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>
        <form method="POST" action="">
            <input type="hidden" name="id" id="id" value="">
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" required>
            <label for="description">Description:</label>
            <textarea id="description" name="description" required></textarea>
            <label for="price">Price:</label>
            <input type="number" id="price" name="price" required>
            <label for="img_url">Image URL:</label>
            <input type="url" id="img_url" name="img_url" required>
            <label for="category">Category:</label>
            <input type="text" id="category" name="category" required>
            <button type="submit">Save Product</button>
            <button type="button" class="back-button" onclick="window.location.href='index.php'">Go to Homepage</button>
        </form>

        <div class="product-list">
            <h2>Existing Products</h2>
            <?php foreach ($products as $product): ?>
                <div class="product-item">
                    <img src="<?php echo htmlspecialchars($product['img_url'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($product['title'], ENT_QUOTES, 'UTF-8'); ?>">
                    <div>
                        <h3><?php echo htmlspecialchars($product['title'], ENT_QUOTES, 'UTF-8'); ?></h3>
                        <p><?php echo htmlspecialchars($product['description'], ENT_QUOTES, 'UTF-8'); ?></p>
                        <p><strong>Price:</strong> <?php echo htmlspecialchars($product['price'], ENT_QUOTES, 'UTF-8'); ?> units</p>
                        <p><strong>Category:</strong> <?php echo htmlspecialchars($product['category'], ENT_QUOTES, 'UTF-8'); ?></p>
                    </div>
                    <div>
                        <button type="button" onclick="editProduct(<?php echo htmlspecialchars(json_encode($product), ENT_QUOTES, 'UTF-8'); ?>)">Edit</button>
                        <form method="POST" action="" style="display: inline;">
                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($product['id'], ENT_QUOTES, 'UTF-8'); ?>">
                            <button type="submit" name="delete" class="back-button">Delete</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
    function editProduct(product) {
        document.getElementById('id').value = product.id;
        document.getElementById('title').value = product.title;
        document.getElementById('description').value = product.description;
        document.getElementById('price').value = product.price;
        document.getElementById('img_url').value = product.img_url;
        document.getElementById('category').value = product.category;
    }
    </script>
</body>
</html>