<?php

/**
 * Dynamically load a class when called in the context of the CLI. Class name needs to be the same as the file in which it is found
 * 
 * @param   string  $class  Name of the class being called
 * 
 * @since   3.14.0
 */

function load_cli_class( string $class ): bool {
    $class = normalize_path_string( $class );
    $path = CLI_PATH . 'classes/' . $class . '.php';
    if ( !file_exists ( $path ) ) {
        echo "Error: Class path <i>{$path}</i> does not exist";
        return false;
    }
    require_once realpath( $path );
    return true;
}