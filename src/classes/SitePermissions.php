<?php

/**
 * This class gets the $_SESSION permissions variable and sets a public function for each permission level.
 * It then sets each as a boolean variable which can be pulled from outside class to test site permissions.
 * 
 * @author  Gareth  Palmer  @evangeltheology
 * 
 * @since   0.1 Pre-alpha
 */

class SitePermissions {
    //TO DO - Make the public variables more dynamic, possibly pulled from a list in the database
    
    /**
     * @var string  $session_login_var      Session login variable, used to test if the user is logged in or not
     * @var string  $session_permit_var     Session variable, can be renamed to purpose or made dynamic as per requirement
     * @var string  $cookie_session_var     Cookie variable, used to signify the login session of the user
     * 
     * @since   0.1 Pre-alpha
     */

    public $session_login_var =     'account' . SESSION_HASH;
    public $session_permit_var =    'permissions' . SESSION_HASH;
    public $cookie_session_var =    'login_session_id_' . COOKIE_HASH;

    /**
     * @var array   $permission_id  Array from the site variable $_SESSION[$this->session_permit_var]
     * 
     * @since   0.1 Pre-alpha
     */

    private $permission_id;

    /**
     * Should be made dynamic in the future
     * 
     * @var boolean $super_admin    Default: false
     * @var boolean $site_admin     Default: false
     * 
     * @since   0.1 Pre-alpha
     */

    public $super_admin = false;
    public $site_admin  = false;

    /**
     * Consructor method, things to do when the class is loaded
     * 
     * @since   0.1 Pre-alpha
     */
    
    public function __construct(){
        if ( isset( $_SESSION[$this->session_login_var] ) ){
            $this->set_permit_var();
        }
    }//__construct

    /**
     * Sets the permissions variables
     * 
     * @since   0.1 Pre-alpha
     */

    private function set_permit_var(){
        if ( !isset( $_SESSION[$this->session_permit_var] ) ){
            return;
        }
        $this->permission_id = str_split( $_SESSION[$this->session_permit_var] );
        
        if ( $this->permission_id[0] == '1' ){
            $this->super_admin = true;
        }

        if ( $this->permission_id[1] == '1' ){
            $this->site_admin = true;
        }
    }

    /**
     * Checks if the login session variable is set and forces login if not
     * 
     * @since   0.1 Pre-alpha
     */

    public function check_login(){
        if ( !isset( $_SESSION[$this->session_login_var] ) ){
            require SRC_PATH . 'login.php';
            PageElements::footer();
            die;
        }
    }

    /**
     * Checks if the logout requirements are fulfilled then performs the logout
     * 
     * @since   0.1 Pre-alpha
     */

    public function check_logout(){
        if ( isset( $_GET['logout'] ) && $_GET['logout'] == '1' ){
            if ( isset( $_SESSION[$this->session_login_var] ) ){
                unset( $_SESSION[$this->session_login_var]);
                unset( $_COOKIE[$this->cookie_session_var]);
                setcookie( $this->cookie_session_var, '', 1, '/' );
            }//isset( $_SESSION[$this->session_login_var]
        }//isset( $_GET['logout'] ) && $_GET['logout'] == '1'        
    }

}