<?php

namespace LBF\Auth;

use LBF\Tools\LDAP\LDAPHandler;

/**
 * Perform the various tasks required for logging a user into an app.
 * 
 * use LBF\Auth\LoginHandler;
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @since   LBF 0.1.6-beta
 */

class LoginHandler {

    /**
     * The LDAP handling object.
     * 
     * @var LDAPHandler $ldap
     * 
     * @readonly
     * @access  private
     * @since   LBF 0.1.6-beta
     */

    private readonly LDAPHandler $ldap;

    /**
     * Whether or not to check loggin through LDAP.
     * 
     * @var boolean $use_ldap   Default: false
     * 
     * @access  private
     * @since   LBF 0.1.6-beta
     */

    private bool $use_ldap = false;

    /**
     * Whether or not the user account is a LDAP account.
     * 
     * @var boolean $user_is_ldap
     * 
     * @readonly
     * @access  private
     * @since   LBF 0.1.6-beta
     */

    private readonly bool $user_is_ldap;

    /**
     * The status code of the login operation
     * 
     * @var integer $status_code
     * 
     * @access  private
     * @since   LBF 0.1.6-beta
     */

    private int $status_code = 0;

    /**
     * The generate password fragment.
     * 
     * @var string  $password_fragment
     * 
     * @readonly
     * @access  private
     * @since   LBF 0.1.6-beta
     */

    private readonly string $password_fragment;


    /**
     * Class constructor.
     * 
     * @param   object  $account                The user account object.
     * @param   string  $password               The password to be verified.
     * @param   boolean $check_account_disabled Whether or not to check if the account is disabled.
     *                                          Default: true
     * @param   string  $cookie_hash            The cookie hash used by the app. in LRS this is `COOKIE_HASH`.
     *                                          Default: ''
     * 
     * @access  public
     * @since   LBF 0.1.6-beta
     */

    public function __construct(
        
        /**
         * The user account object.
         * 
         * @var object  $account
         * 
         * @access  private
         * @since   LBF 0.1.6-beta
         */

        private object $account,
        
        /**
         * The password to be verified.
         * 
         * @var string  $password
         * 
         * @readonly
         * @access  private
         * @since   LBF 0.1.6-beta
         */

        private readonly string $password,
        
        /**
         * Whether or not to check if the account is disabled.
         * 
         * @var boolean $check_account_disabled     Default: true
         * 
         * @readonly
         * @access  private
         * @since   LBF 0.1.6-beta
         */

        private readonly bool $check_account_disabled = true,
        
        /**
         * The cookie hash used by the app. in LRS this is `COOKIE_HASH`.
         * 
         * @var string  $cookie_hash    Default: ''
         * 
         * @readonly
         * @access  private
         * @since   LBF 0.1.6-beta
         */

        private readonly string $cookie_hash = '',
    ) {
        $this->user_is_ldap = ( isset( $account->ldap_user ) && $account->ldap_user == 1 );
        if ( isset( $this->account->ldap_password_fragment ) ) {
            $this->password_fragment = $this->account->ldap_password_fragment;
        } else {
            $this->password_fragment = $this->password_substr( $this->password );
        }
    }


    /**
     * Perform the login task.
     * 
     * @return  boolean
     * 
     * @access  public
     * @since   0.1.6-beta
     */

    public function perform_login(): bool {
        if ( $this->use_ldap && $this->user_is_ldap ) {
            $verified = $this->ldap_login( $this->password );
        } else {
            $verified = $this->standard_login( $this->password );
        }

        $this->status_code = $verified ? 200 : 401;

        if ( $this->check_account_disabled ) {
            /**
             * Check if Account is disabled
             * 
             * $this->set_status_code( 403 );
             */
        }
        return $verified;
    }


    /**
     * Return hte status code.
     * 
     * @return  int The status code.
     * 
     * @access  public
     * @since   LBF 0.1.6-beta
     */

    public function get_status_code(): int {
        return $this->status_code;
    }


    /**
     * Set the status code.
     * 
     * @param   int $code   The code to set.
     * 
     * @access  public
     * @since   LBF 0.1.6-beta
     */

    public function set_status_code( int $code ): void {
        $this->status_code = $code;
    }


    /**
     * Generate the session ID key.
     * 
     * @return  string  A string which represents the session ID in the form of $username | $hash
     * 
     * @access  public
     * @since   LRS 3.1.0
     * @since   LBF 0.1.6-beta
     */

    public function generate_session_id(): string {
        return $this->account->username . '|' . hash( 'sha256', $this->account->username . $this->password_fragment . $this->cookie_hash );
    }


    /**
     * Enable and set up the checking of ldap logging.
     * 
     * @param   boolean             $set_ldap   Whether or not to enable ldap logging.
     * @param   LDAPHandler|null    $ldap       The LDAP object used for LDAP verification.
     * 
     * @return  bool
     * 
     * @access  public
     * @since   LBF 0.1.6-beta
     */
    public function set_ldap( bool $set_ldap, ?LDAPHandler $ldap = null ): bool {
        $this->use_ldap = $set_ldap;
        if ( $set_ldap ) {
            if ( !is_null( $ldap ) ) {
                $this->ldap = $ldap;
                return true;
            } else {
                return false;
            }
        }
        return true;
    }


    /**
     * Grab a substring of the user's already hashed password.
     * 
     * @param   string  $password   The user's already hashed password.
     * 
     * @return  string  A 4 character substring of the user's password.
     * 
     * @access  public
     * @since   LRS 3.1.0
     * @since   LBF 0.1.6-beta
     */

    public function password_substr( string $password ): string {
        return substr( $password, 8, 4 );;
    }


    /**
     * Perform an LDAP password verification task.
     * 
     * @param   string  $password   The password to verify.
     * 
     * @return  boolean
     * 
     * @access  private
     * @since   LBF 0.1.6-beta
     */

    private function ldap_login( string $password ): bool {
        if ( $this->ldap->ldap_login() && $password !== '' ) {
            return true;
        }
        return false;
    }


    /**
     * Perform a standard password verification task.
     * 
     * @param   string  $password   The password to verify.
     * 
     * @return  boolean
     * 
     * @access  private
     * @since   LBF 0.1.6-beta
     */

    private function standard_login( string $password ): bool {
        return password_verify( $password, $this->account->password );
    }

}