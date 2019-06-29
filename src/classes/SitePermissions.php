<?php

/**
 * 
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
     * 
     * @var string  $session_var    Session variable, can be renamed to purpose or made dynamic as per requirement
     * 
     * @since   0.1 Pre-alpha
     */

    private $session_var = 'permissions';

    /**
     * 
     * @var array   $permission_id  Array from the site variable $_SESSION[$this->session_var]
     * 
     * @since   0.1 Pre-alpha
     */

    private $permission_id;

    /**
     * 
     * Should be made dynamic in the future
     * 
     * @var boolean $super_admin    Default: false
     * @var boolean $site_admin     Default: false
     * 
     * @since   0.1 Pre-alpha
     */

    public $super_admin = false;
    public $site_admin = false;
    
    public function __construct(){
        if ( !isset( $_SESSION[$this->session_var] ) ){
            return;
        }
        $this->permission_id = str_split( $_SESSION[$this->session_var] );
        
        if ( $this->permission_id[0] == '1' ){
            $this->super_admin = true;
        }

        if ( $this->permission_id[1] == '1' ){
            $this->site_admin = true;
        }
    }//__construct
    
    public function __destruct(){

    }//__destruct
}