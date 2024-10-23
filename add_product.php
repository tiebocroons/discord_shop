<?php
session_start();
require_once __DIR__ . "/classes/User.php"; // Include User class
require_once __DIR__ . "/db_connect.php"; // Include DB connection

// Check if user is logged in
$user = new User();
if (!$user->isLoggedIn() || !$user->isAdmin()) {
    header("Location: login.php");
    exit;
}

// Handle form submission for adding a new product
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $img_url = $_POST['img_url'];
    $category = $_POST['category'];

    // Prepare SQL statement to insert new product
    $sql = "INSERT INTO products (title, description, price, img_url, category) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssds", $title, $description, $price, $img_url, $category);

    if ($stmt->execute()) {
        $success = "Product added successfully!";
    } else {
        $error = "Error adding product: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Add Product</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Add New Product</h1>
        <?php
        if (isset($success)) {
            echo "<p style='color: green;'>$success</p>";
        }
        if (isset($error)) {
            echo "<p style='color: red;'>$error</p>";
        }
        ?>
        <form method="POST" action="add_product.php">
            <label for="title">Title</label>
            <input type="text" id="title" name="title" required>

            <label for="description">Description</label>
            <textarea id="description" name="description" required></textarea>

            <label for="price">Price</label>
            <input type="number" id="price" name="price" step="0.01" required>

            <label for="img_url">Image URL</label>
            <input type="text" id="img_url" name="img_url" required>

            <label for="category">Category</label>
            <select id="category" name="category" required>
                <option value="discord_nitro">Discord Nitro</option>
                <option value="discord_classic">Discord Classic</option>
                <option value="profile_effects">Profile Effects</option>
                <option value="banner_effects">Banner Effects</option>
            </select>

            <button type="submit">Add Product</button>
        </form>
        
        <!-- Navigation link to go back to index.php -->
        <a href="index.php" class="back-link">Back to Product List</a>
    </div>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>