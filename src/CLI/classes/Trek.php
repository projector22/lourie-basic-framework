<?php

use App\Db\Data\TrekData;

/**
 * Trek out or back the database. Allowing the user to move the database from far behind update and roll back if need be.
 * 
 * Current options
 * - 
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @since   3.14.0
 */

class Trek {

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

        $search = isset( $arguments[2] ) ? $arguments[2] : 'none';
        switch ( $search ) {
            case 'create':
                $this->create( isset( $arguments[3] ) ? $arguments[3] : null );
                break;
            case 'out':
                $this->out();
                break;
            case 'back':
                $this->back( isset( $arguments[3] ) ? $arguments[3] : null );
                break;
            default:
                echo "No action chosen"; // call help maybe?
        }
    }


    /**
     * Create a new trek to the current version
     * 
     * @param   string|null     $version    Which verion to create a trek for
     * 
     * @access  private
     * @since   3.14.0
     */

    private function create( ?string $version ): void {
        if ( ENVIRONMENT == MODE_PRODUCTION ) {
            echo "You cannot create a new trek on a server in production or in 'live' mode";
            return;
        }

        if ( is_null( $version ) ) {
            $version = PROJ_VERSION;
        }

        $timestamp = time();
        $file_name = TREK_PATH . 'trek_' . $timestamp . '.php';
        
        echo "Creating file: {$file_name}... ";

        $template = "<?php

namespace App\Treks;

use LBS\Trek\Oxen;
use LBS\Trek\Wagon;

/**
 * This is the post update changes for version {$version}
 * 
 * This is an automatically generated file and you should only change the contents of the `out()` method and `back()` method.
 * 
 * IMPORTANT: If anything new is being defined in meta.php or CONFIG_FILE or TABLE_FILE and needs to be used here... it also needs to be defined in this method
 * 
 * Tools in the Oxen class which are available are
 * - ConnectMySQL methods
 * - define_new_table( \$table_const, \$table_name )
 * - add_element_to_config( \$search_str, \$new_config_paste )
 * 
 * Both out() and back() are required, but you may create private methods and properties to assist in the executing of your code.
 * 
 * use App\Treks\\trek_{$timestamp};
 * 
 * @version   $version
 */

class trek_{$timestamp} extends Oxen implements Wagon {

    /**
     * Do not edit or delete this property
     * 
     * @var string  \$version
     * 
     * @access  public
     * @since   $version
     */

    public string \$version = '$version';

    /**
     * For debugging, show feedback to the user on the CLI if set to true.
     * 
     * @var boolean \$debug = false
     * 
     * @access  public
     * @since   $version
     */

    public bool \$debug = false;

    /**
     * Any post update changes to be made to the database and codebase
     * These are usually database changes, but might also be used for cleaning up the bin folder, for example
     * 
     * @access  public
     * @since   $version
     */

    public function out() {
        echo \"Implimenting changes for $version...\";
        // Your post update code goes here
    }


    /**
     * As far as possible roll back any changes made in out.
     * Any changes that cannot be rolled back should have a comment indicating this.
     * 
     * @access  public
     * @since   $version
     */

    public function back() {
        echo \"Rolling back changes from $version...\";
        // Your post update roll back code goes here
    }
}";

        $file = fopen( $file_name, "w" ) or die("Unable to open file {$file_name}");
        fwrite( $file, $template );
        fclose( $file );

        echo Draw::tabs( 1 ) . "Done";
        Draw::lines( 2 );
        echo "You can view the file here: " . Draw::tabs( 1 ) . $file_name;
    }


    /**
     * Perform a command line trek out command. Run post update instructions of any treks not yet implimented
     * 
     * @access  private
     * @since   3.14.0
     */

    private function out(): void {
        echo "Checking for unimplemented treks...";

        spl_autoload_unregister( 'load_cli_class' );
        spl_autoload_register( 'load_class' );

        $trek = new TrekData( true );
        $required_updates = $trek->required_treks();
        echo Draw::tabs( 1 ) . count( $required_updates ) . ' found';
        if ( count( $required_updates ) > 0 ) {
            Draw::lines( 2 );
            echo "Hooking up the ox wagons...";
            Draw::lines( 1 );
            foreach ( $required_updates as $path => $trek_name ) {
                echo "Implimenting trek: {$trek_name}... ";
                $class = "App\\Treks\\{$trek_name}";
                $update = new $class;
                if ( isset( $update->debug ) && $update->debug ) {
                    ob_start();
                }
                $version = $update->version;
                $update->out();
                $trek->insert( [
                    'version_id' => $version,
                    'trek_name'  => $trek_name,
                ] );
                if ( isset( $update->debug ) && $update->debug ) {
                    ob_clean();
                }
                echo Draw::tabs( 1 ) . 'Success';
                Draw::lines( 1 );
            }
            echo "All treks implemented successfully";
        }

        spl_autoload_unregister( 'load_class' );
        spl_autoload_register( 'load_cli_class' );

        Draw::lines( 2 );
        echo "Task complete";
    }


    /**
     * Perform a command line trek back command. Run post update in reverse to clear previous changes as far as possible.
     * 
     * @param   string  $instruction    What type of rollback to impliment.
     * 
     * @access  private
     * @since   3.14.0
     */

    private function back( string $instruction ): void {
        if ( substr_count( $instruction, '.' ) == 2 ) {
            $this->back_version( $instruction ); 
        } else if ( is_numeric( $instruction ) && substr_count( $instruction, '.' ) == 0 ) {
            $this->back_number( $instruction );
        } else if ( is_null( $instruction ) ) { 
            $this->back_all();
        } else {
            echo "'{$instruction}' is not a valid input";
        }
    }


    /**
     * Trek back all possible steps
     * 
     * @access  private
     * @since   3.14.0
     */

    private function back_all(): void {
        echo "Treking back to the beginning";
        if ( !$this->check_if_sure() ) {
            return;
        }
        Draw::lines( 2 );
        spl_autoload_unregister( 'load_cli_class' );
        spl_autoload_register( 'load_class' );
        $trek = new TrekData;
        $trek->select_all( '', 'apply_time, trek_name DESC' );
        foreach ( $trek->data as $entry ) {
            $class = "App\\Treks\\{$entry->trek_name}";
            $update = new $class;
            $update->back();
            $trek->delete( ['trek_name' => $entry->trek_name] );
            echo Draw::tabs( 1 ) . "Success";
            Draw::lines( 1 );
        }
        echo "All treks rolled back successfully";
        spl_autoload_unregister( 'load_class' );
        spl_autoload_register( 'load_cli_class' );
        Draw::lines( 2 );
        echo "Task complete";
    }


    /**
     * Trek back a number of steps
     * 
     * @param   integer     $num_of_steps   The number of steps to go back
     * 
     * @access  private
     * @since   3.14.0
     */

    private function back_number( int $num_of_steps ): void {
        echo "Treking back {$num_of_steps} change" . ( $num_of_steps != 1 ? 's' : '' );

        spl_autoload_unregister( 'load_cli_class' );
        spl_autoload_register( 'load_class' );

        $trek = new TrekData( true );
        if ( $trek->number_of_records < $num_of_steps ) {
            Draw::lines( 2 );
            echo "Error, there are less treks than you are trying to get remove";
            Draw::lines( 1 );
            echo "To remove all treks, use the following command:" . Draw::tabs( 2 ) . "php lrs trek back";
            return;
        }
        if ( !$this->check_if_sure() ) {
            return;
        }

        Draw::lines( 2 );
        $trek->select_all( '', 'trek_name DESC', $num_of_steps );
        foreach ( $trek->data as $entry ) {
            $class = "App\\Treks\\{$entry->trek_name}";
            $update = new $class;
            $update->back();
            $trek->delete( ['trek_name' => $entry->trek_name] );
            echo Draw::tabs( 1 ) . "Success";
            Draw::lines( 1 );
        }
        echo "{$num_of_steps} trek" . ( $num_of_steps != 1 ? 's' : '' ) . " rolled back successfully";
        spl_autoload_unregister( 'load_class' );
        spl_autoload_register( 'load_cli_class' );
        Draw::lines( 2 );
        echo "Task complete";
    }


    /**
     * Trek back to a certain version
     * 
     * @param   string  $version    The version to go back to
     * 
     * @access  private
     * @since   3.14.0
     */

    private function back_version( string $version ): void {
        echo "Treking back to {$version}";
        if ( !$this->check_if_sure() ) {
            return;
        }
        Draw::lines( 1 );
        spl_autoload_unregister( 'load_cli_class' );
        spl_autoload_register( 'load_class' );

        $trek = new TrekData;
        $trek->select_all( '', 'apply_time, trek_name DESC' );
        $i = 0;
        foreach ( $trek->data as $entry ) {
            if ( $entry == $version ) {
                break;
            }
            $class = "App\\Treks\\{$entry->trek_name}";
            $update = new $class;
            if ( !$this->version_is_above( $update->version, $version ) ) {
                break;
            }
            $update->back();
            $trek->delete( ['trek_name' => $entry->trek_name] );
            echo Draw::tabs( 1 ) . "Success";
            Draw::lines( 1 );
            $i++;
        }
        echo "{$i} trek" . ( $i != 1 ? 's' : '' ) . " rolled back successfully";
        spl_autoload_unregister( 'load_class' );
        spl_autoload_register( 'load_cli_class' );
        Draw::lines( 2 );
        echo "Task complete";
    }


    /**
     * Check if the user is sure
     * 
     * @return  boolean
     * 
     * @access  public
     * @since   3.14.0
     */

    private function check_if_sure(): bool {
        while ( true ) {
            Draw::lines( 2 );
            echo "Are you sure you wish to roll back these changes?";
            Draw::lines( 1 );
            echo '[yes,no]' . Draw::tabs( 1 );
            $handle   = fopen( "php://stdin", "r" );
            $response = trim( fgets( $handle ) );
            if ( $response == 'yes' ) {
                return true;
            } else if ( $response == 'no' ) {
                Draw::lines( 1 );
                echo "Cancelled";
                return false;
            } else {
                echo "Invalid input";
            }
        }
    }


    /**
     * Get if the current version is the same, or above or below the current version
     * 
     * @param   string  $version        The version to be tested
     * @param   string  $test_version   The version to be compared against, default: PROJ_VERSION
     * 
     * @return  boolean|null    Null means the same, true is $version > $test_version, false is $version < $test_version
     * 
     * @access  private
     * @since   3.14.0
     */

    private function version_is_above( string $version, string $test_version = PROJ_VERSION ): ?bool {
        if ( $version == $test_version ) {
            return null;
        }

        $current = explode( '.', $test_version );
        $main    = $current[0];
        $feature = isset( $current[1] ) ? $current[1] : 0;
        $bugfix  = isset( $current[2] ) ? $current[2] : 0;

        $test = explode( '.', $version );
        $m = $test[0];
        $f = isset( $test[1] ) ? $test[1] : 0;
        $b = isset( $test[2] ) ? $test[2] : 0;

        if ( $m > $main ) {
            return true;
        } else if ( $m < $main ) {
            return false;
        }

        if ( $f > $feature )  {
            return true;
        } else if ( $f < $feature ) {
            return false;
        }

        if ( $b > $bugfix )  {
            return true;
        } else if ( $b < $bugfix ) {
            return false;
        }
        return null;
    }

}