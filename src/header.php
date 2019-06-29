<?php

//This section looks for any files in the 'includes' folder, and loads each of them
$loader = scandir( HOME_PATH . 'src/includes/' );

foreach ( $loader as $load ){
    if (mb_strrchr( $load, '.' ) == '.php' ) {
        require_once( 'includes/' . $load );
    }//if
}//foreach

echo "<!DOCTYPE html>
<html lang='eng'>
<head>
    <meta charset='UTF-8'>
    <title>" . PROGRAM_NAME . "</title>
	<link rel='stylesheet' type='text/css' href='styles.css'>
    <script src='js/scripts.js'></script>
</head>
<body>";
echo "<div class='wrapper'>";