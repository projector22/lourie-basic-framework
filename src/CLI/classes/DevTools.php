<?php

use App\Db\DatabaseDefaults;
use App\Db\DatabaseStructure;
use Framework\Db\ConnectMySQL;

/**
 * Tools for executing various Development tools
 * 
 * Current options
 * - 
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @since   3.14.0
 */


class DevTools {

    /**
     * Constructor method, things to do when the class is loaded
     * 
     * @param   array   $arguments  Arguments passed to the CLI
     * 
     * @access  public
     * @since   3.14.0
     */

    public function __construct( ?array $arguments ) {
        if ( is_file( CONFIG_FILE ) ) {
            require CONFIG_FILE;
        } else {
            echo "App is not yet installed, please install first through the web interface first.";
            return;
        }

        if ( ENVIRONMENT == MODE_PRODUCTION ) {
            echo "You cannot run Dev Tools on a server in production or in 'live' mode";
            return;
        }

        $search = isset ( $arguments[2] ) ? $arguments[2] : 'none';
        switch ( $search ) {
            case 'uninstall':
                $this->perform_uninstall();
                break;
            case 'clear-data':
                $this->clear_data();
                break;
            case 'config-reset':
                $this->reset_data();
                break;
            default:
                echo "No dev tool set";
        }
    }


    /**
     * Uninstall the app install by dropping the database and deleting the CONFIG_FILE
     * 
     * @since   3.14.0
     */

    public function perform_uninstall(): void {
        echo "Uninstall the server...";
        while ( true ) {
            Draw::lines( 2 );
            echo "Are you sure you wish to uninstall the server? This only removes the database and config files and not the entire Application files. This is a permenant change that cannot be reversed!";
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
        }
        Draw::lines( 1 );
        echo "Starting...";
        Draw::lines( 2 );

        spl_autoload_unregister( 'load_cli_class' );
        spl_autoload_register( 'load_class' );

        echo "Dropping database... ";
        $db = new ConnectMySQL;

        if ( !$db->sql_execute( "DROP DATABASE " . DB_NAME . DB_YEAR ) ) {
            echo Draw::tabs( 1 ) . 'Failed';
            Draw::lines( 1 );
            echo 'Uninstall Stopped';
            return;
        }

        spl_autoload_unregister( 'load_class' );
        spl_autoload_register( 'load_cli_class' );

        echo Draw::tabs( 1 ) . "Success";
        Draw::lines( 1 );

        echo "Deleting config... ";
        if ( file_exists( CONFIG_FILE ) && unlink( CONFIG_FILE ) ) {
            echo Draw::tabs( 1 ) . "Success";
        } else {
            echo "Config file already removed";
        }
        Draw::lines( 2 );
        echo "Uninstall completed successfully";
    }


    /**
     * Hard reset data from the database without clearing any configuration
     * 
     * @since   3.14.0
     */

    public function clear_data(): void {
        echo "Clearing all data from the server without clearing configurations...";
        while ( true ) {
            Draw::lines( 2 );
            echo "Are you sure you wish to clear all data from the server? This is a permenant change that cannot be reversed!";
            Draw::lines( 1 );
            echo "IMPORTANT - this includes user accounts. You will be asked to create a new administrator username and password.";
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
        }
        Draw::lines( 1 );
        echo "Starting...";
        Draw::lines( 2 );    

        spl_autoload_unregister( 'load_cli_class' );
        spl_autoload_register( 'load_class' );

        echo "Clearing configs... ";
        Draw::lines( 2 );
        $db = new DatabaseStructure;

        $i = 1;
        $count = count( $db->truncated_table );
        foreach ( $db->truncated_table as $table ) {
            echo "Emptying table {$i} of {$count}...";
            $db->sql_execute( "TRUNCATE TABLE {$table}" );
            echo Draw::tabs( 1 ) . "Done";
            Draw::lines( 1 );
            $i++;
        }

        spl_autoload_unregister( 'load_class' );
        spl_autoload_register( 'load_cli_class' );

        Draw::lines( 2 );
        echo "All cleared data successfully";
    }


    /**
     * Reset all configuration data to factory / default settings
     * 
     * @access  public
     * @since   3.14.0
     */

    public function reset_data(): void {
        echo "Resetting to factory configuration settings...";
        while ( true ) {
            Draw::lines( 2 );
            echo "Are you sure you wish to clear all configs from the server? This is a permenant change that cannot be reversed!";
            Draw::lines( 1 );
            echo "IMPORTANT - this includes user accounts. You will be asked to create a new administrator username and password.";
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
        }
        Draw::lines( 1 );
        echo "Starting...";
        Draw::lines( 2 );

        spl_autoload_unregister( 'load_cli_class' );
        spl_autoload_register( 'load_class' );

        echo "Clearing configs... ";
        Draw::lines( 2 );
        $db = new ConnectMySQL;
        echo "Clearing User Accounts...";
        $db->sql_execute( "TRUNCATE TABLE " . USER_ACCOUNTS );
        echo Draw::tabs( 1 ) . "Done";
        Draw::lines( 1 );
        echo "Clearing Account Permissions...";
        $db->sql_execute( "TRUNCATE TABLE " . ACCOUNTS_PERMISSIONS );
        echo Draw::tabs( 1 ) . "Done";
        Draw::lines( 1 );
        echo "Clearing User Groups...";
        $db->sql_execute( "TRUNCATE TABLE " . USER_GROUPS );
        echo Draw::tabs( 2 ) . "Done";
        Draw::lines( 1 );
        echo "Clearing General Config...";
        $db->sql_execute( "TRUNCATE TABLE " . GENERAL_CONFIG );
        echo Draw::tabs( 1 ) . "Done";
        Draw::lines( 1 );
        echo "Clearing Mail Config...";
        $db->sql_execute( "TRUNCATE TABLE " . MAIL_CONFIG );
        echo Draw::tabs( 2 ) . "Done";
        Draw::lines( 1 );
        echo "Clearing Discipline Config...";
        $db->sql_execute( "TRUNCATE TABLE " . DISCIPLINE_CUSTOM_CONFIG );
        echo Draw::tabs( 1 ) . "Done";
        Draw::lines( 2 );

        $handle   = fopen( "php://stdin", "r" );

        echo "Please enter a new default admin username:" . Draw::tabs( 1 );
        $username = trim( fgets( $handle ) );

        Draw::lines( 1 );
        echo "Please enter a new default admin password:" . Draw::tabs( 1 );
        $password = trim( fgets( $handle ) );

        Draw::lines( 1 );
        echo "Please enter a new default number of periods:" . Draw::tabs( 1 );
        $num_of_periods = trim( fgets( $handle ) );

        Draw::lines( 2 );

        echo "Setting default data...";

        Draw::lines( 2 );
        $defaults = new DatabaseDefaults;
        $defaults->create_structure_cli( $username, $password, $num_of_periods );

        spl_autoload_unregister( 'load_class' );
        spl_autoload_register( 'load_cli_class' );

        Draw::lines( 2 );
        echo "All configuration set successfully";
        Draw::lines( 2 );
        echo " completed successfully";
    }

}