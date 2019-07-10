<?php

/**
 * Load all the prerequisite files and classes, defines the home path starts the session and loads some site wide variables, then finally tests if the app is installed and 
 * executes the rest of the site or the installer as needed.
 * 
 * @author  Gareth  Palmer  @evangeltheology
 * @version 1.0
 * @since   0.1 Pre-alpha
 */

//Hook in the general app functions
require_once 'functions.php';

if ( PHP_OS === 'WINNT' ){
    
    $name = explode( DIRECTORY_SEPARATOR, dirname( __FILE__ ) );

    foreach ( $name as $i => $n ){
        if ( $n == 'src' ){
            $pass = $name[$i-1];
        }//if
    }//foreach

    if ( !isset( $pass ) ){
        die( 'Not all system files present' );
    }//if

    $together = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR .  $pass;
    if ( !defined( 'HOME_PATH' ) ){
        define( 'HOME_PATH',  $together . '/' );    
    }//if
} else {
    if ( !defined( 'HOME_PATH' ) ){
        define( 'HOME_PATH',  $_SERVER['DOCUMENT_ROOT'] . '/' );    
    }//if
}//if detect Server OS

$path = HOME_PATH . 'src/includes/';
$files = scandir( $path );

foreach ( $files as $file ){
    if ( $file != '.' && $file != '..' ){
        require_once $path . $file;
    }//if
}//foreach

spl_autoload_register( 'load_class' );

//Draw the site's header information
require_once 'header.php';

/**
 * Check if the server meets the minimum requirements to run the app 
 * and all the required files are present and loaded 
 * 
 * All the methods are loaded in the constructor
 */

$check_site = new SiteChecks;

//Check for complete instalation
if ( !is_file( INCLUDES_PATH . 'config.php' ) ){
    require SRC_PATH . 'install.php';
} else {
    @session_start();

    //Set site wide class variables
    $permit = new SitePermissions;
    $menu = new Menu;
    $permit->check_logout();
    $permit->check_login();

    //Maybe no longer needed
    if ( ENVIRONMENT == 'dev' ){
        $debug_tools = new DebugTools;
    }

    //Load the home page if on index.php otherwise load whatever page is called
    if ( strpos( $_SERVER['PHP_SELF'], 'index.php' ) > -1 ) {
        require SRC_PATH . 'home.php';
    }
}
