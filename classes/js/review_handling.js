document.getElementById('review-form').addEventListener('submit', function(event) {
    event.preventDefault();

    const formData = {
        product_id: document.getElementById('product_id').value,
        comment: document.getElementById('comment').value
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