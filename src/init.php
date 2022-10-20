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
// $builder->gather_data();
$builder->display_data();
$builder->build();


class Init {

    /**
     * The data which is gathered from the user in setting up the user's app.
     * 
     * @var array   $data
     * 
     * @access  private
     * @since   LBF 0.3.0-beta
     */

    // private array $data;
    private array $data = [
        'app_name'    => 'Lourie Test App',
        'description' => 'App for testing the init tool',
        'author'      => 'Gareth Palmer [Github & Gitlab /projector22]',
        'version'     => '0.1.0',
        'status'      => 'alpha',
        'app-version' => '0.1.0-alpha',
    ];

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
     * The directory which contains the app templates.
     * 
     * @var string  $template_path
     * 
     * @readonly
     * @access  private
     * @since   LBF 0.3.0-beta
     */

    private readonly string $template_path;

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
            'css' => [
                'pages'
            ],
            'img',
            'includes',
            'js' => [
                'home',
            ]
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
        'index.php'                           => 'index.template',
        'src/router.php'                      => 'src_router.template',
        'src/loader.php'                      => 'src_loader.template',
        'src/app/actions/ActionHandler.php'   => 'app_actions_actionhandler.template',
        'src/app/boilerplate/html_footer.php' => 'app_boilerplate_hmlfooter.template',
        'src/app/boilerplate/HTMLHeader.php'  => 'app_boilerplate_htmlheader.template',
        'src/includes/meta.php'               => 'includes_meta.template',
        'src/includes/meta-files.php'         => 'includes_metafiles.template',
        'src/includes/meta-paths.php'         => 'includes_metapaths.template',
        'src/includes/meta-tables.php'        => 'includes_metatables.template',
        'src/includes/static-routes.php'      => 'includes_staticroutes.template',
        'src/app/web/Home.php'                => '',
        'src/css/styles.css'                  => '',
        'src/js/lib.js'                       => '',
        'src/js/home/home.js'                 => '',
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
        $this->working_dir   = realpath( __DIR__ . '/../../../../' );
        $this->template_path = realpath( __DIR__ . '/Init' );
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
        $this->data['author'] = readline( "Enter you name and email, which will be used to attribute files to you. Example: Joe Soap <joe@soapweb.net>: " );
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
        $this->data['app-version'] = $this->data['version'];
        if ( $this->data['status'] !== '' ) {
            $this->data['app-version'] .= '-' . $this->data['status'];
        }
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
        foreach ( self::FILE_STRUCTURE as $file => $template ) {
            $new_file = $this->working_dir . '/' . $file;
            touch( $new_file );
            $write = fopen( $new_file, 'w' );
            fwrite( $write, $this->prepare_template( $template ) );
            fclose( $write );
            echo ".";
        }
        echo " Done";
        $this->lb();
    }


    /**
     * Prepare the template file for inserting
     * 
     * @param   string  $template   The relative path to the template file for meta.php.
     * 
     * @return  string
     * 
     * @access  private
     * @since   LBF 0.3.0-beta
     */

    private function prepare_template( string $template ): string {
        if ( $template == '' || !file_exists( $this->template_path . '/' . $template ) ) {
            return '';
        }
        $data = file_get_contents( $this->template_path . '/' . $template );
        $data = str_replace( '&START_DATE&',     date( 'Y-m-d' ),            $data );
        $data = str_replace( '&AUTHOR&',         $this->data['author'],      $data );
        $data = str_replace( '&VERSION&',        $this->data['app-version'], $data );
        $data = str_replace( '&APP_NAME&',       $this->data['app_name'],    $data );
        $data = str_replace( '&DESCRIPTION&',    $this->data['description'], $data );
        $data = str_replace( '&VERSION_NUMBER&', $this->data['version'],     $data );
        $data = str_replace( '&VERSION_STATUS&', $this->data['status'],      $data );
        return $data;
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
        $key_len = $val_len = 0;
        foreach ( $this->data as $key => $value ) {
            if ( strlen( $key ) > $key_len ) {
                $key_len = strlen( $key );
            }
            if ( strlen( $value ) > $val_len ) {
                $val_len = strlen( $value );
            }
        }
        $line_len = $key_len + $val_len + 7;
        $single = $double = '';
        for ( $i = 0; $i < $line_len; $i++ ) {
            $single .= '-';
            $double .= '=';
        }
        $single .= "\n";
        $double .= "\n";
        echo $single;
        $cell = function ( $str, $total ): string {
            $text = $str;
            $len = $total - strlen( $str );
            for ( $i = 0; $i < $len; $i++ ) {
                $text .= ' ';
            }
            return $text;
        };
        echo "| {$cell( 'Key', $key_len )} | {$cell( 'Value', $val_len )} |\n";
        echo $double;
        foreach ( $this->data as $key => $value ) {
            echo "| {$cell( $key, $key_len )} | {$cell( $value, $val_len )} |\n";
        }
        echo $double;
        $this->lb( 2 );
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