<div class="wrap">

	<h1>
		Freezy Stripe <?php _e('Settings', 'freezy-stripe'); ?>
		<a href="#shortcode" class="page-title-action">
			<?php _e( 'Click to See Shortcode Instructions', 'freezy-stripe' ); ?>
		</a>
	</h1>

	<form method="post" action="options.php" autocomplete="off">

		<?php

		settings_fields('freezy_stripe_settings');
		do_settings_sections( 'freezy_stripe_settings' );
		$currencies = $this->get_currencies();
		$currency = ( strlen( get_option( 'freezy_stripe_currency' ) ) == 0 ) ? 'USD' : get_option( 'freezy_stripe_currency' );
		if ( ! array_key_exists( $currency, $currencies ) )
		{
			$currency = 'USD';
		}
		$suppress = ( get_option( 'freezy_stripe_suppress_https_warning' ) == 1 ) ? 1 : 0;

		?>

		<table class="form-table">
			<thead>
				<tr>
					<th></th>
					<th><?php _e('Current Value', 'freezy-stripe'); ?></th>
					<th><?php _e('Change to', 'freezy-stripe'); ?></th>
				</tr>
			</thead>
			<tr>
				<td colspan="2">
					<?php _e('In order to accept credit card payments on the website, you must fill in the API keys. They can be found in your Stripe account by clicking on "My Account" (usually in the top-right corner of your account) and then choosing the "API Keys" icon', 'freezy-stripe'); ?>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="freezy_stripe_test_secret_key">
						Stripe Test Secret Key
					</label>
				</th>
				<td><?php echo get_option( 'freezy_stripe_test_secret_key', '<span style="color:red">' . __( 'NOT SET', 'freezy-stripe' ) . '</span>' ) ; ?></td>
				<td><input type="text" id="freezy_stripe_test_secret_key" name="freezy_stripe_test_secret_key" value="<?php echo esc_attr( get_option('freezy_stripe_test_secret_key') ); ?>" /></td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="freezy_stripe_test_pub_key">
						Stripe Test Publishable Key
					</label>
				</th>
				<td><?php echo get_option('freezy_stripe_test_pub_key', '<span style="color:red">' . __( 'NOT SET', 'freezy-stripe' ) . '</span>' ) ; ?></td>
				<td><input type="text" id="freezy_stripe_test_pub_key" name="freezy_stripe_test_pub_key" value="<?php echo esc_attr( get_option('freezy_stripe_test_pub_key') ); ?>" /></td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="freezy_stripe_live_secret_key">
						Stripe Live Secret Key
					</label>
				</th>
				<td><?php echo get_option( 'freezy_stripe_live_secret_key', '<span style="color:red">' . __( 'NOT SET', 'freezy-stripe' ) . '</span>' ); ?></td>
				<td><input type="text" id="freezy_stripe_live_secret_key" name="freezy_stripe_live_secret_key" value="<?php echo esc_attr( get_option('freezy_stripe_live_secret_key') ); ?>" /></td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="freezy_stripe_live_pub_key">
						Stripe Live Publishable Key
					</label>
				</th>
				<td><?php echo get_option( 'freezy_stripe_live_pub_key', '<span style="color:red">' . __( 'NOT SET', 'freezy-stripe' ) . '</span>' ); ?></td>
				<td><input type="text" id="freezy_stripe_live_pub_key" name="freezy_stripe_live_pub_key" value="<?php echo esc_attr( get_option('freezy_stripe_live_pub_key') ); ?>" /></td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="freezy_stripe_mode">
						<?php _e( 'Mode', 'freezy-stripe' ); ?>
					</label>
				</th>
				<td>
					<?php echo ( get_option( 'freezy_stripe_mode' ) == 'live' ) ? __( 'Live Mode', 'freezy-stripe' ) : __( 'Test Mode', 'freezy-stripe' ) ?>
				</td>
				<td>
					<select id="freezy_stripe_mode" name="freezy_stripe_mode">
						<option value="live"<?php if ( get_option( 'freezy_stripe_mode' ) == 'live' ) { ?> selected<?php } ?>>
							<?php _e( 'Live Mode', 'freezy-stripe' ); ?>
						</option>
						<option value="test"<?php if ( get_option('freezy_stripe_mode') != 'live' ) { ?> selected<?php } ?>>
							<?php _e( 'Test Mode', 'freezy-stripe' ); ?>
						</option>
					</select>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="freezy_stripe_currency">
						<?php _e( 'Currency', 'freezy-stripe' ); ?>
					</label>
				</th>
				<td><?php echo $currency . ' - ' . $currencies[ $currency ]; ?></td>
				<td>
					<select id="freezy_stripe_currency" name="freezy_stripe_currency">
						<?php foreach ( $currencies as $key => $val ) { ?>
							<option value="<?php echo $key; ?>"<?php if ( $key == $currency ) { ?> selected<?php } ?>>
								<?php echo $key; ?>
								-
								<?php echo $val; ?>
							</option>
						<?php } ?>
					</select>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="freezy_stripe_suppress_https_warning">
						<?php _e( 'Suppress HTTPS Warning', 'freezy-stripe' ); ?>
						<span style="color:red">***</span>
					</label>

				</th>
				<td>
					<?php echo ( $suppress == 1 ) ? __( 'Yes - Not Recommended', 'freezy-stripe' ) : __( 'No', 'freezy-stripe' ); ?>
				</td>
				<td>
					<select id="freezy_stripe_suppress_https_warning" name="freezy_stripe_suppress_https_warning">
						<option value="0"<?php if ($suppress == 0) { ?> selected<?php } ?>>
							<?php _e( 'No - Recommended', 'freezy-stripe' ); ?>
						</option>
						<option value="1"<?php if ($suppress == 1) { ?> selected<?php } ?>>
							<?php _e( 'Yes - Not Recommended', 'freezy-stripe' ); ?>
						</option>
					</select>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<span style="color:red">***</span>
					<?php

					_e( 'By default, a warning message will show up if your website is not secure. If you choose to turn this off and use this plugin on a non-secure website, please be aware that your transactions are not 100% safe. ' , 'freezy-stripe');
					_e( 'There are several options for securing your website. The best option is to talk to your hosting company about getting an SSL certificate. Another alternative is to use the free SSL service available at ', 'freezy-stripe' );

					?>
					<a href="http://cloudflare.com" target="_blank">cloudflare.com</a>.
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="freezy_stripe_company_name">
						<?php _e( 'Company Name', 'freezy-stripe' ); ?>
					</label>
				</th>
				<?php $title = ( strlen( get_option( 'freezy_stripe_company_name', '' ) ) > 0 ) ? get_option( 'freezy_stripe_company_name' ) : get_option( 'blogname' ); ?>
				<td><?php echo $title; ?></td>
				<td><input type="text" id="freezy_stripe_company_name" name="freezy_stripe_company_name" value="<?php echo esc_attr( $title ); ?>" /></td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="freezy_stripe_company_logo">
						<?php _e( 'Logo', 'freezy-stripe' ); ?>
					</label>
				</th>
				<td>
					<div id="freezy-stripe-company-logo">
						<?php if ( get_option( 'freezy_stripe_company_logo', '' ) != '' ) { ?>
							<img src="<?php echo get_option( 'freezy_stripe_company_logo' ); ?>">
						<?php } else { ?>
							<?php _e( 'NOT SET', 'freezy-stripe' ); ?>
						<?php } ?>
					</div>
				</td>
				<td>
					<input type="hidden" name="freezy_stripe_company_logo" id="freezy_stripe_company_logo" value="<?php echo esc_attr( get_option( 'freezy_stripe_company_logo' ) ); ?>">
					<input id="freezy-stripe-upload-logo" class="button-primary" value="<?php _e( 'Add Logo', 'freezy-stripe' ); ?>" type="button">
					<input id="freezy-stripe-remove-logo" class="button-secondary" value="<?php _e( 'Remove Logo', 'freezy-stripe' ); ?>" type="button" <?php if ( get_option( 'freezy_stripe_company_logo', '' ) == '' ) { ?> style="display:none"<?php } ?>>
				</td>
			</tr>
		</table>

		<?php submit_button(); ?>

	</form>

	<a name="shortcode"></a>
	<h1>Freezy Stripe <?php _e('Shortcode Instructions', 'freezy-stripe'); ?></h1>
	<p>
		<strong>
			<?php _e( 'Add this shortcode to your page to accept a payment for a product or service:', 'freezy-stripe' ); ?>
		</strong>
	</p>


	[freezy_stripe name="<?php _e( 'My Product', 'freezy-stripe' ); ?>" price="29.99"]

	<p>
		<strong>
			<?php _e( 'You can add multiple prices so people can select which price they want (great for donations):', 'freezy-stripe' ); ?>
		</strong>
	</p>

	[freezy_stripe name="<?php _e( 'My Donation', 'freezy-stripe' ); ?>" price="5,10,15,20,25"]

</div>