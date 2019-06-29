<?php

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

function protect( $data ) {
    $data = trim( $data );
    $data = stripslashes( $data );
    $data = htmlspecialchars( $data, ENT_QUOTES );
    return $data;
}

function serverlist(){
    $array = $_SERVER;
    $list_title = array_keys( $array );
    echo "<table>";
    echo "<tr><th>Index</th><th>Key</th></tr>";

    foreach ( $array as $key => $list ){
        $arr_key = array_search( $key, array_keys( $array ) );
        $item = $list_title[ $arr_key ];
        echo "<tr><td>$item</td><td>$list</td></tr>";
    }//foreach
    echo "</table>";
}

function display_array( $data ){
    echo "<pre>";
    print_r( $data );
    echo "</pre>";
}

function see_whats_posted( $post ){
    foreach ( $post as $i => $k ){
        echo "<b>$i</b>: $k<br>";
    }
}
