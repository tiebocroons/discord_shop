<?php
session_start();
require_once __DIR__ . "/classes/User.php"; // Include User class
require_once __DIR__ . "/db_connect.php"; // Include DB connection

// Create User instance and check if user is logged in
$user = new User();
if (!$user->isLoggedIn()) {
    header("Location: login.php");
    exit;
}

// Fetch distinct categories for the filter dropdown
$categoryQuery = "SELECT DISTINCT category FROM products";
$categoryResult = $conn->query($categoryQuery);

// Fetch all products, optionally filtered by category
$category = isset($_GET['category']) ? $_GET['category'] : '';
$sql = "SELECT * FROM products";
if (!empty($category)) {
    $sql .= " WHERE category = ?";
}

$stmt = $conn->prepare($sql);
if (!empty($category)) {
    $stmt->execute([$category]);
} else {
    $stmt->execute();
}
$products = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Product Overview</title>
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
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No products available for the selected category.</p>
            <?php endif; ?>
        </div>

        <form action="logout.php" method="POST">
            <button type="submit">Logout</button>
        </form>
    </div>
</body>
</html>

<?php
// Close DB connection
$conn = null;
?>