<?php

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

function connectDB(){
    $link = mysqli_connect( DB_LOC, DB_USER, DB_PASS, DB_NAME );
    if( $link === false ){
        die( "ERROR: Could not connect. " . mysqli_connect_error() );
    }
    return $link;
}

function page_header(){
    require_once( 'header.php' );
}

function footer(){
    require_once( 'footer.php' );
}

function token( $token ){
    echo "<input type='hidden' value='$token' name='token' id='token'>";
}

function setToken() {
    if ( isset( $_POST['token'] ) ) {
        $token = $_POST['token'];
        return $token;
    } else if ( isset( $_GET['token']) ){
        $token = $_GET['token'];
        return $token;
    }
}

function lines( $k ){
    for( $i = 0; $i < $k; $i++){
        echo "<br>";
    }
}

function dot(){
    echo "<b>.</b>";
}

function remove_trailing_chars( $data, $test ){
    while ( substr( $data, -1 ) == $test ){
        $data = rtrim( $data, $test );  
    }//while
    return $data;
}

function element_spacer_one() {
    echo "<span class='element_spacer_one'></span><span></span>";    
}


// function get_contents( $file ){
//     $data = json_decode( file_get_contents( $file ), true );
//     return $data;
// }


function substr_between( $string, $start, $end ){
    $string = ' ' . $string;
    $ini = strpos( $string, $start );
    if ($ini == 0) {
        return '';
    }
    $ini += strlen( $start );
    $len = strpos( $string, $end, $ini ) - $ini;
    return substr( $string, $ini, $len );
}

function delete_folder( $path ){
    if ( PHP_OS === 'WINNT' ){
        exec( sprintf( "rd /s /q %s", escapeshellarg( $path ) ) );
    } else {
        exec( sprintf( "rm -rf %s", escapeshellarg( $path ) ) );
    }
}

// function protect( $data ) {
//     $data = trim( $data );
//     $data = stripslashes( $data );
//     $data = htmlspecialchars( $data, ENT_QUOTES );
//     return $data;
// }