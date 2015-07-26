<?php

/**
 * The Brutus Plugin
 *
 * Brutus is your bouncer; your muscle; your protector & defender
 *
 * $Id$
 *
 * @package Brutus
 * @subpackage Main
 */

/**
 * Plugin Name: Brutus
 * Plugin URI:  https://flox.io
 * Description: Brutus is your bouncer; your muscle; your protector & defender
 * Author:      John James Jacoby
 * Author URI:  https://flox.io
 * Version:     1.0.0
 * Text Domain: brutus
 * Domain Path: /languages/
 * License:     GPLv2 or later (license.txt)
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Initialize Brutus
 *
 * @since Brutus (1.0.0)
 */
function wp_brutus() {

	$path = dirname(__FILE__);

	// Includes
	include $path . '/classes/brutus.php';
	include $path . '/classes/cookie.php';

	// That no-good sailor's got me girl!
	new Brutus();
}
add_action( 'init', 'wp_brutus' );

if ( ! function_exists( 'wp_verify_nonce' ) ) :
/**
 * Verify that correct nonce was used with time limit.
 *
 * The user is given an amount of time to use the token, so therefore, since the
 * UID and $action remain the same, the independent variable is the time.
 *
 * @since Brutus (1.1.0)
 *
 * @param string     $nonce  Nonce that was used in the form to verify
 * @param string|int $action Should give context to what is taking place and be the same when nonce was created.
 * @return false|int False if the nonce is invalid, 1 if the nonce is valid and generated between
 *                   0-12 hours ago, 2 if the nonce is valid and generated between 12-24 hours ago.
 */
function wp_verify_nonce( $nonce = '', $action = -1 ) {
	return Brutus::pluggable_verify_nonce( $nonce, $action );
}
endif;

if ( ! function_exists( 'wp_create_nonce' ) ) :
/**
 * Creates a cryptographic token tied to a specific action, user, and window of time.
 *
 * @since Brutus (1.1.0)
 *
 * @param string|int $action Scalar value to add context to the nonce.
 * @return string The token.
 */
function wp_create_nonce( $action = -1 ) {
	return Brutus::pluggable_create_nonce( $action );
}
endif;
