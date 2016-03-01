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
		add_thickbox();
		wp_enqueue_script( 'freezy-stripe-stripe-js', 'https://checkout.stripe.com/checkout.js', array( 'jquery' ), (WP_DEBUG) ? time() : self::VERSION_JS, FALSE );
		wp_enqueue_script( 'freezy-stripe-js', plugin_dir_url( dirname( __DIR__ )  ) . 'js/freezy-stripe.js', array( 'jquery' ), (WP_DEBUG) ? time() : self::VERSION_JS, TRUE );
		wp_enqueue_style( 'freezy-stripe-css', plugin_dir_url( dirname( __DIR__ ) ) . 'css/freezy-stripe.css', array(), (WP_DEBUG) ? time() : self::VERSION_CSS );
	}

	/**
	 * @param $attributes
	 *
	 * @return string
	 */
	public function short_code( $attributes )
	{
		$this->attributes = shortcode_atts( array(
			'name' => '',
			'price' => ''
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

	/**
	 * @return array
	 */
	public static function getStripeKeys()
	{
		$mode = ( get_option('freezy_stripe_mode') == 'live' ) ? 'live' : 'test';

		return array(
			'secret' => get_option( 'freezy_stripe_'.$mode.'_secret_key' ),
			'pub' => get_option( 'freezy_stripe_'.$mode.'_pub_key' )
		);
	}

	/**
	 *
	 */
	public function form_capture()
	{
		if ( isset( $_POST['freezy_stripe_action'] ) )
		{
			if ( wp_verify_nonce( $_POST['_wpnonce'], 'freezy-stripe-nonce' ) )
			{
				if ( $_POST['freezy_stripe_action'] == 'charge' )
				{
					$stripe_keys = self::getStripeKeys();
					Stripe\Stripe::setApiKey( $stripe_keys['secret'] );

					try
					{
						$token = Stripe\Token::retrieve( $_POST['token'] );
						$email =  $token->email;
						$address = $token->card->address_line1;
						$city = $token->card->address_city;
						$state =  $token->card->address_state;
						$zip =  $token->card->address_zip;
						$name = $token->card->name;

						/** @var \Stripe\Charge $charge */
						Stripe\Charge::create( array(
							'amount' => round( $_POST['price'] ),
							'currency' => 'usd',
							'source' => $_POST['token'],
							'description' => $_POST['description']
						) );

						$post_id = wp_insert_post(
							array(
								'post_title' => $name,
								'post_status' => 'publish',
								'post_type' => 'freezy_payment'
							)
						);

						update_post_meta( $post_id, 'payment_for', $_POST['description'] );
						update_post_meta( $post_id, 'price', $_POST['price']/100 );
						update_post_meta( $post_id, 'email', $email );
						update_post_meta( $post_id, 'address', $address );
						update_post_meta( $post_id, 'city', $city );
						update_post_meta( $post_id, 'state', $state );
						update_post_meta( $post_id, 'zip', $zip );

						$referrer = $_POST['_wp_http_referer'];
						$parts = explode( '?', $referrer );
						$page = $parts[0];
						if ( count( $parts ) > 1 )
						{
							unset( $parts[0] );
							$qs = $parts;
						}
						else
						{
							$qs = array();
						}
						if ( ! in_array( 'freezy=success', $qs ) )
						{
							$qs[] = 'freezy=success';
						}

						header( 'Location:' . $page . '?' . implode( '&', $qs ) );
						exit;
					}
					catch ( \Exception $e )
					{
						// card was declined
					}
				}
			}
		}
	}

	/**
	 *
	 */
	public function create_post_type()
	{
		$title = __( 'Freezy Payment', 'freezy-stripe' );
		$plural = __( 'Freezy Payments', 'freezy-stripe' );

		$labels = array (
			'name' => $plural,
			'singular_name' => $plural,
			'add_new_item' => __( 'Add New', 'freezy-stripe' ) . ' ' . $title,
			'edit_item' => __( 'Edit', 'freezy-stripe' ) . ' ' . $title,
			'new_item' => __( 'New', 'freezy-stripe' ) . ' ' . $title,
			'view_item' => __( 'View', 'freezy-stripe' ) . ' ' . $title,
			'search_items' => __( 'Search', 'freezy-stripe' ) . ' ' . $plural,
			'not_found' => __( 'No', 'freezy-stripe' ) . ' ' . $plural . ' ' . __( 'Found', 'freezy-stripe'  )
		);

		$args = array (
			'labels' => $labels,
			'hierarchical' => FALSE,
			'description' => $plural,
			'supports' => array( 'title' ),
			'show_ui' => TRUE,
			'show_in_menu' => 'freezy_stripe',
			'show_in_nav_menus' => TRUE,
			'publicly_queryable' => TRUE,
			'exclude_from_search' => FALSE,
			'has_archive' => TRUE
		);

		register_post_type( 'freezy_payment' , $args );
	}

	/**
	 *
	 */
	public function admin_menus()
	{
		add_menu_page( 'Freezy Stripe', 'Freezy Stripe', 'manage_options', 'freezy_stripe', array( $this, 'print_instructions_page' ), 'dashicons-cart' );
		add_submenu_page( 'freezy_stripe', __( 'Settings', 'freezy-stripe' ), __( 'Settings', 'freezy-stripe' ), 'manage_options', 'freezy_stripe' );
	}

	/**
	 * @param $input
	 *
	 * @return string|void
	 */
	public function custom_enter_title( $input )
	{
		global $post_type;

		if ( $post_type == 'freezy_payment' )
		{
			return __( 'Enter Customer Name', 'freezy-stripe' );
		}

		return $input;
	}

	/**
	 *
	 */
	public function custom_meta()
	{
		add_meta_box( 'freezy-payment-meta', __( 'Additional Info', 'freezy-stripe' ), array( $this, 'meta_box' ), 'freezy_payment' );
	}

	/**
	 *
	 */
	public function meta_box()
	{
		include ( dirname( dirname( __DIR__ ) ) . '/includes/meta.php' );
	}

	/**
	 * @param $post_id
	 * @param $post
	 */
	public function save_meta( $post_id, $post )
	{
		if ( $post->post_type == 'freezy_payment' )
		{
			$payment_for = ( isset( $_REQUEST['freezy_payment_for'] ) ) ? trim( $_REQUEST['freezy_payment_for'] ) : '';
			$price = ( isset( $_REQUEST['freezy_price'] ) ) ? preg_replace( '/[^0-9\.]/', '', $_REQUEST['freezy_price'] ) : 0;
			if ( strlen( $price ) == 0 )
			{
				$price = 0;
			}
			$email = ( isset( $_REQUEST['freezy_email'] ) ) ? trim( $_REQUEST['freezy_email'] ) : '';
			$address = ( isset( $_REQUEST['freezy_address'] ) ) ? trim( $_REQUEST['freezy_address'] ) : '';
			$city = ( isset( $_REQUEST['freezy_city'] ) ) ? trim( $_REQUEST['freezy_city'] ) : '';
			$state = ( isset( $_REQUEST['freezy_state'] ) ) ? trim( $_REQUEST['freezy_state'] ) : '';
			$zip = ( isset( $_REQUEST['freezy_zip'] ) ) ? trim( $_REQUEST['freezy_zip'] ) : '';

			update_post_meta( $post_id, 'payment_for', $payment_for );
			update_post_meta( $post_id, 'price', $price );
			update_post_meta( $post_id, 'email', $email );
			update_post_meta( $post_id, 'address', $address );
			update_post_meta( $post_id, 'city', $city );
			update_post_meta( $post_id, 'state', $state );
			update_post_meta( $post_id, 'zip', $zip );
		}
	}

	/**
	 * @param $columns
	 *
	 * @return array
	 */
	public function add_columns( $columns )
	{
		$new = array(
			'payment_for' => __( 'Product or Service', 'freezy-stripe' ),
			'price' => __( 'Price', 'freezy-stripe' )
		);
		$columns = array_slice( $columns, 0, 2, TRUE ) + $new + array_slice( $columns, 2, NULL, TRUE );
		$columns['title'] = __( 'Customer', 'freezy-stripe' );


		return $columns;
	}

	/**
	 * @param $column
	 */
	public function custom_columns( $column )
	{
		$post = $GLOBALS['post'];
		$custom = get_post_custom( $post->ID );
		$payment_for = ( array_key_exists( 'payment_for', $custom ) ) ? $custom[ 'payment_for' ][0] : '';
		$price = ( array_key_exists( 'price', $custom ) ) ? $custom[ 'price' ][0] : 0;

		if ( $column == 'payment_for' )
		{
			echo $payment_for;
		}
		elseif ( $column == 'price' )
		{
			echo $price;
		}
	}
}