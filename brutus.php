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
