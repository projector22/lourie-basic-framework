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

$builder = new Init;
$builder->build();
// $builder->gather_data();
// $builder->display_data();


class Init {

    private array $data;

    private readonly string $working_dir;

    public function __construct() {
        if ( isset( $_SERVER['REMOTE_ADDR'] ) ) {
            die( 'Permission denied. You may not run this file in the browser' );
        }
        $this->working_dir = realpath( __DIR__ . '/../../../../' );
    }

    public function gather_data(): void {
        $this->data['app_name'] = readline( "Enter a name for your app: " );
        $this->data['description'] = readline( "Enter a simple description of your app: " );
        $ver = readline( "Enter the starting version, without alpha or beta (Leave blank for '0.1.0'): " );
        $this->data['version'] = $ver !== '' ? $ver : '0.1.0';
        $status = 'z';
        $valid = ['a', 'b', ''];
        while ( !in_array( strtolower( $status ), $valid ) ) {
            $status = readline( "Set the app to an Alpha or Beta status, entry 'a' or 'b' or leave blank to not set to 'live': " );
        }
        $this->data['status'] = match( strtolower( $status ) ) {
            'a' => 'alpha',
            'b' => 'beta',
            ''  => '',
        };
    }


    public function build(): void {
        echo "Creating folder structure: ";
        $file_system = [
            'src' => [
                'app' => [
                    'actions' => [
                        'pages',
                    ],
                    'boilerplate',
                    'db' => [
                        'data',
                        'tables',
                    ],
                    'enums',
                    'layout',
                    'templates',
                    'web',
                ],
                'css',
                'img',
                'includes',
                'js'
            ],
            'bin' => ['logs'],
        ];
        $this->create_dirs( $file_system, $this->working_dir );
        echo " Done.";
        $this->lb();
    }


    private function create_dirs( array|string $file_system, string $working_dir ): void {
        foreach ( $file_system as $dir_name => $sub_dir ) {
            if ( is_string( $sub_dir ) ) {
                if ( !file_exists( $working_dir . '/' . $sub_dir ) ) {
                    mkdir( $working_dir . '/' . $sub_dir, 0777, true );
                }
                echo ".";
            } else {
                $this->create_dirs( $sub_dir, $working_dir . '/' . $dir_name );
            }
        }
    }


    public function display_data( ?array $data = null, string $pfx = '' ): void {
        $print = '';
        if ( is_null( $data ) ) {
            $data = $this->data;
        }
        foreach ( $this->data as $key => $value ) {
            if ( is_array( $value ) ) {
                $print .= "{$key} -> \n{$pfx}{$this->display_data( $value, "\t" )}";
            }
            $print .= "{$pfx}{$key} -> {$value}\n";
        }
        echo $print;
    }

    private function lb( int $count = 1 ) {
        for ( $i = 0; $i < 1; $i++ ) {
            echo "\n";
        }
    }
}