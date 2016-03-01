<?php

$id = uniqid();
$stripe_keys = \FreezyStripe\Controller::getStripeKeys();
$title = ( strlen( get_option( 'freezy_stripe_company_name', '' ) ) > 0 ) ? get_option( 'freezy_stripe_company_name' ) : get_option( 'blogname' );
$logo = get_option( 'freezy_stripe_company_logo', '' );
$currencies = $this->get_currencies();
$currency = ( strlen( get_option( 'freezy_stripe_currency' ) ) == 0 ) ? 'USD' : get_option( 'freezy_stripe_currency' );
if ( ! array_key_exists( $currency, $currencies ) )
{
	$currency = 'USD';
}
$suppress = ( get_option( 'freezy_stripe_suppress_https_warning' ) == 1 ) ? TRUE : FALSE;

\FreezyStripe\Stripe\Stripe::setApiKey( $stripe_keys['secret'] );

$price = $this->get_attribute( 'price' );
$prices = explode( ',', $price );

foreach ($prices as $index => $price)
{
	$price = preg_replace( '/[^0-9\.]/', '', $price );
	if ( strlen( $price ) == 0 )
	{
		unset( $prices[ $index ] );
	}
	else
	{
		$prices[ $index ] = $price;
	}
}

?>

<?php if ( isset( $_POST['freezy_stripe_action'] ) && ! isset( $freezy_stripe_error_posted ) ) { ?>

	<div class="freezy-stripe-alert" id="freezy-stripe-error-<?php echo $id; ?>" data-id="freezy-a-error-<?php echo $id; ?>" style="display:none">
		<div class="alert alert-danger">
			<?php _e( 'There was a problem charging your credit card.', 'freezy-stripe' ); ?>
		</div>
	</div>
	<a href="#TB_inline?width=600&height=550&inlineId=freezy-stripe-error-<?php echo $id; ?>" id="freezy-a-error-<?php echo $id; ?>" class="thickbox"></a>

	<?php $freezy_stripe_error_posted = TRUE; ?>

<?php } ?>

<?php if ( ! isset( $_POST['freezy_stripe_action'] ) && isset( $_GET['freezy'] ) && $_GET['freezy'] == 'success' && ! isset( $freezy_stripe_success_posted ) ) { ?>

	<div class="freezy-stripe-alert" id="freezy-stripe-success-<?php echo $id; ?>" data-id="freezy-a-success-<?php echo $id; ?>" style="display:none">
		<div class="alert alert-success">
			<?php _e( 'Success! Your card was charged.', 'freezy-stripe' ); ?>
		</div>
	</div>
	<a href="#TB_inline?width=300&height=200&inlineId=freezy-stripe-success-<?php echo $id; ?>" id="freezy-a-success-<?php echo $id; ?>" class="thickbox"></a>

	<?php $freezy_stripe_success_posted = TRUE; ?>

<?php } ?>

<?php if ( strlen( $stripe_keys['pub'] ) > 0 && strlen( $stripe_keys['secret'] ) > 0 && count( $prices ) > 0 ) { ?>

	<script>

		if (typeof freezy_stripe_handlers === 'undefined') {
			var freezy_stripe_handlers = [];
		}

		var freezy_stripe_handler = StripeCheckout.configure({
			key: '<?php echo $stripe_keys['pub']; ?>',
			<?php if ( strlen( $logo ) > 0 ) { ?>
				image: '<?php echo $logo; ?>',
			<?php } ?>
			locale: 'auto',
			token: function(token) {
				jQuery('#freezy-stripe-token-<?php echo $id; ?>').val(token.id);
				jQuery('#freezy-stripe-form-<?php echo $id; ?>').submit();
			}
		});

		freezy_stripe_handlers.push({
			id: '<?php echo $id; ?>',
			handler: freezy_stripe_handler
		});

		// Close Checkout on page navigation
		jQuery(window).on('popstate', function() {
			for (var h=0; h<freezy_stripe_handlers.length; h++) {
				freezy_stripe_handlers[h].handler.close();
			}
		});

	</script>

	<div class="freezy-stripe-form-container">

		<form method="post" autocomplete="off" id="freezy-stripe-form-<?php echo $id; ?>">
			<?php wp_nonce_field( 'freezy-stripe-nonce' ); ?>
			<input type="hidden" name="freezy_stripe_action" value="charge">
			<input type="hidden" name="token" id="freezy-stripe-token-<?php echo $id; ?>">
			<input type="hidden" name="description" value="<?php echo esc_html( $this->get_attribute( 'name' ) ); ?>">
			<strong>
				<?php echo $this->get_attribute( 'name' ) ; ?>
			</strong>
			<br>
			<?php if ( count( $prices ) > 1 ) { ?>
				<select name="price" id="freezy-stripe-price-<?php echo $id; ?>">
					<?php foreach ($prices as $price) { ?>
						<option value="<?php echo round( $price * 100 ); ?>">
							<?php echo number_format( $price, 2 ); ?>
						</option>
					<?php } ?>
				</select>
			<?php } else { ?>
				<em>
					<?php echo number_format( $prices[0], 2 ); ?>
				</em>
				<input type="hidden" name="price" id="freezy-stripe-price-<?php echo $id; ?>" value="<?php echo round( $prices[0] * 100 ); ?>">
			<?php } ?>
			<br>
			<button
				type="submit"
				class="freezy-stripe-submit"
				data-currency="<?php echo $currency; ?>"
				data-name="<?php echo esc_html( $title ); ?>"
				data-description="<?php echo esc_html( $this->get_attribute( 'name' ) ); ?>"
				data-id="<?php echo $id; ?>">
				<?php _e( 'Pay with Card', 'freezy-stripe' ); ?>
			</button>

			<?php if ( ! $suppress) { ?>
				<div class="freezy-stripe-ssl-check" data-if-error-show="<?php _e( 'Warning: Your payment may not be secure.<br>Always check for HTTPS in the URL.', 'freezy-stripe' ); ?>"></div>
			<?php } ?>

		</form>

	</div>
<?php } ?>
