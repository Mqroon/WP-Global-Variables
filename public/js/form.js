jQuery(document).ready(function($) {
    $('.custom_action_handler').on('submit', function(e) {
        e.preventDefault();
        var data = $(this).serialize();
        data.custom_input = JSON.stringify(data.custom_input);
        $.post(my_ajax_object.ajax_url, data, function(response) { responseHandle(response); });
    });

    function addEventListener() {
        $('.form_custom_action_handler').on('submit', function(e) {
            e.preventDefault();
            var data = $(this).serialize();
            data.custom_input = JSON.stringify(data.custom_input);
            $.post(my_ajax_object.ajax_url, data, function(response) { responseHandle(response); });
        });
    }
    
    function responseHandle(response) {
        alert(response);
        
        var formData = { action: "refresh_variable_data" }
        var serializedData = $.param(formData);
    
        $.ajax({
            url: my_ajax_object.ajax_url, // Replace with your server endpoint
            type: 'POST',
            data: serializedData,
            success: function(response) {
                $("#global_variables_wrapper").replaceWith(response);
                addEventListener();
            },
            error: function(error) {
                console.error('Error submitting form:', error);
                alert('There was an error in updating the data, please refresh.');
            }
        });
    }
    
    addEventListener();
});