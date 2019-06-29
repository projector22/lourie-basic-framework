<?php

/**
 * 
 * Load all the prerequisite files and classes, defines the home path starts the session and loads some site wide variables, then finally tests if the app is installed and 
 * executes the rest of the site or the installer as needed.
 * 
 * @author  Gareth  Palmer  @evangeltheology
 * @version 1.0
 * @since   0.1 Pre-alpha
 */

/**
 * 
 * Loads the defined class
 * 
 * @param   string  $class  The class name that needs to be loaded
 * @return  false   If the file does not exist
 * @version 1.0
 * @since   0.1 Pre-alpha
 */

function load_class( $class ){
    $path = CLASSES_PATH . '/' . $class . '.php';
    if ( !file_exists ( $path ) ){
        return false;
    }
    require_once $path;
}

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

@session_start();

//Set site wide class variables
$permit = new SitePermissions;
$menu = new Menu;

if ( ENVIRONMENT == 'dev' ){
    $debug_tools = new DebugTools;
}

// Hook in the general app functions
require_once 'functions.php';

if ( !is_file( HOME_PATH . 'src/includes/config.php' ) ){
    require HOME_PATH . 'src/install.php';
} else {
    require HOME_PATH . 'src/home.php';
}

//Check if the server meets the minimum requirements to run the app and all the required files are present and loaded
$check_site = new SiteChecks;
