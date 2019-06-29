<?php

/*
 * The purpose of this class is to get and return a boolean variable for each permission level.
 * It first resets the variables to false then tests for $SESSION['permissions']
 * Functions only for the logged in user
 */

class SitePermissions {
    
    private $permission_id;
    public $super_admin = false;
    public $site_admin = false;
    public $registrar = false;
    public $discipline = false;
    
    public function __construct(){
        if ( !isset( $_SESSION['permissions'] ) ){
            return;
        }
        $this->permission_id = str_split( $_SESSION['permissions'] );
        
        if ( $this->permission_id[0] == '1' ){
            $this->super_admin = true;
        }

        if ( $this->permission_id[1] == '1' ){
            $this->site_admin = true;
        }

        if ( $this->permission_id[2] == '1' ){
            $this->registrar = true;
        }

        if ( $this->permission_id[3] == '1' ){
            $this->discipline = true;
        }
    }//__construct
    
    public function __destruct(){

    }//__destruct
}