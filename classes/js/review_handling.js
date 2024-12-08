document.getElementById('review-form').addEventListener('submit', function(event) {
    event.preventDefault();
    const formData = {
        product_id: productIdElement.value,
        comment: commentElement.value
    };
    fetch('ajax/add_comment.php', {
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
            alert('An error occurred: ' + error);
        }
    })
    .catch(error => {
        alert('An error occurred: ' + error);
    });
});