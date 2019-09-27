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


/**
 * Protect $_POST or $_GET data from various potential attacks, such as sql injection and XSS
 * 
 * @param   string  $data   Some kind of input data and many various kinds
 * 
 * @return  string  $data   The cleaned up data
 * 
 * @since   0.1 Pre-alpha
 */

function protect( $data ) {
    
    /**
     * This removes whitespace and other predefined characters from both sides of a string
     * 
     * @link    https://www.w3schools.com/php/func_string_trim.asp
     */

    $data = trim( $data );

    /**
     * This removes backslashes 
     * 
     * @link    https://www.w3schools.com/php/func_string_stripslashes.asp
     */
    
    $data = stripslashes( $data );

    /**
     * The htmlspecialchars() function converts some predefined characters to HTML entities.
     * 
     * The predefined characters are:
     * - & (ampersand) becomes &amp;
     * - " (double quote) becomes &quot;
     * - ' (single quote) becomes &#039;
     * - < (less than) becomes &lt;
     * - > (greater than) becomes &gt;
     * 
     * @link    https://www.w3schools.com/php/func_string_htmlspecialchars.asp
     */

    $data = htmlspecialchars( $data, ENT_QUOTES );

    return $data;
}


/**
 * Shortcut for setting a hidden input element named 'token'
 * 
 * Generally interpreted by setToken()
 * 
 * @since   0.1 Pre-alpha
 */

function token( $token ) {
    echo "<input type='hidden' name='token' value='$token'>";
}


/**
 * Checks if there is $_POST or $GET named 'token' and if so, return it's content
 * 
 * Order:
 * - $_POST
 * - $_GET
 * 
 * @since   0.1 Pre-alpha
 */

function setToken() {
    if ( isset( $_POST['token'] ) ) {
        $token = $_POST['token'];
        return $token;
    } else if ( isset( $_GET['token']) ){
        $token = $_GET['token'];
        return $token;
    }
}


/**
 * Encode the data as JSON and write it to the daily routine file (DAILY_ROUTINE)
 * 
 * @param   array   $records    An array of data to be encoded and written to file
 * @param   string  $file_name  Path & file name the json file to be written
 * 
 * @since   0.1 Pre-alpha
 */

function create_encoded_json( $records, $file_name ){
    $json = json_encode( $records, JSON_PRETTY_PRINT );    
    if ( !file_exists( $file_name ) ){
        //write data to file
        $new_config_write = fopen( $file_name, 'w' );
        fwrite( $new_config_write, $json );
        fclose( $new_config_write );
    } else {
        file_put_contents( $file_name, $json );
    }
}


/**
 * Remove all of the trailing characters from the end of a string
 * 
 * TODO - check if this cannot be achieved by direct use of the trim or rtrtim functions
 * 
 * @param   string  $data   The string to be cleaned up
 * @param   string  $test   The character to be removed
 * 
 * @return  string  $data   Cleaned up string
 * 
 * @since   0.1 Pre-alpha
 */

function remove_trailing_chars( $data, $test ){
    while ( substr( $data, -1 ) == $test ){
        $data = rtrim( $data, $test );  
    }//while
    return $data;
}


/**
 * Generate the <option> tags for a <select> droplist
 * 
 * TODO - See if there are other droplists which can be simplified with this
 * 
 * @param   array   $list       An array of values to be placed in the droplist
 * @param   array   $value      The desired values, if they are different from the displayed list   Default: null
 * @param   string  $selected   Which item is to be preselected as the default value                Default: ''
 * 
 * @return  string  $option     The complete HTML of <option> elements which can be placed in a <select>
 * 
 * @since   0.1 Pre-alpha
 */

function build_item_droplist( $list, $value=null, $selected='' ){
    $option = '';

    if ( !is_null( $value ) ){
        foreach ( $list as $i => $item ){
            if ( $value[$i] == $selected ) {
                $option = $option . "<option value='$value[$i]' selected>$item</option>";    
            } else {
                $option = $option . "<option value='$value[$i]'>$item</option>";
            }
         }//foreach
    } else {
        foreach ( $list as $i => $item ){
            if ( $item == $selected) {
                $option = $option . "<option value='$item'>$item</option>";
            } else {
                $option = $option . "<option value='$item'>$item</option>";
            }
        }//foreach
    }//if
    return $option;
}


/**
 * Get the string in between two strings
 * 
 * @param   string  $string     The full string to be examined
 * @param   string  $start      The starting point within $string
 * @param   string  $end        The end point within $string
 * 
 * @return  string              The string results of the above workings
 * 
 * Credit:
 * @link    https://stackoverflow.com/questions/5696412/how-to-get-a-substring-between-two-strings-in-php
 * 
 * @since   0.1 Pre-alpha
 */

function get_string_between( $string, $start, $end ){
    $string = ' ' . $string;
    $ini = strpos( $string, $start );
    if ( $ini == 0 ) {
        return '';
    }
    $ini += strlen( $start );
    $len = strpos( $string, $end, $ini ) - $ini;
    return substr( $string, $ini, $len );
}//get_string_between


/**
 * Delete a folder using direct cmd or bash commands
 * 
 * @param   string  $path   Path to folder to be deleted
 * 
 * @since   0.1 Pre-alpha
 */

function delete_folder( $path ){
    if ( PHP_OS === 'WINNT' ){
        exec( sprintf( "rd /s /q %s", escapeshellarg( $path ) ) );
    } else {
        exec( sprintf( "rm -rf %s", escapeshellarg( $path ) ) );
    }
}