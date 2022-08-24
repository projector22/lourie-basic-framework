<?php

use App\Actions\UpdateActions;
use App\Actions\PostUpdateActions;

/**
 * Perform and update to the server.
 * 
 * Current options
 * - 
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @since   3.14.0
 */

class Update {

    /**
     * Constructor method, things to do when the class is loaded
     * 
     * @param   array   $arguments  Arguments parsed to the object.
     * 
     * @access  public
     * @since   3.14.0
     */

    public function __construct( array $arguments ) {
        if ( is_file( CONFIG_FILE ) ) {
            require CONFIG_FILE;
        } else {
            echo "App is not yet installed, please install first through the web interface first.";
            return;
        }

        if ( !isset( $arguments[2] ) ) {
            $help = new Help;
            $help->update();
            return;
        }

        switch ( $arguments[2] ) {
            case 'check':
                $this->check_for_update();
                break;
            case 'install':
                $this->install_update( isset( $arguments[3] ) ? $arguments[3] : null );
                break;
            default:
                $help = new Help;
                $help->update();
                return;
        }
    }


    /**
     * Perform the task of checking for an update
     * 
     * @access  private
     * @since   3.14.0
     */

    private function check_for_update(): void {
        spl_autoload_unregister( 'load_cli_class' );
        spl_autoload_register( 'load_class' );
        $update = new UpdateActions;

        ob_start();
        $check = $update->check_for_git_update();
        $response = ob_get_contents();
        ob_end_clean();

        if ( str_contains( $response, 'Git not installed') ) {
            echo "Git not installed - please install git on the server before updates will work";
            return;
        } else if ( str_contains( $response, "Authentication failed" ) ) {
            echo "Username or Access Token incorrect";
            return;
        } else if ( str_contains( $response, "fatal" ) ) {
            echo "A fatal error has occured";
            return;
        } else if ( $check ) {
            echo "An update is available";
        } else {
            echo "Everything is up to date";
        }

        spl_autoload_unregister( 'load_class' );
        spl_autoload_register( 'load_cli_class' );
    }


    /**
     * Install the update from the repository
     * 
     * @param   string|null A switch from the command. Pass -y to skip the "are you sure" dialogue
     * 
     * @access  private
     * @since   3.14.0
     */

    private function install_update( ?string $param ): void {
        spl_autoload_unregister( 'load_cli_class' );
        spl_autoload_register( 'load_class' );
        $update      = new UpdateActions;
        $post_update = new PostUpdateActions;

        if ( $param !== '-y' ) {
            while ( true ) {
                echo "This will check and perform updates as required? NOTE: THIS FEATURE IS VERY MUCH IN BETA. DO NOT USE IN PRODUCTION! USE AT OWN RISK!";
                Draw::lines( 1 );
                echo '[yes,no]' . Draw::tabs( 1 );
                $handle   = fopen( "php://stdin", "r" );
                $response = trim( fgets( $handle ) );
                if ( $response == 'yes' || $response == 'y' ) {
                    break;
                } else if ( $response == 'no' || $response == 'n' ) {
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

        $update->begin();
        $post_update->run_post_update();

        spl_autoload_unregister( 'load_class' );
        spl_autoload_register( 'load_cli_class' );
    }
}