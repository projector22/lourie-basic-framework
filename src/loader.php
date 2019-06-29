<?php

/* This script looks at anything in the includes/ folder and automatically loads them */

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

function load_class( $class ){
    $path = CLASSES_PATH . '/' . $class . '.php';
    if ( !file_exists ( $path ) ){
        return false;
    }
    require_once $path;
}

@session_start();



//Set site wide class variables
$permit = new SitePermissions;
$menu = new Menu;





// $path = HOME_PATH . 'src/classes/';
// $files = scandir( $path );

// foreach ( $files as $file ){
//     if ( $file != '.' && $file != '..' ){
//         require_once $path . $file;    
//     }//if    
// }//foreach

/* Hook in the general app functions */

require_once 'functions.php';

if ( !is_file( HOME_PATH . 'src/includes/config.php' ) ){
    require HOME_PATH . 'src/install.php';
} else {
    require HOME_PATH . 'src/home.php';
}
