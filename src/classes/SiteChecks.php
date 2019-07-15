<?php

/**
 * This class allows the site to check for any prerequisites and to either die immediately if the problem cannot be fixed or ignored
 * from the user's side, or to return a variable $passes_all_checks with a true or false which allows the user to take action accordingly.
 * 
 * @author  Gareth Palmer  @evangeltheology
 * 
 * @since   0.1 Pre-alpha
 */

class SiteChecks {
    
     /**
      * Minimum required version of PHP allowed
      * 
      * @var string
      *
      * @since 0.1 Pre-alpha
      */

    private $min_php_version = '7.2.0';


    /**
     * Variable to be called if testing for all checks to be successfully passed
     *
     * @var boolean
     *
     * @since 0.1 Pre-alpha
     */

    public $passes_all_checks = true;


    /**
     * Consructor method, things to do when the class is loaded
     * 
     * @since   0.1 Pre-alpha
     */

    public function __construct(){
        $this->check_php_version();
    }//__construct

    
    /**
     * Check the php version, dies if test fails
     * 
     * @since   0.1 Pre-alpha
     */

    private function check_php_version(){
        if ( phpversion() < $this->min_php_version ){
            $this->passes_all_checks = false;
            die( "<h1>Unfortunately your webserver is running PHP Version " . phpversion() . ". Please upgrade it to at least PHP Version " . $this->min_php_version . "</h1>" );
        }//if 
    }

}