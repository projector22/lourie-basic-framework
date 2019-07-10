<?php

/**
 * The basic HTML which forms the top elements of the generated HTML of the app
 * 
 * @author  Gareth  Palmer  @evangeltheology
 * 
 * @since   0.1 Pre-alpha
 */

$favicon = 'placeholder.png';


/**
 * This function will dynamically load any styles besides the two main ones already defined (styles.small.css & 
 * style.large.css)
 * 
 * @author  Gareth  Palmer  @evangeltheology
 * 
 * @since   0.1 Pre-alpha
 */

function load_other_styles(){
    $files = scandir( STYLES_PATH );
    foreach ( $files as $file ){
        if ( $file != '.' && $file != '..' && $file != 'styles.small.css' && $file != 'styles.large.css' ){
            if ( is_file( 'src/styles/' . $file ) ){
                echo "\n\t<link rel='stylesheet' type='text/css' href='src/styles/$file'>";
            }
        }//if
    }//foreach

}

echo "<!DOCTYPE html>
<html lang='eng'>
<head>
    <meta charset='UTF-8'>
    <title>" . PROGRAM_NAME . "</title>
    <link rel='stylesheet' type='text/css' href='src/styles/styles.small.css'>
    <link rel='stylesheet' type='text/css' href='src/styles/styles.large.css'>";
    load_other_styles();
    echo "\n\t<script src='src/js/scripts.js'></script>
    <script src='src/js/pace.js'></script>
    <link rel='icon' type='image/png' href='src/img/$favicon'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>\n";
    if ( ENVIRONMENT == 'dev' ){
        echo "\t<meta name='robots' content='noindex, nofollow'>\n\t<meta name='googlebot' content='noindex, nofollow'>";
    }
echo "\n</head>
<body>
<div class='wrapper'>\n";