<?php
session_start();
require_once 'classes/Database.php'; // Include DB connection
require_once 'classes/User.php'; // Include User class

// Create a User object to check if the user is an admin
$user = new User();

// Check if the user is logged in and is an admin
if (!$user->isLoggedIn() || !$user->isAdmin()) {
    header("Location: login.php"); // Redirect if not logged in or not admin
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $title = $_POST['title'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $img_url = $_POST['img_url'];
    $category = $_POST['category'];

    // Prepare the SQL statement
    $sql = "INSERT INTO products (title, description, price, img_url, category) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    // Check for errors in preparing the statement
    if (!$stmt) {
        die("SQL Error: " . $conn->error);
    }

    // Bind the parameters (note the type string must match the number of variables)
    $stmt->bind_param('ssdss', $title, $description, $price, $img_url, $category);

    // Execute the statement
    if ($stmt->execute()) {
        echo "Product added successfully.";
    } else {
        echo "Error adding product: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();
}

// Close DB connection
$conn->close();
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
        <form method="POST" action="add_product.php">
            <label for="title">Title</label>
            <input type="text" id="title" name="title" required>

            <label for="description">Description</label>
            <input type="text" id="description" name="description" required>

            <label for="price">Price</label>
            <input type="number" step="0.01" id="price" name="price" required>

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
        
        <!-- Button to go back to index.php -->
        <form action="index.php" method="GET">
            <button type="submit">Back to Products</button>
        </form>
    </div>
</body>
</html>