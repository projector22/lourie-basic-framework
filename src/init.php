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

    /**
     * The data which is gathered from the user in setting up the user's app.
     * 
     * @var array   $data
     * 
     * @access  private
     * @since   LBF 0.3.0-beta
     */

    private array $data;

    /**
     * The working directory into which this tool will create all the required files.
     * 
     * @var string  $working_dir    If not otherwise set, defaults to realpath( __DIR__ . '/../../../../' ).
     * 
     * @readonly
     * @access  private
     * @since   LBF 0.3.0-beta
     */

    private readonly string $working_dir;

    /**
     * The directory structure used by the application.
     * 
     * @var array   DIR_STRUCTURE
     * 
     * @access  public
     * @since   LBF 0.3.0-beta
     */

    const DIR_STRUCTURE = [
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
        'bin',
    ];

    /**
     * The files to create as used by the application.
     * 
     * @var array   FILE_STRUCTURE
     * 
     * @access  public
     * @since   LBF 0.3.0-beta
     */


    const FILE_STRUCTURE = [
        'index.php',
        'src/router.php',
        'src/loader.php',
        'src/app/actions/ActionHandler.php',
        'src/app/boilerplate/html_footer.php',
        'src/app/boilerplate/HTMLHeader.php',
        'src/includes/meta.php',
        'src/includes/meta-files.php',
        'src/includes/meta-paths.php',
        'src/includes/meta-tables.php',
        'src/includes/static-routes.php',
        'src/css/styles.css',
        'src/js/lib.js',
    ];


    /**
     * Class constructor.
     * 
     * @access  public
     * @since   LBF 0.3.0-beta
     */

    public function __construct() {
        if ( isset( $_SERVER['REMOTE_ADDR'] ) ) {
            die( 'Permission denied. You may not run this file in the browser' );
        }
        $this->working_dir = realpath( __DIR__ . '/../../../../' );
    }


    /**
     * Perform the task of gathering all the user defined data required by the user's app.
     * 
     * @access  public
     * @since   LBF 0.3.0-beta
     */

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


    /**
     * Perform the task of creating and writing all the files and folders used by the app, as well as performing other build tasks like
     * running Composer.
     * 
     * @access  public
     * @since   LBF 0.3.0-beta
     */

    public function build(): void {
        $this->build_folder_structure();
        $this->build_file_structure();

    }


    /**
     * Tools for laying out the file structure of the application.
     * 
     * See the constant `FILE_STRUCTURE` for the name of each file.
     * 
     * @access  public
     * @since   0.3.0-beta
     */

    public function build_file_structure(): void {
        echo "Creating required files: ";
        foreach ( self::FILE_STRUCTURE as $file ) {
            $new_file = $this->working_dir . '/' . $file;
            touch( $new_file );
            $write = fopen( $new_file, 'w' );
            fwrite( $write, match( $file ) {
                'index.php'                           => '',
                'src/router.php'                      => '',
                'src/loader.php'                      => '',
                'src/app/actions/ActionHandler.php'   => '',
                'src/app/boilerplate/html_footer.php' => '',
                'src/app/boilerplate/HTMLHeader.php'  => '',
                'src/includes/meta.php'               => '',
                'src/includes/meta-files.php'         => '',
                'src/includes/meta-paths.php'         => '',
                'src/includes/meta-tables.php'        => '',
                'src/includes/static-routes.php'      => '',
                'src/css/styles.css'                  => '',
                'src/js/lib.js'                       => '',
                default                               => '',
            } );
            fclose( $write );
            echo ".";
        }
        echo " Done";
        $this->lb();
    }


    /**
     * Tool for laying out the folder structure of the application.
     * 
     * See the constant `DIR_STRUCTURE` for the name of each file.
     * 
     * @access  private
     * @since   LBF 0.3.0-beta
     */

    private function build_folder_structure(): void {
        echo "Creating folder structure: ";
        $this->create_dirs( self::DIR_STRUCTURE, $this->working_dir );
        echo " Done";
        $this->lb();
    }


    /**
     * Recursively create the various directories required by the app.
     * 
     * @param   array   $file_system    File structure to be processed and created.
     * @param   string  $working_dir    The directory into which the folders should be created.
     * 
     * @access  private
     * @since   LBF 0.3.0-beta
     */

    private function create_dirs( array $file_system, string $working_dir ): void {
        foreach ( $file_system as $dir_name => $sub_dir ) {
            if ( is_string( $sub_dir ) ) {
                if ( !file_exists( $working_dir . '/' . $sub_dir ) ) {
                    mkdir(
                        directory: $working_dir . '/' . $sub_dir, 
                        recursive: true
                    );
                }
                echo ".";
            } else {
                $this->create_dirs( $sub_dir, $working_dir . '/' . $dir_name );
            }
        }
    }


    /**
     * Debug tool for displaying array data onto the terminal.
     * 
     * @param   array|null  $data   Data to display, if null, `$this->data` will be displayed. Default: null.
     * @param   string      $pfx    A prefext to attach to a line, default is `''`, but anything can be parsed, including `\t` tabs.
     * 
     * @access  public
     * @since   0.3.0-beta
     */

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


    /**
     * Add a defined number of line breaks.
     * 
     * @param   int $lines  The number of lines to add. Default is 1.
     * 
     * @access  private
     * @since   0.3.0-beta
     */

    private function lb( int $lines = 1 ): void {
        for ( $i = 0; $i < $lines; $i++ ) {
            echo "\n";
        }
    }

}