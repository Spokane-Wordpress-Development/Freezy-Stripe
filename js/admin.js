(function($){

    $(function(){

        $('#freezy-stripe-upload-logo').click(function(e){

            e.preventDefault();

            // If the media frame already exists, reopen it.
            if ( file_frame ) {
                file_frame.open();
                return;
            }

            // Create the media frame.
            file_frame = wp.media.frames.file_frame = wp.media({
                title: 'Insert Images',
                button: {
                    text: 'Insert'
                },
                multiple: true  // Set to true to allow multiple files to be selected
            });

            // When an image is selected, run a callback.
            file_frame.on( 'select', function() {

                var selection = file_frame.state().get('selection');

                selection.map( function( attachment ) {

                    attachment = attachment.toJSON();
                    $('#freezy-stripe-company-logo').html('<img src="' + attachment.url + '">');
                    $('#freezy_stripe_company_logo').val(attachment.url);
                    $('#freezy-stripe-remove-logo').show();

                });
            });

            // Finally, open the modal
            file_frame.open();
        });

        $('#freezy-stripe-remove-logo').click(function(e){

            e.preventDefault();
            $('#freezy-stripe-company-logo').html('');
            $('#freezy_stripe_company_logo').val('');
            $('#freezy-stripe-remove-logo').hide();

        });

    });

})(jQuery);
