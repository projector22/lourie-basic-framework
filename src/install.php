<?php

/**
 * 
 * Script to perform a clean install of the app, and to display the install form as needed
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
            $db_name =       DatabaseControl::protect( $_POST['db_name'] );
            $db_user =       DatabaseControl::protect( $_POST['db_user'] );
            $db_pass =       DatabaseControl::protect( $_POST['db_pass'] );
            $site_admin =    DatabaseControl::protect( $_POST['site_admin'] );
            $site_password = password_hash(  $_POST['site_password'], PASSWORD_DEFAULT );
        }
        
        if ( $_POST['db_loc'] == '' ){
            $db_loc = '127.0.0.1';
        } else{
            $db_loc = DatabaseControl::protect( $_POST['db_loc'] );
        }
        
        if ( $_POST['db_tbl_pfx'] == '' ){
            $tbl_pfx = '';
        } else {
            $tbl_pfx = DatabaseControl::protect( $_POST['db_tbl_pfx'] ) . '_';
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

        //Set app hashes
        $session_hash = md5( date( 'Y-m-d G:i:s' ) . time() . $tbl_pfx . $db_name . $db_user );
        $cookie_hash = md5( date( 'Y-m-d G:i:s' ) . time() . $tbl_pfx . $db_name );

        //Create config.php file
        $config = "<?php

//Server Config
define( 'TBL_PFX', '$tbl_pfx' );
define( 'DB_NAME', TBL_PFX . '$db_name' );
define( 'DB_LOC', '$db_loc' );
define( 'DB_USER', '$db_user' );
define( 'DB_PASS', '$db_pass' );

//Site Tables
define( 'USER_ACCOUNTS', TBL_PFX . 'user_accounts' );
define( 'USER_GROUPS', TBL_PFX . 'user_groups' );
define( 'SESSION_LOGS', TBL_PFX . 'session_logs' );
define( 'LDAP_CONFIG', TBL_PFX . 'ldap_config' );

//Authentication Hashes
define( 'SESSION_HASH', '$session_hash' );
define( 'COOKIE_HASH', '$cookie_hash' );
";
        
        $file = fopen( INCLUDES_PATH . 'config.php', 'w' ) or die( "Unable to write config.php file" );
        fwrite( $file, $config );
        fclose( $file );
        require 'includes/config.php';

        //Create tables and insert default values
        $db_structure = new DatabaseStructure;
        
        $db_structure->create_tables();
        $db_structure->sql_execute( "INSERT INTO " . USER_ACCOUNTS . " (account_name, password) VALUES ('$site_admin', '$site_password')" );
        //Load the index page
        header( "Location: " . "index.php" );
        break;
    default:
        //The install page form
        echo "<div class='install_form_contain'>";
        echo "<div class='install_form_heading'>";
        echo "<img src='src/img/" . SITE_LOGO . "' alt='Logo Placeholder' width='200px'>";
        echo "<h1>Placeholder Text</h1>";
        echo "</div>";//install_form_heading
        echo "<div class='install_form_instructions'>";
        echo "Fill in the following categories below to begin installing your app";
        echo "</div>";//install_form_instructions
        echo "<div class='install_form_elements'>";
        echo "Database Name<input type='text' name='db_name'>";
        element_spacer_one();
        echo "Database username<input type='text' name='db_user'>";
        element_spacer_one();
        echo "Database password <input type='text' name='db_pass'>";
        element_spacer_one();
        echo "Database address/ IP* <input type='text' name='db_loc' placeholder='127.0.0.1'>";
        echo "<span></span><i>* Leave blank to set to 127.0.01 - recommended</i>";
        element_spacer_one();
        $rand = generate_random_prefix();
        echo "Desired table prefix** <input type='text' name='db_tbl_pfx' value='$rand'>";
        echo "<span></span><i>** You can leave this as randomly generated or set your own. 
        If you set it to blank, no table prefix will be set - not recommended</i>";
        element_spacer_one();
        echo "Admin username: <input type='text' name='site_admin'>";
        element_spacer_one();
        echo "Admin password <input type='password' name='site_password'>";
        element_spacer_one();
        echo "</div>";//install_form_elements

        token('run_install');
        lines(2);
        echo "<div class='install_form_submit'>";
        echo "<input type='submit' name='submit' class='submit_button_one' value='Begin Install'>";
        echo "</div>";//install_form_submit
        echo "</div>";//install_form_contain
        break;
}//switch $token
echo "</form>";


lines(5);
footer();
