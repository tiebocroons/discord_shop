$(document).ready(function() {
    $('#review-form').submit(function(event) {
        event.preventDefault();
        
        const formData = {
            csrf_token: $('input[name="csrf_token"]').val(),
            user_id: $('input[name="user_id"]').val(),
            product_id: $('input[name="product_id"]').val(),
            comment: $('#comment').val()
        };

        console.log(formData); // Log the data being sent for debugging

        $.ajax({
            type: 'POST',
            url: 'details.php',
            data: JSON.stringify(formData),
            contentType: 'application/json',
            success: function(response) {
                console.log(response); // Log the response for debugging
                if (response.success) {
                    const reviewList = $('#review-list');
                    const reviewItem = $('<div>').addClass('review-item');
                    reviewItem.html(`<strong>Comment:</strong> ${formData.comment}`);
                    reviewList.append(reviewItem);
                    $('#review-form')[0].reset();
                } else {
                    console.error('Failed to submit review: ' + response.error); // Log the error for debugging
                    if (response.logs) {
                        response.logs.forEach(log => console.error('Server log: ' + log)); // Log the server logs for debugging
                    }
                    alert('Failed to submit review: ' + response.error);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error: ' + error); // Log the AJAX error for debugging
                console.error('Response text: ' + xhr.responseText); // Log the response text for debugging
                alert('An error occurred: ' + error);
            }
        });
    });
});