document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('review-form').addEventListener('submit', function(event) {
        event.preventDefault();
        
        const formData = {
            csrf_token: document.querySelector('input[name="csrf_token"]').value, // CSRF token as a string
            user_id: parseInt(document.querySelector('input[name="user_id"]').value, 10), // Convert user_id to an integer
            product_id: parseInt(document.querySelector('input[name="product_id"]').value, 10), // Convert product_id to an integer
            comment: document.getElementById('comment').value
        };

        console.log("Form data being sent:", formData); // Log the data being sent for debugging

        fetch('details.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            console.log(data); // Log the response for debugging
            if (data.success) {
                const reviewList = document.getElementById('review-list');
                const reviewItem = document.createElement('div');
                reviewItem.classList.add('review-item');
                reviewItem.innerHTML = `<strong>Comment:</strong> ${formData.comment}`;
                reviewList.appendChild(reviewItem);
                document.getElementById('review-form').reset();
            } else {
                console.error('Failed to submit review: ' + data.error); // Log the error for debugging
                if (data.logs) {
                    data.logs.forEach(log => console.error('Server log: ' + log)); // Log the server logs for debugging
                }
                alert('Failed to submit review: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Fetch error: ' + error); // Log the fetch error for debugging
            alert('An error occurred: ' + error);
        });
    });
});