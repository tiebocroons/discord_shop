<?php
session_start();
require_once __DIR__ . "/db_connect.php"; // Include DB connection
require_once __DIR__ . "/classes/User.php"; // Include User class

// Check if the user is logged in and is an admin
$user = new User();
if (!$user->isLoggedIn() || !$user->isAdmin()) {
    header("Location: login.php");
    exit;
}

// Initialize variables
$title = $description = $price = $img_url = $category = "";
$error = "";
$success = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $price = trim($_POST['price']);
    $img_url = trim($_POST['img_url']);
    $category = $_POST['category'];

    // Validate input
    if (empty($title) || empty($description) || empty($price) || empty($img_url) || empty($category)) {
        $error = "All fields are required.";
    } elseif (!is_numeric($price) || $price <= 0) {
        $error = "Price must be a positive number.";
    } else {
        // Prepare and execute the SQL statement
        $sql = "INSERT INTO products (title, description, price, img_url, category) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdss", $title, $description, $price, $img_url, $category);

        if ($stmt->execute()) {
            $success = "Product added successfully!";
            // Clear the form after successful submission
            $title = $description = $price = $img_url = $category = "";
        } else {
            $error = "Error adding product: " . $stmt->error;
        }
        $stmt->close();
    }
}

$conn->close(); // Close DB connection
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
        
        <?php if ($error): ?>
            <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
        <?php elseif ($success): ?>
            <p style="color: green;"><?php echo htmlspecialchars($success); ?></p>
        <?php endif; ?>

        <form method="POST" action="add_product.php">
            <label for="title">Product Title</label>
            <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($title); ?>" required>

            <label for="description">Description</label>
            <textarea id="description" name="description" required><?php echo htmlspecialchars($description); ?></textarea>

            <label for="price">Price</label>
            <input type="number" id="price" name="price" value="<?php echo htmlspecialchars($price); ?>" required step="0.01">

            <label for="img_url">Image URL</label>
            <input type="text" id="img_url" name="img_url" value="<?php echo htmlspecialchars($img_url); ?>" required>

            <label for="category">Category</label>
            <select id="category" name="category" required>
                <option value="discord_nitro">Discord Nitro</option>
                <option value="discord_classic">Discord Classic</option>
                <option value="profile_effects">Profile Effects</option>
                <option value="banner_effects">Banner Effects</option>
            </select>

            <button type="submit">Add Product</button>
        </form>
    </div>
</body>
</html>