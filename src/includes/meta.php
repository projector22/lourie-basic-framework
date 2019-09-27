<?php

/**
 * 
 * Contains meta information used by the site, mostly in the form of defined constants. 
 * This is often information that the site admin may wish to fiddle with over time according to their needs
 * 
 * @author  Gareth  Palmer  @evangeltheology
 * 
 * @since   0.1 Pre-alpha
 */

$site_version = '0.0 Pre-Alpha';
$program_name = "Lourie";
$site_author = 'Gareth Palmer';

$max_upload_size = 30000000;

//Comment out whichever is inapropriate, set to live for the production environment.
// $environment = 'live';
$environment = 'dev';

define( 'PROGRAM_VERSION', $site_version );
define( 'PROGRAM_NAME', $program_name );
define( 'SITE_AUTHOR', $site_author );
define( 'ENVIRONMENT', $environment );

// This section is where most key global constants are defined
$name = explode( "\\", dirname( __FILE__ ) );
$path = $name[3];

foreach ( $name as $i => $n ){
    if ( $n == 'src' ) {
        if ( $name[$i-1] != 'www' ){
            $path = $name[$i-1];
        } else {
            $path = '..';
        }//if $name[$i-1] != 'www'
    }// if $n == 'src'
}//foreach

$server_request = explode( '/', $_SERVER['REQUEST_URI'] );
$server_loc = '/';
foreach( $server_request as $i => $s ){
    if ( $i == 0 ){
        continue;
    }
    if ( strpos( $s, '.php' ) == false ){
        $server_loc .= $s . '/';
    } else {
        break;
    }
}

/*
 * This checks for the location of the whole scripts, if its hosted on the main site or in a subfolder
 * For example example.com vs example.com/lourie-folder
 * This makes a difference in how HOME_PATH & HOME_LOC is defined
 */ 

if ( realpath( $_SERVER['DOCUMENT_ROOT'] . '/src' ) == dirname( __FILE__ ) ){
    $together = $_SERVER['DOCUMENT_ROOT'];
} else {
    $together = $_SERVER['DOCUMENT_ROOT'] . "\\" .  $path;
}

//Folder Paths
if ( !defined( 'HOME_PATH' ) ){
    if ( realpath( $_SERVER['DOCUMENT_ROOT'] . '/src/includes' ) == dirname( __FILE__ ) ){
        $together = $_SERVER['DOCUMENT_ROOT'];
    } else {
        $together = $_SERVER['DOCUMENT_ROOT'] . "\\" .  $path;
    }

    define( 'HOME_PATH',  $together. '/' );
}

//The installed server location, root or subfolder
define( 'HOME_LOC', $server_loc );

//The path - checking for subdirectory url or not
define( 'WEB_PATH', $path . '/' );

define( 'SRC_PATH',      HOME_PATH . 'src/' );
define( 'UPLOADS_PATH',  HOME_PATH . 'uploads/' );
define( 'INCLUDES_PATH', SRC_PATH  . 'includes/' );
define( 'CLASSES_PATH',  SRC_PATH  . 'classes/' );
define( 'JS_PATH',       SRC_PATH  . 'js/' );
define( 'IMG_PATH',      SRC_PATH  . 'img/' );
define( 'STYLES_PATH',   SRC_PATH  . 'styles/' );

//Pages
define( "HOME_PAGE",  "index.php" );
define( "ADMIN_PAGE", "admin.php" );
define( 'SITE_HELP',  "help.php" );

//Logo
define( "SITE_LOGO", "placeholder.png" );

//Upload Size
define( 'MAX_UPLOAD_SIZE', $max_upload_size );

//Update URLs
//These have placeholder values and will need to be changed to appropriate values
define( 'UPDATE_URL',         'https://path.to.your.update.net' );
define( 'UPDATE_FILE',         UPDATE_URL . '/file.zip' );
define( 'GIT_REPO_URL',       'https://github.com/your-username/your-project.git' );
define( 'FRAMEWORK_REPO_URL', 'https://gitlab.com/projector22/lourie-basic-framework.git' );