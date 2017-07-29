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

if ( ! function_exists( 'wp_set_current_user' ) ) :
/**
 * Changes the current user by ID or name.
 *
 * Set $id to null and specify a name if you do not know a user's ID.
 *
 * Some WordPress functionality is based on the current user and not based on
 * the signed in user. Therefore, it opens the ability to edit and perform
 * actions on users who aren't signed in.
 *
 * @since 1.2.0 Brutus
 * @global WP_User $current_user The current user object which holds the user data.
 *
 * @param int    $id   User ID
 * @param string $name User's username
 * @return WP_User Current user User object
 */
function wp_set_current_user( $id, $name = '' ) {
	global $current_user;

	// If `$id` matches the user who's already current, there's nothing to do.
	if ( isset( $current_user )
		&& ( $current_user instanceof WP_User )
		&& ( $id == $current_user->ID )
		&& ( null !== $id )
	) {
		return $current_user;
	}

	$current_user = new WP_User( $id, $name );

	setup_userdata( $current_user->ID );

	do_action( 'set_current_user' );

	return $current_user;
}
endif;

if ( ! function_exists( 'is_user_logged_in' ) ) :
/**
 * Checks if the current visitor is a logged in user.
 *
 * @since 1.2.0 Brutus
 *
 * @return bool True if user is logged in, false if not logged in.
 */
function is_user_logged_in() {
	$user = wp_get_current_user();

	return $user->exists();
}
endif;

if ( ! function_exists( 'wp_get_current_user' ) ) :
/**
 * Retrieve the current user object.
 *
 * Will set the current user, if the current user is not set. The current user
 * will be set to the logged-in person. If no user is logged-in, then it will
 * set the current user to 0, which is invalid and won't have any permissions.
 *
 * @since 1.2.0 Brutus
 *
 * @see _wp_get_current_user()
 * @global WP_User $current_user Checks if the current user is set.
 *
 * @return WP_User Current WP_User instance.
 */
function wp_get_current_user() {
	return _wp_get_current_user();
}
endif;

// Includes
require_once dirname( __FILE__ ) . '/classes/brutus.php';
require_once dirname( __FILE__ ) . '/classes/cookie.php';

// That no-good sailor's got me girl!
new Brutus();
