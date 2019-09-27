<?php

/**
 * This page draws the admin page
 * 
 * @author  Gareth Palmer  @evangeltheology
 * 
 * @since   3.1
 */

require_once 'src/loader.php';
PageElements::top_of_page();

//Checks for the session variable
$self = $permit->session_login_var;

if ( !$permit->super_admin && !$permit->site_admin ) {
    header( "Location: index.php" );
}

if ( isset( $_GET['p'] ) ){
    $page = $_GET['p'];
} else {
    $page = ''; //Change this to whatever you'd like as default
}

switch ( $page ){ 
    default:
        'Nothing to see here yet';
        break;
}