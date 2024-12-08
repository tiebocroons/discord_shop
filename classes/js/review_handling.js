document.getElementById('review-form').addEventListener('submit', function(event) {
    event.preventDefault();

    const productIdElement = document.getElementById('product_id');
    const commentElement = document.getElementById('comment');

    // Debugging output to check if elements are found
    console.log('productIdElement:', productIdElement);
    console.log('commentElement:', commentElement);

    if (!productIdElement || !commentElement) {
        alert('An error occurred: Required form elements are missing.');
        return;
    }

    const formData = {
        product_id: productIdElement.value,
        comment: commentElement.value
    };

    // Debugging output to check form data
    console.log('formData:', formData);

    fetch('ajax/add_comment.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json()) // Read the response as JSON
    .then(data => {
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
    })
    .catch(error => {
        alert('An error occurred: ' + error);
    });
});