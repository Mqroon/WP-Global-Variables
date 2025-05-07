jQuery(document).ready(function($) {
    $('.gv_custom_action_handler').off('submit').on('submit', function(e) {
        e.preventDefault();

        $('#loading_icon').show();
        $('#save_icon').hide();

        var data = $(this).serialize();
        data.custom_input = JSON.stringify(data.custom_input);
        $.post(my_ajax_object.ajax_url, data, function(response) { gv_responseHandle(response); });
    });

    // Seperate functions to avoid duplicate event listeners

    function gv_addEventListener() {
        $('.gv_custom_action_handler').off('submit').on('submit', function(e) {
            e.preventDefault();

            $('#loading_icon').show();
            $('#save_icon').hide();

            var data = $(this).serialize();
            data.custom_input = JSON.stringify(data.custom_input);
            $.post(my_ajax_object.ajax_url, data, function(response) { gv_responseHandle(response); });
        });
    }
    
    function gv_responseHandle(response) {
        var formData = { action: "refresh_variable_data" }
        var serializedData = $.param(formData);
    
        $.ajax({
            url: my_ajax_object.ajax_url, // Replace with your server endpoint
            type: 'POST',
            data: serializedData,
            success: function(response) {
                $("#gv_variables_wrapper").html(response);
                gv_addEventListener();
                $('#loading_icon').hide();
                $('#save_icon').show();    
            },
            error: function(error) {
                console.error('Error submitting form:', error);
                alert('There was an error in updating the data, please refresh.');
            }
        });
    }
});