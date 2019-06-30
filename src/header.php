<?php

/**
 * 
 * The basic HTML which forms the top elements of the generated HTML of the app
 * 
 * @author  Gareth  Palmer  @evangeltheology
 * 
 * @since   0.1 Pre-alpha
 */

echo "<!DOCTYPE html>
<html lang='eng'>
<head>
    <meta charset='UTF-8'>
    <title>" . PROGRAM_NAME . "</title>
    <link rel='stylesheet' type='text/css' href='src/styles/styles-small.css'>
    <link rel='stylesheet' type='text/css' href='src/styles/styles-large.css'>
    <script src='src/js/scripts.js'></script>
    <meta name='viewport' content='width=device-width, initial-scale=1'>\n";
    if ( ENVIRONMENT == 'dev' ){
        echo "\t<meta name='robots' content='noindex, nofollow'>\n\t<meta name='googlebot' content='noindex, nofollow'>";
    }
echo "\n</head>
<body>
<div class='wrapper'>\n";