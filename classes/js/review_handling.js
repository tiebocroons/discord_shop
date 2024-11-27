$(document).ready(function() {
    const userId = parseInt($('#user_id').val(), 10);
    const productId = parseInt($('#product_id').val(), 10);

    // Handle review form submission
    $('#review-form').on('submit', function(event) {
        event.preventDefault();
        const comment = $('#comment').val();
        const csrfToken = $('#csrf_token').val();

        console.log({
            user_id: userId,
            product_id: productId,
            comment: comment,
            csrf_token: csrfToken
        }); // Log the data being sent for debugging

        $.ajax({
            url: 'details.php',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                user_id: userId,
                product_id: productId,
                comment: comment,
                csrf_token: csrfToken
            }),
            success: function(response) {
                console.log(response); // Log the response for debugging
                if (response.success) {
                    const reviewList = $('#review-list');
                    const reviewItem = $('<div>').addClass('review-item');
                    reviewItem.html(`<strong>Comment:</strong> ${comment}`);
                    reviewList.append(reviewItem);
                    $('#review-form')[0].reset();
                } else {
                    console.error('Failed to submit review: ' + response.error); // Log the error for debugging
                    if (response.logs) {
                        response.logs.forEach(log => console.error('Server log: ' + log)); // Log the server logs for debugging
                    }
                    if (isset($data['csrf_token'], $_SESSION['csrf_token']) && $data['csrf_token'] === $_SESSION['csrf_token']) {
                        // Proceed with the review submission
                    } else {
                        console.error('Invalid CSRF token.');
                        alert('Invalid CSRF token.');
                    }                    
                }
            },
            error: function(xhr, error) {
                console.error('AJAX error: ' + error); // Log the AJAX error for debugging
                console.error('Response text: ' + xhr.responseText); // Log the response text for debugging
                alert('An error occurred: ' + error);
            }
        }).done(function() {
            console.log("Request completed");
        }).fail(function(textStatus) {
            console.error("Request failed: " + textStatus);
        }).always(function() {
console.log("Request ended");
        });
    });
});