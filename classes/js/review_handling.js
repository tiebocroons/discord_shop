$(document).ready(function() {
    $('#review-form').submit(function(event) {
        event.preventDefault();
        
        const formData = {
            csrf_token: $('input[name="csrf_token"]').val(),
            user_id: $('input[name="user_id"]').val(),
            product_id: $('input[name="product_id"]').val(),
            comment: $('#comment').val()
        };

        $.ajax({
            type: 'POST',
            url: 'details.php',
            data: JSON.stringify(formData),
            contentType: 'application/json',
            success: function(response) {
                if (response.success) {
                    alert('Review submitted successfully!');
                    location.reload(); // Reload the page to show the new review
                } else {
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