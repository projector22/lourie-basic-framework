<?php

/*
 * Site Author: Gareth Palmer
 * Start Date: 2019-05-11
 */

$site_version = '0.0 Pre-Alpha';
$program_name = "Lourie";
$site_author = 'Gareth Palmer';

$max_upload_size = 30000000;

define( 'PROGRAM_VERSION', $site_version );
define( 'PROGRAM_NAME', $program_name );
define( 'SITE_AUTHOR', $site_author );

// This section is where most key global constants are defined
$name = explode( "\\", dirname( __FILE__ ) );

$path = $name[3];

foreach ( $name as $i => $n ){
    if ( $n == 'src' ){
        $path = $name[$i-1];
    }//if - look for 'scripts'
}//foreach

/*
 * This checks for the location of the whole scripts, if its hosted on the main site or in a subfolder
 * For example example.com vs example.com/lourie-registration-system
 * This makes a difference in how HOME_PATH is defined
 */ 

if ( realpath( $_SERVER['DOCUMENT_ROOT'] . '/src' ) == dirname( __FILE__ ) ){
    $together = $_SERVER['DOCUMENT_ROOT'];
} else {
    $together = $_SERVER['DOCUMENT_ROOT'] . "\\" .  $path;
}

//Folder Paths
if ( !defined( 'HOME_PATH' ) ){
    define( 'HOME_PATH',  $together. '/' );
}

define( 'SRC_PATH', HOME_PATH . 'src/' );
define( 'INCLUDES_PATH', SRC_PATH . 'includes/' );
define( 'CLASSES_PATH', SRC_PATH . 'classes/' );