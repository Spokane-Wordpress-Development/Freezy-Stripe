<?php

/**
 * Plugin Name: Freezy Stripe
 * Plugin URI:
 * Description: A free and easy (get it?) way to take Stripe payments on your website!
 * Author: Spokane WordPress Development
 * Author URI: http://www.spokanewp.com
 * Version: 1.0.0
 * Text Domain: freezy-stripe
 *
 * Copyright 2016 Spokane WordPress Development
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 */

require_once ( 'classes/FreezyStripe/Controller.php' );
require_once ( 'classes/Stripe/init.php' );

/* controller object */
$controller = new \FreezyStripe\Controller;

/* activate */
register_activation_hook( __FILE__, array( $controller, 'activate' ) );

/* enqueue js and css */
add_action( 'init', array( $controller, 'init' ) );

/* Create custom post type */
add_action( 'init', array( $controller, 'create_post_type' ) );

/* capture form post */
add_action ( 'init', array( $controller, 'form_capture' ) );

/* register shortcode */
add_shortcode ( 'freezy_stripe', array( $controller, 'short_code' ) );

/* admin stuff */
if (is_admin() )
{
	/* Add main menu and sub-menus */
	add_action( 'admin_menu', array( $controller, 'admin_menus') );

	/* register settings */
	add_action( 'admin_init', array( $controller, 'register_settings' ) );

	/* admin scripts */
	add_action( 'admin_init', array( $controller, 'admin_scripts' ) );

	/* custom title label */
	add_filter( 'enter_title_here', array( $controller, 'custom_enter_title' ) );

	/* create custom attributes for post type */
	add_action( 'add_meta_boxes_freezy_payment', array( $controller, 'custom_meta' ) );

	/* custom columns */
	add_filter( 'manage_freezy_payment_posts_columns', array( $controller, 'add_columns' ) );
	add_action( 'manage_freezy_payment_posts_custom_column' , array( $controller, 'custom_columns' ) );

	/* Save meta */
	add_action( 'save_post', array( $controller, 'save_meta' ), 10, 2 );

	/* add the instructions page link */
	add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $controller, 'instructions_link' ) );

	/* add the instructions page */
	add_action( 'admin_menu', array( $controller, 'instructions_page' ) );
}