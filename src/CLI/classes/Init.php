<?php

/**
 * Generate and run all the initialization tools get run first run steps
 * 
 * Current options
 * - Create bin folder and subfolders
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @since   3.14.0
 */

class Init {


    /**
     * The basic structure of the bin path
     * 
     * @access  public
     * @since   3.14.0
     */

    const BIN_PATH_STRUCTURE = [
        'backups/',
        'crons/' => [
            'bat/', 'php/', 'sh/'
        ],
        'json/',
        'logs/',
        'signatures/',
        'letterheads',
        'templates/',
        'uploads/',
        'photos/',
    ];


    /**
     * Constructor method, things to do when the class is loaded
     * 
     * @access  public
     * @since   3.14.0
     */

    public function __construct() {
        $echo_path = !realpath( BIN_PATH ) ? BIN_PATH : realpath( BIN_PATH );
        echo "Creating $echo_path ... ";
        if ( is_dir( BIN_PATH ) ) {
            echo realpath( BIN_PATH ) . " already exists\n";
        } else {
            if ( mkdir( BIN_PATH, 0755, true ) ) {
                exec( "find " . BIN_PATH . " -type d -exec chmod 0777 {} +" );
                echo "Success\n";
            } else {
                echo "Folder creation failed\n";
            }
        }
        
        $this->write_folder( self::BIN_PATH_STRUCTURE );
    }


    /**
     * Recursively write the required folders according to a parsed parameters
     * 
     * @param   array   $structure      Structure of files to be inserted
     * @param   string  $sub_folder     If a sub folder - the parent folder. Default: ''
     * 
     * @access  public
     * @since   3.14.0
     */

    private function write_folder( array $structure, string $sub_folder = '' ): void {
        foreach ( $structure as $index => $folder_name ) {
            if ( is_array( $folder_name ) ) {
                $path = BIN_PATH . $sub_folder . $index;
                $echo_path = !realpath( $path ) ? $path : realpath( $path );
                echo "Creating $echo_path ... ";
                if ( !is_dir( $path ) ) {
                    if ( mkdir( $path, 0755, true ) ) {
                        exec( "find {$path} -type d -exec chmod 0777 {} +" );
                        echo "Success\n";
                        $this->write_folder( $folder_name, $index );
                    } else {
                        echo "Folder creation failed\n";
                    }
                } else {
                    echo realpath( $path ) . " already exists\n";
                }
            } else {
                $path = BIN_PATH . $sub_folder . $folder_name;
                $echo_path = !realpath( $path ) ? $path : realpath( $path );
                echo "Creating $echo_path ... ";
                if ( !is_dir( $path ) ) {
                    if ( mkdir( $path, 0755, true ) ) {
                        exec( "find {$path} -type d -exec chmod 0777 {} +" );
                        echo "Success\n";
                    } else {
                        echo "Folder creation failed\n";
                    }
                } else {
                    echo realpath( $path ) . " already exists\n";
                }
            }
        }
    }

}