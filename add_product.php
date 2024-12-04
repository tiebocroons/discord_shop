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
    <title>Add or Edit Product</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Add or Edit Product</h1>
        <?php if (isset($message)): ?>
            <p class="success"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <p class="error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>
        <form method="POST" action="add_product.php">
            <input type="hidden" id="id" name="id">
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" required>
            <label for="description">Description:</label>
            <input type="text" id="description" name="description" required>
            <label for="price">Price:</label>
            <input type="number" step="0.01" id="price" name="price" required>
            <label for="img_url">Image URL:</label>
            <input type="text" id="img_url" name="img_url" required>
            <label for="category">Category:</label>
            <select id="category" name="category" required>
                <option value="discord_nitro">Discord Nitro</option>
                <option value="discord_classic">Discord Classic</option>
                <option value="profile_effects">Profile Effects</option>
                <option value="banner_effects">Banner Effects</option>
            </select>
            <button type="submit">Save Product</button>
            <button type="submit" name="delete" onclick="return confirm('Are you sure you want to delete this product?')">Delete Product</button>
        </form>
        <h2>All Products</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Image URL</th>
                    <th>Category</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($product['id'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($product['title'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($product['description'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($product['price'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($product['img_url'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($product['category'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td>
                            <button onclick="editProduct(<?php echo htmlspecialchars(json_encode($product), ENT_QUOTES, 'UTF-8'); ?>)">Edit</button>
                            <form method="POST" action="add_product.php" style="display:inline;">
                                <input type="hidden" name="id" value="<?php echo htmlspecialchars($product['id'], ENT_QUOTES, 'UTF-8'); ?>">
                                <button type="submit" name="delete" onclick="return confirm('Are you sure you want to delete this product?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
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