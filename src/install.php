<?php

/**
 * 
 * Script to perform a clean install of the app, 
 * 
 * @author  Gareth  Palmer  @evangeltheology
 * 
 * @var     string  $token  The token set by a $_POST or $_GET if isset. Switched out for options
 * 
 * @since   0.1 Pre-alpha
 */

require_once 'includes/meta.php';
require_once 'functions.php';
require_once 'classes/DatabaseControl.php';
require_once 'classes/DatabaseStructure.php';

/**
 *  Generates a fixed length 3 character string to be offered to the user as a table prefix
 * 
 * @param   int     $len    Number of characters returned, default: 3
 * @return  string  $rand   A string the length defined by $len
 * 
 * @since   0.1 Pre-alpha
 */

function generate_random_prefix( $len=3 ){
    $key = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    $rand = substr( $key, rand( 0, strlen( $key ) ), $len );
    return $rand;
}

$token = setToken();

echo "<form method='post' action='index.php'>";

switch ( $token ){
    case 'run_install':
    /**
     * Checks if all the elements are correctly sent from $_POST then generates the database, 
     * its tables and the config.php
     */
        if ( $_POST['db_name'] == '' || 
             $_POST['db_user'] == '' || 
             $_POST['db_pass'] == '' || 
             $_POST['site_admin'] == '' ||
             $_POST['site_password'] == '' ){
            //if form element is missing     
            echo "One of the fields are missing!";
            unset( $token );
            lines(2);
            echo "<input type='submit' value='Back'>";
            footer();
            die;
        } else{
            $db_name = $_POST['db_name'];
            $db_user = $_POST['db_user'];
            $db_pass = $_POST['db_pass'];
            $site_admin = $_POST['site_admin'];
            $site_password = $_POST['site_password'];
        }
        if ( $_POST['db_loc'] == '' ){
            $db_loc = '127.0.0.1';
        } else{
            $db_loc = $_POST['db_loc'];
        }
        
        if ( $_POST['db_tbl_pfx'] == '' ){
            $tbl_pfx = '';
        } else {
            $tbl_pfx = $_POST['db_tbl_pfx'] . '_';
        }

        //Create table
        try {
            $conn = new PDO( "mysql:host=$db_loc", $db_user, $db_pass );
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $sql = "CREATE DATABASE IF NOT EXISTS " . $tbl_pfx . $db_name;
            $conn->exec( $sql );
        } catch( PDOException $e ) {
            if ( $e->getCode() == '1045' ){
                echo "One of your database details was incorrect, please try again";
            } else {
                $e->getMessage();
            }
            unset( $token );
            lines(2);
            echo "<input type='submit' value='Back'>";
            footer();
            die;
        }
        $conn = null;

        //Create config.php file
        $config = "<?php

define( 'TBL_PFX', '$tbl_pfx' );
define( 'DB_NAME', TBL_PFX . '$db_name' );
define( 'DB_LOC', '$db_loc' );
define( 'DB_USER', '$db_user' );
define( 'DB_PASS', '$db_pass' );

define( 'SITE_TABLES', array( 'user_accounts' => TBL_PFX . 'user_accounts' ) );";
        
        $file = fopen( INCLUDES_PATH . 'config.php', 'w' ) or die( "Unable to write config.php file" );
        fwrite( $file, $config );
        fclose( $file );
        require 'includes/config.php';

        //Create tables and insert default values
        $db_structure = new DatabaseStructure;
        
        $db_structure->create_tables();
        $db_structure->sql_execute( "INSERT INTO " . SITE_TABLES['user_accounts'] . " (user_name, password) VALUES ('$site_admin', '$site_password')" );

        //Load the index page
        header( "Location: " . "index.php" );
        break;
    default:
        //The install page form
        echo "Database Name <input type='text' name='db_name'>";
        lines(1);
        echo "Database username <input type='text' name='db_user'>";
        lines(1);
        echo "Database password <input type='text' name='db_pass'>";
        lines(1);
        echo "Database address/ IP <input type='text' name='db_loc'> <i>* Leave blank to set to 127.0.01 - recommended</i>";
        lines(1);
        $rand = generate_random_prefix();
        echo "Desired table prefix <input type='text' name='db_tbl_pfx' value='$rand'> <i>* You can leave this as randomly generated or set your own. If you set it to blank, no table prefix will be user - not recommended</i>";
        lines(1);
        echo "Admin Account username: <input type='text' name='site_admin'>";
        lines(1);
        echo "Admin Account password <input type='password' name='site_password'>";
        lines(2);
        token('run_install');
        echo "<input type='submit' name='submit' value='Begin Install'>";
        break;
}//switch $token
echo "</form>";


lines(5);
footer();
