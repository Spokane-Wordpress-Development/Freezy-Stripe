<?php

namespace FreezyStripe;

class Controller {

	const VERSION = '1.0.0';
	const VERSION_CSS = '1.0.0';
	const VERSION_JS = '1.0.0';
	const OPTION_VERSION = 'freezy_stripe_version';

	public $attributes;

	/**
	 *
	 */
	public function activate()
	{
		add_option( self::OPTION_VERSION, self::VERSION );
	}

	/**
	 *
	 */
	public function init()
	{

	}

	/**
	 * @param $attributes
	 *
	 * @return string
	 */
	public function short_code( $attributes )
	{
		$this->attributes = shortcode_atts( array(
			'key' => 'default_val'
		), $attributes );

		ob_start();
		include( dirname( dirname( __DIR__ ) ) . '/includes/shortcode.php');
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}

	/**
	 * @param $attribute
	 *
	 * @return string
	 */
	public function get_attribute( $attribute )
	{
		if ( is_array( $this->attributes ) && array_key_exists( $attribute, $this->attributes ) )
		{
			return $this->attributes[ $attribute ];
		}

		return '';
	}

	/**
	 * @param array $links
	 *
	 * @return array
	 */
	public function instructions_link( $links )
	{
		$link = '<a href="options-general.php?page=' . plugin_basename( dirname( dirname( __DIR__ ) ) ) . '">' . __( 'Instructions', 'freezy-stripe' ) . '</a>';
		$links[] = $link;
		return $links;
	}

	/**
	 *
	 */
	public function instructions_page()
	{
		add_options_page(
			'Freezy Stripe ' . __( 'Instructions', 'freezy-stripe' ),
			'Freezy Stripe',
			'manage_options',
			plugin_basename( dirname( dirname( __DIR__ ) ) ),
			array( $this, 'print_instructions_page')
		);
	}

	/**
	 *
	 */
	public function print_instructions_page()
	{
		include( dirname( dirname( __DIR__ ) ) . '/includes/instructions.php' );
	}

	/**
	 *
	 */
	public function admin_scripts()
	{
		wp_enqueue_media();
		wp_enqueue_script( 'freezy-stripe-admin-js', plugin_dir_url( dirname( __DIR__ )  ) . 'js/admin.js', array( 'jquery' ), (WP_DEBUG) ? time() : self::VERSION_JS, TRUE );
		wp_enqueue_style( 'freezy-strip-admin-css', plugin_dir_url( dirname( __DIR__ ) ) . 'css/admin.css', array(), (WP_DEBUG) ? time() : self::VERSION_CSS );
	}

	/**
	 *
	 */
	public function register_settings()
	{
		register_setting( 'freezy_stripe_settings', 'freezy_stripe_test_secret_key' );
		register_setting( 'freezy_stripe_settings', 'freezy_stripe_test_pub_key' );
		register_setting( 'freezy_stripe_settings', 'freezy_stripe_live_secret_key' );
		register_setting( 'freezy_stripe_settings', 'freezy_stripe_live_pub_key' );
		register_setting( 'freezy_stripe_settings', 'freezy_stripe_mode' );
		register_setting( 'freezy_stripe_settings', 'freezy_stripe_currency' );
		register_setting( 'freezy_stripe_settings', 'freezy_stripe_company_name' );
		register_setting( 'freezy_stripe_settings', 'freezy_stripe_suppress_https_warning' );
		register_setting( 'freezy_stripe_settings', 'freezy_stripe_company_logo' );
	}

	/**
	 * @return array
	 */
	public function get_currencies()
	{
		return array(
			'AUD' => 'Australian Dollar',
			'CAD' => 'Canadian Dollar',
			'DKK' => 'Danish Krone',
			'EUR' => 'Euro',
			'GBP' => 'British Pound',
			'NOK' => 'Norwegian Krone',
			'SEK' => 'Swedish Krona',
			'USD' => 'United States Dollar'
		);
	}
}