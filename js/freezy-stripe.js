(function($){

    $(function(){

        $('.freezy-stripe-submit').click(function(e){

            e.preventDefault();
            var id = $(this).data('id');
            var value = $('#freezy-stripe-price-'+id).val();

            freezy_stripe_handler.open({
                name: $(this).data('name'),
                description: $(this).data('description'),
                amount: value,
                currency: $(this).data('currency'),
                billingAddress: true,
                shippingAddress: true
            });
        });

        $('.freezy-stripe-error, .freezy-stripe-success').each(function(index){
            if (index == 0) {
                $(this).show();
            }
        });

        if (window.location.protocol != 'https:') {
            $('.freezy-stripe-ssl-check').each(function(){
                $(this).addClass('alert').addClass('alert-danger').html($(this).data('if-error-show'));
            });
        }

    });

})(jQuery);