document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('review-form').addEventListener('submit', function(event) {
        event.preventDefault();

        const formData = {
            user_id: parseInt(document.querySelector('input[name="user_id"]').value, 10), // Convert user_id to an integer
            product_id: parseInt(document.querySelector('input[name="product_id"]').value, 10), // Convert product_id to an integer
            comment: document.getElementById('comment').value
        };

        fetch('../ajax/add_comment.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.text()) // Read the response as text
        .then(text => {
            try {
                const data = JSON.parse(text); // Try to parse the response as JSON
                if (data.success) {
                    const reviewList = document.getElementById('review-list');
                    const reviewItem = document.createElement('div');
                    reviewItem.classList.add('review-item');
                    reviewItem.innerHTML = `<strong>Comment:</strong> ${formData.comment}`;
                    reviewList.appendChild(reviewItem);
                    document.getElementById('review-form').reset();
                } else {
                    alert('Failed to submit review: ' + data.error);
                }
            } catch (error) {
                console.error('Response is not valid JSON:', text); // Log the response text for debugging
                alert('An error occurred: ' + error);
            }
        })
        .catch(error => {
            console.error('Fetch error: ' + error); // Log the fetch error for debugging
            alert('An error occurred: ' + error);
        });
    });
});