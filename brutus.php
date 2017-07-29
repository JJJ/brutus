<?php

/**
 * Plugin Name: Brutus
 * Plugin URI:  https://pluginsloaded.com/plugins/brutus/
 * Author:      John James Jacoby
 * Author URI:  https://jjj.blog
 * License:     GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Description: Your bouncer; your muscle; your protector & defender
 * Version:     1.2.0
 * Text Domain: brutus
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'wp_verify_nonce' ) ) :
/**
 * Verify that correct nonce was used with time limit.
 *
 * The user is given an amount of time to use the token, so therefore, since the
 * UID and $action remain the same, the independent variable is the time.
 *
 * @since 1.1.0 Brutus
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
 * @since 1.1.0 Brutus
 *
 * @param string|int $action Scalar value to add context to the nonce.
 * @return string The token.
 */
function wp_create_nonce( $action = -1 ) {
	return Brutus::pluggable_create_nonce( $action );
}
endif;

/**
 * Init wrapper
 *
 * @since 1.2.0 Brutus
 */
function _brutus() {

	// Includes
	require_once dirname( __FILE__ ) . '/classes/brutus.php';
	require_once dirname( __FILE__ ) . '/classes/cookie.php';

	// That no-good sailor's got me girl!
	new Brutus();
}
add_action( 'init',       array( $this, '_brutus'  ) );
add_action( 'login_init', array( $this, '_brutus'  ) );