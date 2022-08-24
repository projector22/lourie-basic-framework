<?php

namespace Framework\Auth;

/**
 * This class allows the site to check for any prerequisites and to either die immediately if the problem cannot be fixed or ignored
 * from the user's side, or to return a variable $passes_all_checks with a true or false which allows the user to take action accordingly.
 * 
 * This class simply needs to be called to a variable to run all of it's checks.
 * 
 * use Framework\Auth\SiteChecks;
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @since   3.1.0
 */

class SiteChecks {
    
    /**
     * Minimum required version of PHP allowed
     * 
     * @var string $min_php_version
     * 
     * @access  private
     * @since   3.1.0
     * @since   3.17.2  Updated to 8.0.0
     * @since   3.25.4  Updated to 8.1.0
     */

    private string $min_php_version = '8.1.0';


    /**
     * Variable to be called if testing for all checks to be successfully passed
     * 
     * @var boolean $passes_all_checks  Default: true
     * 
     * @access  public
     * @since   3.1.0
     */

    public bool $passes_all_checks = true;


    /**
     * Constructor method, things to do when the class is loaded
     * 
     * @access  public
     * @since   3.1.0
     */

    public function __construct() {
        $this->check_php_version();
    } //__construct


    /**
     * Check the php version, dies if test fails
     * 
     * @access  private
     * @since   3.1.0
     */

    private function check_php_version(): void {
        if ( phpversion() < $this->min_php_version ){
            $this->passes_all_checks = false;
            die( "<h1>Unfortunately your webserver is running PHP Version " . phpversion() . ". Please upgrade it to at least PHP Version {$this->min_php_version}</h1>" );
        }
    }

}