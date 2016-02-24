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
	public function register_settings()
	{
		register_setting( 'freezy_stripe_settings', 'freezy_stripe_setting_name' );
	}
}