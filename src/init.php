#!/usr/bin/env php
<?php

/**
 * @todo    Build this tool
 * 
 * The idea is that, but running it, it builds out the APP environment for you. This is
 * if you are spinning up a new app from scratch. Creating files and folder permissions.
 * 
 * My also be used in deployment, functionally as stage one (or two after `composer install`)
 * of an installer.
 * 
 * It may even be set to hook into the app's local installer.
 * 
 * @since   LBF 0.0.1
 */

// echo "Enter a name for your app?";

$builder = new Init;
$builder->gather_data();


class Init {

    private array $data;

    public function __construct() {
        if ( isset( $_SERVER['REMOTE_ADDR'] ) ) {
            die( 'Permission denied. You may not run this file in the browser' );
        }
    }

    public function gather_data() {
        // $this->data['app_name'] = readline( "Enter a name for your app: " );
        // $this->data['description'] = readline( "Enter a simple description of your app: " );
        // $ver = readline( "Enter the starting version, without alpha or beta (Leave blank for '0.1.0'): " );
        // $this->data['version'] = $ver !== '' ? $ver : '0.1.0';
        $status = 'z';
        $valid = ['a', 'b', ''];
        while ( !in_array( strtolower( $status ), $valid ) ) {
            $status = readline( "Set the app to an Alpha or Beta status, entry 'a' or 'b' or leave blank to not set to 'live': " );
        }
        $this->data['status'] = strtolower( $status );
    }
}