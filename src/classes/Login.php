<?php

/**
 * 
 * Functions to perform login functions
 * 
 * @author  Gareth  Palmer  @evangeltheology
 * 
 * @since   0.1 Pre-alpha
 */

class Login {

    private $db_control;
    private $permit;

    /**
     * Consructor method, things to do when the class is loaded
     * 
     * @since   0.1 Pre-alpha
     */

    public function __construct(){
        $this->db_control = new DatabaseControl;
        $this->permit     = new SitePermissions;
    }//__construct

    /**
     * 
     * Generate the session ID key
     * 
     * @param   string  $username   The username of the user loggin in
     * @param   string  $fragment   A fragment of the already hashed password of the afore mentioned user
     * @return  string              A string which represents the session ID in the form of $username | $hash
     * 
     * @since   0.1 Pre-alpha
     */

    public function generate_session_id( $username, $fragment ){
        $key = $username . "|" . hash( 'sha256', $username . $fragment . COOKIE_HASH );
        return $key;
    }

    /**
     * 
     * Grab a substring of the user's already hashed password
     * 
     * @param   string  $password   The user's already hashed password
     * @return  string              A 4 character substring of the user's password
     * 
     * @since   0.1 Pre-alpha
     */

    public function password_substr( $password ){
        $sub = substr( $password, 8, 4 );
        return $sub;
    }
    
    /**
     * 
     * Once authenticated, this starts the session, sets the session variables and creates the session cookie to keep the user logged in.
     * Also it updates the database where required.
     * 
     * @param   string  $username       The logged in user's username
     * @param   string  $permissions    The logged in user's predefined access permissions
     * @param   string  $fragment       A fragment of the logged in user's already hashed password 
     * 
     * @since   0.1 Pre-alpha
     */

    public function set_session( $username, $permissions, $fragment ){
        session_start();

        $ts = date( 'Y-m-d G:i:s' );
        
        $_SESSION[$this->permit->session_login_var] = $username;
        $_SESSION[$this->permit->session_permit_var] = $permissions;
        
        $session_id = $this->generate_session_id( $username, $fragment );
    
        setcookie( $this->permit->cookie_session_var, $session_id, time()+( 45*24*60*60*1000 ), '/' );//45 days
    
        //log session start
        $ip = $_SERVER['REMOTE_ADDR'];
        $browser = $_SERVER['HTTP_USER_AGENT'];
        $this->db_control->sql_execute( "INSERT INTO " . SESSION_LOGS . " (account_id, ip, browser, timestamp) VALUES ('$username','$ip','$browser','$ts')" );
        $this->db_control->sql_execute( "UPDATE " . USER_ACCOUNTS . " SET last_login='$ts' WHERE account_name='$username'" );
    }

    /**
     * 
     * Part of the static 
     */
    
    public function login_heading(){
        echo "<img src='src/img/" . SITE_LOGO . "' alt='Logo Placeholder' width='200px'>";
        echo "<h1 class='login_heading'>Login</h1>";
    }

    /**
     * Destructor method, things to do when the class is closed
     * 
     * @since   0.1 Pre-alpha
     */

    public function __destruct(){

    }//__destruct
}