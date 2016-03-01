(function($){

    $(function(){

        $('.freezy-stripe-submit').click(function(e){

            e.preventDefault();
            var id = $(this).data('id');
            var value = $('#freezy-stripe-price-'+id).val();

            for (var h=0; h<freezy_stripe_handlers.length; h++) {
                if (freezy_stripe_handlers[h].id == id) {
                    freezy_stripe_handlers[h].handler.open({
                        name: $(this).data('name'),
                        description: $(this).data('description'),
                        amount: value,
                        currency: $(this).data('currency'),
                        billingAddress: true,
                        shippingAddress: true
                    });
                }
            }
        });

        $('.freezy-stripe-alert').each(function(index){
            if (index == 0) {
                var id = $(this).data('id');
                $('#'+id).trigger('click');
            }
        });

        if (window.location.protocol != 'https:') {
            $('.freezy-stripe-ssl-check').each(function(){
                $(this).addClass('alert').addClass('alert-danger').html($(this).data('if-error-show'));
            });
        }
    });

})(jQuery);