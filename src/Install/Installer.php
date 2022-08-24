<?php

namespace Framework\Install;

use \PDO;
use App\Actions\PostUpdateActions;
use App\Config\JSON;
use App\Db\DatabaseDefaults;
use App\Structure\Footer;
use App\Structure\HTMLHeader as Header;
use Framework\HTML\Draw;

/**
 * This class is called at the end of pages to show the footer section and to close off the body and html tags properly
 * 
 * use Framework\Install\Installer;
 * 
 * @property    string      $environment
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @since   3.12.1
 */

class Installer {

    /**
     * @var string  $environment    The environment of the server: Windows / Unix like (Linux, MacOS etc.)
     * 
     * @access  private
     * @since   3.12.1
     */

    private $environment = PHP_OS === 'WINNT' ? 'windows' : 'unix';


    /**
     * Draw out the page selector for the install routine
     * 
     * @access  public
     * @since   3.15.0
     */

    public function draw() {
        // check if the config file exists. Shouldn't be possible if this script is executed but you never know
        if ( file_exists( CONFIG_FILE ) ) {
            $config_file = file( CONFIG_FILE );
            // include ( CONFIG_FILE );
            header( "Location: ../home" );
        }

        switch ( get_token() ) {
            case 'runinstall':
                $this->perform_first_run_install();
                break;
            default:
                Header::$install = true;
                Header::draw();
                echo "<div class='install_container'>";

                $this->first_run_installer_form();

                Footer::draw();
                echo "</div>"; // install_container
                die;
        }
    }


    /**
     * Draw out the form to allow the user to set values in order to operate the app
     * 
     * @access  public
     * @since   3.12.1
     */

    public function first_run_installer_form() {

        /**
         *  Generates a fixed length 3 character string to be offered to the user as a table prefix
         * 
         * @param   int     $len    Number of characters returned, default: 5
         * 
         * @return  string  A string the length defined by $len
         * 
         * @since   3.1.0
         * @since   3.12.1  Moved to Framework\Install\Installer
         */

        function generate_random_prefix( $len = 5 ) {
            $i = 0;
            $string = md5( time() + $i );
            while ( is_numeric( $string[0] ) ) {
                $i++;
                $string = md5( time() + $i );
            }
            return substr( $string, 0, $len );
        }

        /**
         * Place a table Row and input cells on the install page
         * 
         * @param   string  $title  The line item title
         * @param   string  $type   The input type
         * @param   string  $name   The input name
         * @param   string  $value  The input default value     Default: ''
         * 
         * @since   3.1.0
         * @since   3.12.1  Moved to Framework\Install\Installer
         */

        function install_cell( $title, $type, $name, $value = '' ) {
            echo "<tr>";
            echo "<td>$title</td>";
            echo "<td><input type='$type' name='$name' value='$value'></td>";
            echo "</tr>";    
        }

        echo "<main>";
        echo "<h1 class='install_header text_align_center'>" . PROGRAM_NAME . "</h1>";
        Draw::lines( 1 );
        echo "<h2 class='install_subheader text_align_center'>First Run Setup</h2>";
        echo "<p class='text_align_center'><i>Please ensure you have correctly configured either virtualhosts or if you don't have access, .htaccess before proceeding.</i></p>";
        if ( $this->environment == 'unix' ) {
            if ( !is_dir( BIN_PATH ) ) {
                echo "<div class='install_warning text_align_center'>";
                echo "Before you can continue, please run and execute <span class='font_mono'><b>php lrs init</b></span> from the terminal, then refresh the page.";
                echo "</div>";
                echo "</main>";
                return;
            }
        }
        echo "<form action='src/install.php' method='post'>";
        echo "<table class='install_table'>";
        set_token( 'runinstall' );
        install_cell( 'School Name:',           'text',     'sch_name' );
        install_cell( 'Database User:',         'text',     'db_user' );
        install_cell( 'Database Password:',     'text',     'db_pass' );
        install_cell( 'Table Prefix:',          'text',     'tbl_prefix',    generate_random_prefix() );
        install_cell( 'Database Name:',         'text',     'db_name',       'school_reg' );
        install_cell( 'Server IP/ Address:',    'text',     'server_addr',   '127.0.0.1' );
        install_cell( 'Number of Periods:',     'number',   'num_periods' );
        install_cell( 'Administrator Username', 'text',     'admin_user',    'admin' );
        install_cell( 'Administrator Password', 'password', 'admin_password' );
        install_cell( '',                       'submit',   'params_submit', 'Submit' );
        echo "</table>";
        echo "</form>";
        echo "</main>";
    }


    /**
     * Execute all the elements of the installation process
     * 
     * This includes:
     * - Creating the bin folder and subfolders.
     * - Writing the config.php file which contains the required defined constants to operate.
     * - Creating the database and tables, and set all default entries.
     * - Create a number of template files and JSON files used by the app.
     * 
     * @access  public
     * @since   3.12.1
     */

    public function perform_first_run_install() {
        $post_data  = protect( $_POST );
        $tbl_prefix = $post_data['tbl_prefix']  == '' ? ''           : $post_data['tbl_prefix'] . '_';
        $server_ip  = $post_data['server_addr'] == '' ? '127.0.0.1'  : $post_data['server_addr'];
        $database_name = $post_data['db_name']  == '' ? 'school_reg' : str_replace( ' ', '_', $post_data['db_name'] );
        $year = date( 'Y' );

        /**
         * Generate the bin folder structure, structure
         * 
         * @since   3.12.1
         */

        $result = null;
        switch ( $this->environment ) {
            case 'windows':
                $php_path = php_executable_path();
                if ( !$php_path ) {
                    echo 'The system cannot file where to find the php.exe file, please add it to PATH';
                    die;   
                }

                exec( "{$php_path} lrs init", $result );
                foreach ( $result as $line ) {
                    if ( str_contains( $line, 'Folder creation failed' ) ) {
                        echo "Could not create one of the required folders";
                        return;
                    }
                    if ( !is_dir( BIN_PATH ) ) {
                        echo '<i>' . BIN_PATH . '</i> does not exist. Please run <b>' . $php_path . ' init</b> from the root folder of the install and try the install again.';
                        die;
                    }
                }
                break;
            case 'unix':
                if ( !is_dir( BIN_PATH ) ) {
                    echo '<i>' . BIN_PATH . '</i> does not exist. Please run <b>php lrs init</b> from the root folder of the install and try the install again.';
                    die;
                }
                break;
        }

        // Generate and write the config file
        self::generate_config_file( $tbl_prefix, $post_data['sch_name'], $year, $server_ip, $post_data['db_user'], $post_data['db_pass'], $database_name  );

        // Load the config
        require_once CONFIG_FILE;
            
        /**
         * Create the database and create the tables
         * 
         * @since   3.12.1
         */

        try {
            $conn = new PDO( "mysql:host={$server_ip}", $post_data['db_user'], $post_data['db_pass'] );
            $conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
            $conn->exec( "CREATE DATABASE IF NOT EXISTS " . $tbl_prefix . $database_name . $year );
        } catch( \PDOException $e ) {
            switch ( $e->getCode() ) {
                case 1045;
                    echo "Error: One of your database details was incorrect, please try again";
                    break;
                case 2002:
                    echo "Error: No server can be found at address <i>{$server_ip}</i> or the server cannot connect";
                    break;
                default:
                    echo "An error has occured<br>";
                    echo $e->getMessage();
            }

            unset( $token );
            Draw::lines( 2 );
            echo "<input type='submit' value='Back'>";
            return;
        }

        // Disabled at the moment
        // $this->check_htaccess();

        // Create database tables and insert default data into database
        $database = new DatabaseDefaults;
        $database->create_structure();
        
        // Insert data into JSON
        JSON::set_default_json_data();
        JSON::write_json_file( UPLOAD_HEADINGS,    JSON::$headings_json );
        JSON::write_json_file( UPDATE_CREDENTIALS, JSON::$update_credentials_json );
        JSON::write_json_file( SCHOOL_CONFIG_INFO, [
            'basic_info' => [
                'school_name' => $post_data['sch_name']
            ],
            'positional_info' => [],
            'grades_offered'  => [],
            'weekdays_taught' => [],
            'academic_year'   => [
                'number_of_terms' => 4,
            ],
        ] );

        $update = new PostUpdateActions;
        $update->file_folder_maintenance();
        $update->composer_update();
        $update = null;

        // Load the home page / login page
        header( "Location: ../home" );
    }


    /**
     * Check and update the .htaccess file if needed
     * 
     * @access  public
     * @since   3.12.1
     * 
     * @deprecated  3.15.4
     */

    private function check_htaccess() {
        // Create .htaccess file
        $htaccess = '';
        if ( file_exists( HOME_PATH . '.htaccess' ) ) {
                $file_content = fopen( HOME_PATH . '.htaccess', 'r' );
                while ( !feof( $file_content ) ) {
                        $line = fgets( $file_content );
                        if ( str_contains( $line, 'ErrorDocument 404' ) ) {
                                $htaccess .= "ErrorDocument 404 " . INSTALL_FOLDER . "404.php\n";
                            } else {
                                    $htaccess .= $line;
                                }
                            }
                            fclose( $file_content );
            } else {
                $htaccess .= "ErrorDocument 404 " . INSTALL_FOLDER . "404.php";
            }

        // Seems to be broken on LINUX - file permissions
        if ( $file = fopen( HOME_PATH . '.htaccess', 'w' ) ) {
            fwrite( $file, $htaccess );
            fclose( $file );
        }
    }


    /**
     * generate the full config file string to be written to the database, with the relevant parameters as values
     * 
     * @param   string  $tbl_prefix     The defined table prefex
     * @param   string  $school_name    The defined school name
     * @param   string  $year           The year the server is spun up
     * @param   string  server_ip       The ip address of the server
     * @param   string  $db_user        The database username to connect to the database
     * @param   string  $db_pass        The database password to connect to the database
     * @param   string  $database_name  The name of the database to be connected to
     * @param   string  $cookie_hash    The defined cookie hash - leaving off will cause a new hash to be generated
     *                                  Default: null
     * @param   string  $session_hash   The defined session hash - leaving off will cause a new hash to be generated
     *                                  Default: null
     * 
     * @access  public
     * @since   3.12.1
     */

    public static function generate_config_file( $tbl_prefix, $school_name, $year, $server_ip, $db_user, $db_pass, $database_name, $cookie_hash = null, $session_hash = null ) {
        // Set App Hashes
        $cookie_hash  = is_null( $cookie_hash ) ? md5( date( 'Y-m-d G:i:s' ) . time() . $tbl_prefix . $database_name . $school_name ) : $cookie_hash;
        $session_hash = is_null( $session_hash ) ? md5( date( 'Y-m-d G:i:s' ) . time() . $tbl_prefix . $database_name ) : $session_hash;

        $config = "<?php
/** Configuration **/

\$environment = MODE_PRODUCTION;
// \$environment = MODE_MAINTENANCE;
// \$environment = MODE_DEVELOPEMENT;

if ( isset ( \$_SERVER['HTTP_HOST'] ) ) { // Required to escape in command line situations
    if ( \$_SERVER['HTTP_HOST'] == 'localhost' || \$_SERVER['HTTP_HOST'] == '127.0.0.1' ) {
        \$environment = 'dev';
    }
}

// Define the environment of this install
define ( 'ENVIRONMENT', \$environment );

// The timezone of the install
date_default_timezone_set( 'Africa/Johannesburg' );

// The table prefex
define( 'TBL_PFX', '{$tbl_prefix}' );

// Starting Year
define( 'START_YEAR', '{$year}' );

// Server IP or URL
define( 'DB_LOC', '{$server_ip}' );
        
// Database User Name
define( 'DB_USER', '{$db_user}' );

// Database User Password
define( 'DB_PASS', '{$db_pass}' );

// Database name
define( 'DB_NAME', TBL_PFX . '{$database_name}' );

// Database year
define( 'DB_YEAR', '{$year}' );

// Set cookie authentication variable - change this to log all users out.
define ( 'COOKIE_HASH', '{$cookie_hash}' );
define ( 'SESSION_HASH', '{$session_hash}' );

// Load all the table constants
require INCLUDES_PATH . 'tables.php';";

        // write data to file
        $file = fopen( CONFIG_FILE, 'w' ) or die( "Unable to write config.php file" );
        fwrite( $file, $config );            
        fclose( $file );
    }

}