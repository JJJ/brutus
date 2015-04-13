<?php

/**
 * The Brutus Class
 *
 * Brutus is your bouncer; your muscle; your protector & defender
 *
 * @package Brutus
 * @subpackage Classes
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Brutus is a real classy bouncer
 *
 * @since Brutus (1.0.0)
 */
final class Brutus {

	/** Nonce Vars ************************************************************/

	/**
	 * @var string Name of Nonce
	 */
	private $nonce_name = 'brutus';

	/**
	 * @var string Nonce Action
	 */
	private $nonce_action = 'the_bouncer';

	/**
	 * @var Brutus_Cookie The
	 */
	private $cookie = null;

	/**
	 * @var array Possible `wp-login.php` form paths
	 */
	private $form_paths = array(
		'wp-login.php',
		'wp-login.php?action=register',
		'wp-login.php?action=lostpassword'
	);

	/**
	 * @var array Possible `site_url`s to allow `wp_redirect`ed access to
	 */
	private $allowed_redirect_tos = array(
		'wp-login.php?loggedout=true',
		'wp-login.php?action=rp',
		'wp-login.php?registration=disabled',
		'wp-login.php?checkemail=confirm',
		'wp-login.php?checkemail=registered',
		'wp-login.php?action=lostpassword&error=expiredkey',
		'wp-login.php?action=lostpassword&error=invalidkey'
	);

	/** Constructor ***********************************************************/

	/**
	 *
	 * @since Brutus (1.0.0)
	 */
	public function __construct() {

		// No priv actions & filters
		add_action( 'login_init',  array( $this, 'login_init'  ) );
		add_filter( 'wp_redirect', array( $this, 'wp_redirect' ) );
		add_action( 'wp_login',    array( $this, 'wp_login'    ) );
		add_action( 'wp_logout',   array( $this, 'wp_logout'   ) );

		// Logged-in user support
		if ( is_user_logged_in() ) {
			add_filter( 'logout_url', array( $this, 'logout_url' ) );
			return;
		}

		// Initialize the cookie
		$this->cookie_init();

		// Filters
		add_filter( 'login_url',             array( $this, 'login_url'          ) );
		add_filter( 'lostpassword_url',      array( $this, 'lostpassword_url'   ) );
		add_filter( 'register_url',          array( $this, 'register_url'       ) );
		add_filter( 'site_url',              array( $this, 'site_url'           ), 10, 3 );
		add_filter( 'nonce_user_logged_out', array( $this, 'logged_out_user_id' ) );
	}

	/** Actions ***************************************************************/

	/**
	 * Verify Brutus nonce on `login_init` action
	 *
	 * @since Brutus (1.0.0)
	 */
	public function login_init() {
		self::verify_nonce();
	}

	/**
	 * Initialize the logged-out-user cookie
	 *
	 * @since Brutus (1.0.0)
	 */
	public function cookie_init() {

		// Setup the cookie object
		$this->cookie = new Brutus_Cookie(
			'_brutus_user_id',
			DAY_IN_SECONDS * 2,
			is_ssl(),
			NONCE_SALT
	    );

		// Initialize the cookie
		$this->cookie->init();
	}

	/** Filters ***************************************************************/

	/**
	 * Filter `login_url` and protect it with Brutus nonce
	 *
	 * @since Brutus (1.0.0)
	 *
	 * @param  string $login_url
	 * @return string Nonced URL for login
	 */
	public function login_url( $login_url = '' ) {
		return self::add_nonce_to_url( $login_url );
	}

	/**
	 * Filter `logout_url` and protect it with Brutus nonce
	 *
	 * @since Brutus (1.0.0)
	 *
	 * @param  string $logout_url
	 * @return string Nonced URL for logout
	 */
	public function logout_url( $logout_url = '' ) {
		return self::add_nonce_to_url( $logout_url );
	}

	/**
	 * Filter `lostpassword_url` and protect it with Brutus nonce
	 *
	 * @since Brutus (1.0.0)
	 *
	 * @param  string $lostpassword_url
	 * @return string Nonced URL for lost password
	 */
	public function lostpassword_url( $lostpassword_url = '' ) {
		return self::add_nonce_to_url( $lostpassword_url );
	}

	/**
	 * Filter `register_url` and protect it with Brutus nonce
	 *
	 * @since Brutus (1.0.0)
	 *
	 * @param  string $register_url
	 * @return string Nonced URL for registration
	 */
	public function register_url( $register_url = '' ) {
		return self::add_nonce_to_url( $register_url );
	}

	/**
	 * Filter `nonce_user_logged_out` and return an ID based on cookie value
	 *
	 * @since Brutus (1.0.0)
	 *
	 * @return integer
	 */
	public function logged_out_user_id() {
		return (int) $this->cookie->get_user_id();
	}

	/**
	 * Filter `site_url` and maybe add nonce to it
	 *
	 * @since Brutus (1.0.0)
	 *
	 * @param  string $url
	 * @param  string $path
	 * @param  string $scheme
	 *
	 * @return string Nonced URL for `wp-login.php` form
	 */
	public function site_url( $url = '', $path = '', $scheme = null ) {

		// Check $scheme & $path for login form paths
		if ( ( 'login_post' === $scheme ) && ( in_array( $path, $this->form_paths, true ) ) ) {
			$url = self::add_nonce_to_url( $url );
		}

		// Always return the URL
		return $url;
	}

	/**
	 * Filter `wp_redirect` and maybe add nonce to URL
	 *
	 * @since Brutus (1.0.0)
	 *
	 * @param  string $url
	 * @return string Nonced URL to redirect to
	 */
	public function wp_redirect( $url = '' ) {

		// Check $scheme & $path for login form paths
		if ( false !== array_search( $url, $this->allowed_redirect_tos, true ) ) {
			$url = self::add_nonce_to_url( $url );
		}

		// Always return the URL
		return $url;
	}

	/**
	 * Kill the nonce cookie when a user successfully logs in
	 *
	 * @since Brutus (1.0.0)
	 */
	public function wp_login() {
		return $this->cookie->kill_cookie();
	}

	/**
	 * Quick-init cookies immediately after logout
	 *
	 * @since Brutus (1.0.0)
	 */
	public function wp_logout() {
		$this->cookie_init();
	}

	/** Protected Helpers *****************************************************/

	/**
	 * Add Brutus's bouncer nonce to any URL
	 *
	 * Note: This is an unescaped version of `wp_nonce_url()`
	 *
	 * @since Brutus (1.0.0)
	 *
	 * @param  string $url
	 * @return string A URL with the bouncer nonce
	 */
	protected function add_nonce_to_url( $url = '' ) {
		$action_url = str_replace( '&amp;', '&', $url );
		return add_query_arg( $this->nonce_name, wp_create_nonce( $this->nonce_action ), $action_url );
	}

	/**
	 * Verify nonce
	 *
	 * @since Brutus (1.0.0)
	 */
	protected function verify_nonce() {

		// Redirect if no nonce
		if ( empty( $_REQUEST[ $this->nonce_name ] ) ) {
			self::redirect();
		}

		// Get the nonce
		$nonce = $_REQUEST[ $this->nonce_name ];

		// Redirect if nonce check fails
		if ( ! wp_verify_nonce( $nonce, $this->nonce_action ) ) {
			self::redirect();
		}
	}

	/**
	 * Do the safe redirect to the root of the site
	 *
	 * @since Brutus (1.0.0)
	 */
	protected function redirect() {
		wp_safe_redirect( network_home_url() );
		die;
	}
}
