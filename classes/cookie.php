<?php

/**
 * The Brutus Cookie Class
 *
 * Brutus is your bouncer; your muscle; your protector & defender
 *
 * @package Brutus
 * @subpackage Classes
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Uses a cookie to store the anonymous user ID
 *
 * @since Brutus (1.0.0)
 */
final class Brutus_Cookie {

    /**
     * @var string The name of our cookie
     */
    protected $cookie_name;

    /**
     * @var int When the cookie expires
     */
    protected $expires;

    /**
     * @var boolean Whether or not to set an SSL only cookie
     */
    protected $secure;

    /**
     * @var string Nonce salt we'll use to "sign" our IDs
     */
    protected $salt;

    /**
     * @var PasswordHash Container for an instance of PasswordHash
     */
    protected $hasher = null;

    /**
     * Constructor
     *
     * @since   Brutus (1.0.0)
     *
     * @param   string  $cookie_name
     * @param   int     $expires
     * @param   boolean $secure
     * @param   string  $salt
     */
    public function __construct( $cookie_name = '', $expires = '', $secure = false, $salt = '' ) {
        $this->cookie_name = $cookie_name;
        $this->expires     = $expires;
        $this->secure      = $secure;
        $this->salt        = $salt;
    }

    /**
	 * Maybe set a cookie with a unique user ID to use for nonces
	 *
     * @since Brutus (1.0.0)
     */
    public function init() {
        list( $uid, $expires, $sig ) = $this->cookie_id();

        // if we already have an ID set up, don't bother
        if ( ! empty( $uid ) && $this->valid_expiration( $expires ) && $this->valid_signature( $uid, $sig ) ) {
            return;
        }

        $user_id = md5( $this->get_hasher()->get_random_bytes( 64 ) );

        $this->set_cookie( $user_id );
    }

    /**
	 * Get the unique user ID
	 *
	 * Used by `nonce_user_logged_out` filter
	 *
     * @since Brutus (1.0.0)
     */
    public function get_user_id() {
        list( $uid, $expires, $sig ) = $this->cookie_id();
        return $uid;
    }

	/**
	 * Kill the nonce cookie when user logs in
	 *
	 * @since  Brutus (1.0.0)
	 *
	 * @return void
	 */
	public function kill_cookie() {

		// Kill the global cookie
		unset( $_COOKIE[ $this->cookie_name ] );

		// Expire physical cookie
		return setcookie(
			$this->cookie_name,
			' ',
			time() - YEAR_IN_SECONDS,
			COOKIEPATH,
			COOKIE_DOMAIN,
			$this->secure
		);
	}


	/** Protected Methods *****************************************************/

    /**
     * Get the user ID from the cookie.
     *
     * @since   Brutus (1.0.0)
     *
     * @return  array [$uid, $expires] or [null, null] on failure
     */
    protected function cookie_id() {

		// Bail with empty array
        if ( ! isset( $_COOKIE[ $this->cookie_name ] ) ) {
            return array( null, null, null );
        }

        $cookie = $_COOKIE[ $this->cookie_name ];

		// Bail with empty array
        if ( 2 !== substr_count( $cookie, '|' ) ) {
            return array( null, null, null );
        }

        return explode( '|', $cookie );
    }

    /**
     * Set the nonce cookie.
     *
     * @since   Brutus (1.0.0)
     *
     * @param   string $uid The user ID to set
     * @return  void
     */
    protected function set_cookie( $uid ) {

		// Setup cookie values
        $expires = time() + $this->expires;
        $value   = $uid . '|' . $expires . '|' . $this->sign( $uid );

        // Put the user ID into the $_COOKIE superglobal
        $_COOKIE[ $this->cookie_name ] = $value;

		// Drop a delicious cookie
        return setcookie(
            $this->cookie_name,
            $value,
            $expires,
            COOKIEPATH,
            COOKIE_DOMAIN,
            $this->secure
        );
    }

    /**
     * Get the hasher, used so we can generate a nice set of random bytes
     *
     * @since   Brutus (1.0.0)
	 *
     * @return  PasswordHash
     */
    protected function get_hasher() {

		// Return hasher if already set
        if ( ! is_null( $this->hasher ) ) {
            return $this->hasher;
        }

		// Require phpass
        require_once ABSPATH . 'wp-includes/class-phpass.php';

		// Setup the hasher
        $this->hasher = new PasswordHash( 8, true );

        return $this->hasher;
    }

    /**
     * Check to see if an expires time falls without our allowed time limits.
     *
     * @since   Brutus (1.0.0)
	 *
     * @param   int $expires The unix timestamp to check
     * @return  boolean
     */
    protected function valid_expiration( $expires ) {
        $diff = intval( $expires ) - time();

        // If we've passed our day threshold return false
        if ( $diff <= DAY_IN_SECONDS ) {
            return false;
        }

        return true;
    }

    /**
     * Check a signature and see if it's valid.
     *
     * @since   Brutus (1.0.0)
	 *
     * @param   string $uid
     * @param   string $sig
     * @return  boolean
     */
    protected function valid_signature( $uid, $sig ) {
        return 0 === strcmp( $sig, $this->sign( $uid ) );
    }

    /**
     * Sign $uid with our salt.
     *
     * @since   Brutus (1.0.0)
	 *
     * @param   string $uid
     * @return  string
     */
    protected function sign( $uid ) {
        return hash_hmac( 'sha1', $uid, $this->salt );
    }
}
