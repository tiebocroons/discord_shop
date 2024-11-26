<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Details</title>
</head>
<body>
    <!-- Product Details Section -->
    <div id="product-details">
        <h1><?php echo htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?></h1>
        <p><?php echo htmlspecialchars($product['description'], ENT_QUOTES, 'UTF-8'); ?></p>
        <p><strong>Price:</strong> <?php echo htmlspecialchars($product['price'], ENT_QUOTES, 'UTF-8'); ?></p>
        <button id="view-details">View Details</button>
    </div>

    <!-- Add the review section here -->
    <div id="reviews">
        <h2>Reviews</h2>
        <div id="review-list"></div>
        <h3>Add a Review</h3>
        <form id="review-form">
            <input type="hidden" id="product_id" value="<?php echo htmlspecialchars($productId, ENT_QUOTES, 'UTF-8'); ?>">
            <label for="rating">Rating:</label>
            <input type="number" id="rating" name="rating" min="1" max="5" required>
            <label for="comment">Comment:</label>
            <textarea id="comment" name="comment" required></textarea>
            <button type="submit">Submit Review</button>
        </form>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const productId = document.getElementById('product_id').value;

        // Fetch and display reviews
        function fetchReviews() {
            fetch(`details.php?product_id=${productId}`)
                .then(response => response.json())
                .then(data => {
                    const reviewList = document.getElementById('review-list');
                    reviewList.innerHTML = '';
                    data.forEach(review => {
                        const reviewItem = document.createElement('div');
                        reviewItem.innerHTML = `<strong>Rating:</strong> ${review.rating} <br> <strong>Comment:</strong> ${review.comment}`;
                        reviewList.appendChild(reviewItem);
                    });
                });
        }

        // Handle review form submission
        document.getElementById('review-form').addEventListener('submit', function(event) {
            event.preventDefault();
            const rating = document.getElementById('rating').value;
            const comment = document.getElementById('comment').value;

            fetch('details.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ product_id: productId, rating: rating, comment: comment })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    fetchReviews();
                    document.getElementById('review-form').reset();
                } else {
                    alert('Failed to submit review');
                }
            });
        });

        // Initial fetch of reviews
        fetchReviews();
    });
    </script>
</body>
</html>