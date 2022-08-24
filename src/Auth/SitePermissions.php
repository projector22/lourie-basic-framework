<?php

namespace Framework\Auth;

use App\Auth\Login;
use App\Auth\Cookies;
use App\Auth\Sessions;
use App\Structure\Footer;
use App\Db\Data\UserAccountsData;
use Framework\Tools\JSON\JSONTools;

/**
 * This class gets the $_SESSION permissions variable and sets a public function for each permission level.
 * 
 * It then sets each as a boolean variable which can be pulled from outside class to test site permissions.
 * 
 * The purpose of this class is to get and return a boolean variable for each permission level.
 * 
 * It first resets the variables to false then tests for $SESSION['permissions']
 * 
 * Functions only for the logged in user
 * 
 * use Framework\Auth\SitePermissions;
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @since   3.0.0
 */

class SitePermissions {

    /**
     * If user is logged in or not
     * 
     * @var boolean     $logged_in      Default: false
     * 
     * @access  public
     * @since   3.7.3
     */

    public bool $logged_in = false;

    /**
     * If access is via an API key
     * 
     * @var boolean     $valid_api_key    Default: false
     * 
     * @access  public
     * @since   3.16.1
     */

    public bool $valid_api_key = false;

    /**
     * The API key sent to the app.
     * 
     * @var string|null $api_key    Default: null
     * 
     * @access  private
     * @since   3.16.1
     */
    
    private ?string $api_key;


    /**
     * Constructor method, things to do when the class is loaded
     * 
     * @access  public
     * @since   3.1.0
     */

    public function __construct() {
        if ( Session::value_exists( Sessions::SESSION_LOGIN_VAR ) ) {
            $this->logged_in = true;
        }
        if ( !isset( $_COOKIE[Cookies::SESSION_ID] ) ) {
            $this->logged_in = false;
        }

        $this->api_key = Api::get_key();
    } //__construct


    /**
     * Checks if the login session variable is set and forces login if not
     * 
     * @access  public
     * @since   3.1.0
     */

    public function check_login(): void {
        if ( !$this->logged_in && !$this->valid_api_key ) {
            $login = new Login;
            $login->construct_page();
            Footer::draw();
            die;
        } else {
            if ( self::check_page_refresh_token() || $this->valid_api_key ) {
                $accounts = new UserAccountsData;
                if ( $this->valid_api_key && !$this->logged_in ) {
                    $accounts->select_one( "api_key='{$this->api_key}'" );
                } else {
                    $accounts->select_one( "account_name='{$_SESSION[Sessions::SESSION_LOGIN_VAR]}'" );
                }
                if ( $accounts->number_of_records == 0 ) {
                    // If the user has been deleted - delete the cookie and log the user out
                    Cookies::destroy_all_cookies();
                    header( "Location: home?logout=1" );
                    $this->logged_in = false;
                } else {
                    Session::set_value(
                        Sessions::SESSION_LOGGED_USER_DATA,
                        [
                            'first_name'        => $accounts->data->first_name,
                            'last_name'         => $accounts->data->last_name,
                            'formatted_name'    => $accounts->data->formatted_name(),
                            'ldap_user'         => $accounts->data->ldap_user,
                            'linked_to_teacher' => $accounts->data->linked_to_teacher,
                            'email'             => $accounts->data->email,
                        ]
                    );
                    Session::set_value(
                        Sessions::SESSION_SCHOOL_DATA,
                        JSONTools::read_json_file_to_object( SCHOOL_CONFIG_INFO )
                    );
                    // 1 days duration, 5 Minutes timer
                    setcookie( Cookies::REFRESH_TIMER, ( ( ( time() + ( 300 ) ) * 7 ) + 4 ), time() + ( 1*24*60*60 ), '/', "", false, true );
                }
            }
        }
    }


    /**
     * Checks if the logout requirements are fulfilled then performs the logout
     *
     * @access  public 
     * @since   3.1.0
     */

    public function check_logout() {
        if ( isset( $_GET['logout'] ) && $_GET['logout'] == '1' ) {
            if ( $this->logged_in ) {
                Session::destroy_all_values();
            }
            Cookies::destroy_all_cookies();
            $this->logged_in = false;
        }
    }


    /**
     * Check if an API call has been made
     * 
     * @access  public
     * @since   3.16.1
     */

    public function check_api_call(): void {
        $this->valid_api_key = false;
        if ( is_null( $this->api_key ) ) {
            return;
        }

        $user_accounts = new UserAccountsData();
        $user_accounts->select_all( "api_key='{$this->api_key}'" );
        if ( $user_accounts->number_of_records > 0 ) {
            $this->valid_api_key = true;
        }
    }


    /**
     * Return the name of the currently logged in user
     * 
     * @return  string|null If user is not logged in, return null
     * 
     * @access  public
     * @since   3.15.0
     */

    public static function get_current_logged_in_user(): ?string {
        return isset( $_SESSION[Sessions::SESSION_LOGIN_VAR] ) ? $_SESSION[Sessions::SESSION_LOGIN_VAR] : null;
    }


    /**
     * Check the refresh requirements.
     * 
     * If:
     * - timer isn't set OR
     * - timer has expired OR
     * - a force refresh is commanded OR
     * - an permissions error was previously thrown.
     * - In maintenance mode
     * 
     * @return  boolean
     * 
     * @since   3.15.11
     */

    public static function check_page_refresh_token(): bool {
        return !isset( $_COOKIE[Cookies::REFRESH_TIMER] ) || 
        ( ( ( $_COOKIE[Cookies::REFRESH_TIMER] - 4 ) / 7 ) - time() ) < 10 ||
        isset( $_POST['force_refresh'] ) && $_POST['force_refresh'] == 1 ||
        isset( $_SESSION[Sessions::SESSION_USER_PERMISSIONS] ) && $_SESSION[Sessions::SESSION_USER_PERMISSIONS]->can_access == false ||
        !isset( $_SESSION[Sessions::SESSION_LOGGED_USER_DATA] ) ||
        !isset( $_SESSION[Sessions::SESSION_USER_PERMISSIONS] ) ||
        !isset( $_SESSION[Sessions::SESSION_SCHOOL_DATA] ) ||
        ENVIRONMENT == MODE_MAINTENANCE;
    }

}