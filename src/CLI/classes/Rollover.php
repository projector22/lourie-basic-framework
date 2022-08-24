<?php

use App\Actions\RolloverActions;
use LBS\HTML\Draw;
use LBS\Tools\JSON\JSONTools as JSON;

/**
 * Execute a database rollover from or to the next year.
 * 
 * ## Current options
 * - rollover
 * - rollover -y
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @since   3.14.0
 */

class Rollover {

    /**
     * Constructor method, things to do when the class is loaded
     * 
     * @param   string|null $choice     The switch parameter added to the command. Only '-y' will cause a reaction.
     * 
     * @access  public
     * @since   3.14.0
     */

    public function __construct( ?string $choice ) {
        if ( is_file( CONFIG_FILE ) ) {
            require CONFIG_FILE;
        } else {
            echo "App is not yet installed, please install first through the web interface first.";
            return;
        }

        spl_autoload_unregister( 'load_cli_class' );
        spl_autoload_register( 'load_class' );

        $st_name_l = is_file( APP_CONFIG ) ? strtolower( JSON::read_json_file_to_array( APP_CONFIG )['aliases']['student'] ) : 'student';

        $rollover = new RolloverActions;

        if ( $choice !== '-y' ) {
            while ( true ) {
                echo "This will do the rollover the app to {$rollover->year_to}. This proccess will shelve {$rollover->year_from} and make a number of changes to the database, including {$st_name_l} promotion. Do you wish to proceed?";
                Draw::lines( 1 );
                echo '[yes,no]' . Draw::tabs( 1 );
                $handle   = fopen( "php://stdin", "r" );
                $response = trim( fgets( $handle ) );
                if ( $response == 'yes' ) {
                    break;
                } else if ( $response == 'no' ) {
                    Draw::lines( 1 );
                    echo "Cancelled";
                    return;
                } else {
                    echo "Invalid input";
                }
                Draw::lines( 2 );
            }
            Draw::lines( 2 );
        }
        $rollover->begin();

        spl_autoload_unregister( 'load_class' );
        spl_autoload_register( 'load_cli_class' );
    }

}