<?php
session_start();
require_once __DIR__ . "/classes/User.php"; // Include User class
require_once __DIR__ . "/db_connect.php"; // Include DB connection

// Initialize User class with database connection
$user = new User($conn); // Pass the DB connection to User class

// Check if user is logged in
if (!$user->isLoggedIn()) {
    header("Location: login.php");
    exit;
}

// Fetch all products from the database
$sql = "SELECT * FROM products";
$result = $conn->query($sql);

// Check for query execution error
if ($result === false) {
    die("Error fetching products: " . $conn->error);
}
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
        <h1>Welcome, <?php echo htmlspecialchars($user->getUsername()); ?>!</h1>
        <p>You are successfully logged in.</p>
        
        <h2>Available Products</h2>
        <div class="product-list">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($product = $result->fetch_assoc()): ?>
                    <div class="product-item">
                        <img src="<?php echo htmlspecialchars($product['img_url']); ?>" alt="<?php echo htmlspecialchars($product['title']); ?>" />
                        <h3><?php echo htmlspecialchars($product['title']); ?></h3>
                        <p><?php echo htmlspecialchars($product['description']); ?></p>
                        <p><strong>Price:</strong> <?php echo htmlspecialchars($product['price']); ?> units</p>
                        <a href="product.php?id=<?php echo $product['id']; ?>">View Details</a>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No products available.</p>
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
$conn->close();
?>
