<?php

/**
 * This page checks for the called action and then calls for the appropriate instructions
 * Usually called from a Javascript AJAX
 * 
 * @author  Gareth Palmer  @evangeltheology
 * 
 * @since   0.1 Pre-alpha
 */

require_once 'includes/meta.php';
require_once SRC_PATH . 'functions.php';
require_once INCLUDES_PATH . 'config.php';

spl_autoload_register( 'load_class' );

$token = setToken();

switch ( $token ){
    default:
        require '404.php';
        break;
}//switch $token