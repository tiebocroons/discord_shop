<?php
session_start();
require_once __DIR__ . "/classes/Database.php";
require_once __DIR__ . "/classes/User.php";
require_once __DIR__ . "/classes/Product.php";

// Create User instance and check if user is logged in
$user = new User();
if (!$user->isLoggedIn()) {
    header("Location: login.php");
    exit;
}

// Get the database connection
$conn = Database::getInstance()->getConnection();

// Fetch distinct categories for the filter dropdown
$categoryQuery = "SELECT DISTINCT category FROM products";
$categoryResult = $conn->query($categoryQuery);

// Fetch all products, optionally filtered by category or search term
$category = isset($_GET['category']) ? $_GET['category'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sql = "SELECT * FROM products WHERE 1=1";

if (!empty($category)) {
    $sql .= " AND category = ?";
}
if (!empty($search)) {
    $sql .= " AND (title LIKE ? OR description LIKE ?)";
}

$stmt = $conn->prepare($sql);
$params = [];
if (!empty($category)) {
    $params[] = $category;
}
if (!empty($search)) {
    $params[] = '%' . $search . '%';
    $params[] = '%' . $search . '%';
}
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Product List</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Welcome, <?php echo htmlspecialchars($user->getUsername(), ENT_QUOTES, 'UTF-8'); ?>!</h1>
        <p>You are successfully logged in.</p>
        
        <!-- Admin check for navigation -->
        <?php if ($user->isAdmin()): ?>
            <div class="admin-nav">
                <a href="add_product.php">Add New Product</a>
            </div>
        <?php endif; ?>

        <!-- Navigation link to cart -->
        <div class="nav">
            <a href="cart.php">View Cart</a>
            <a href="change_credentials.php">Change Name/Password</a>
        </div>

        <!-- Filter by Category -->
        <form method="GET" action="">
            <label for="category">Filter by Category:</label>
            <select name="category" id="category">
                <option value="">All</option>
                <?php if ($categoryResult->rowCount() > 0): ?>
                    <?php while ($row = $categoryResult->fetch()): ?>
                        <option value="<?php echo htmlspecialchars($row['category'], ENT_QUOTES, 'UTF-8'); ?>" 
                            <?php echo ($category === $row['category']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($row['category'], ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php endwhile; ?>
                <?php endif; ?>
            </select>
            <button type="submit">Filter</button>
        </form>

        <!-- Search by Name or Description -->
        <form method="GET" action="">
            <label for="search">Search:</label>
            <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>">
            <button type="submit">Search</button>
        </form>

        <h2>Available Products</h2>
        <div class="product-list">
            <?php if (count($products) > 0): ?>
                <?php foreach ($products as $product): ?>
                    <div class="product-item">
                        <img src="<?php echo htmlspecialchars($product['img_url'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($product['title'], ENT_QUOTES, 'UTF-8'); ?>" />
                        <h3><?php echo htmlspecialchars($product['title'], ENT_QUOTES, 'UTF-8'); ?></h3>
                        <p><?php echo htmlspecialchars($product['description'], ENT_QUOTES, 'UTF-8'); ?></p>
                        <p><strong>Price:</strong> <?php echo htmlspecialchars($product['price'], ENT_QUOTES, 'UTF-8'); ?> units</p>
                        <a href="details.php?id=<?php echo htmlspecialchars($product['id'], ENT_QUOTES, 'UTF-8'); ?>">View Details</a>
                        <input type="number" id="quantity-<?php echo htmlspecialchars($product['id'], ENT_QUOTES, 'UTF-8'); ?>" min="1" value="1">
                        <button id="buy-button-<?php echo htmlspecialchars($product['id'], ENT_QUOTES, 'UTF-8'); ?>" onclick="buyProduct(<?php echo htmlspecialchars($product['id'], ENT_QUOTES, 'UTF-8'); ?>)">Buy Product</button>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No products available.</p>
            <?php endif; ?>
        </div>
    </div>

    <script>
    function buyProduct(productId) {
        const quantityInput = document.getElementById(`quantity-${productId}`);
        const quantity = parseInt(quantityInput.value, 10);
        const buyButton = document.getElementById(`buy-button-${productId}`);

        if (isNaN(quantity) || quantity < 1) {
            alert('Please enter a valid quantity.');
            return;
        }

        buyButton.disabled = true;

        fetch('ajax/add_to_cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ product_id: productId, quantity: quantity })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Product added to cart successfully.');
            } else {
                alert('Failed to add product to cart: ' + data.error);
                buyButton.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred: ' + error);
            buyButton.disabled = false;
        });
    }
    </script>
</body>
</html>