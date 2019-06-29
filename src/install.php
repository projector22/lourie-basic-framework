<?php

require_once 'includes/meta.php';
require_once 'functions.php';
require_once 'classes/DatabaseControl.php';
require_once 'classes/DatabaseStructure.php';

page_header();

$token = setToken();

function install_generate_random_prefix( $len=3 ){
    $charset = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
    $base = strlen( $charset );
    $result = '';

    $now = explode( ' ', microtime() )[1];
    while ( $now >= $base ){
        $i = $now % $base;
        $result = $charset[$i] . $result;
        $now /= $base;
    }
    return substr( $result, -$len );
}

echo "<form method='post' action='index.php'>";

switch ( $token ){
    case 'run_install':
        if ( $_POST['db_name'] == '' || 
             $_POST['db_user'] == '' || 
             $_POST['db_pass'] == '' || 
             $_POST['site_admin'] == '' ||
             $_POST['site_password'] == '' ){
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

        $link = @mysqli_connect( $db_loc, $db_user, $db_pass ) or die( "ERROR: Could not connect. " . mysqli_connect_error() );
        $sql = "CREATE DATABASE " . $tbl_pfx . $db_name;
        mysqli_query( $link, $sql ) or die( 'Database could not be created: ' . mysqli_error( $link ) );
        mysqli_close( $link );
        
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
        $db_structure = new DatabaseStructure;
        
        $db_structure->create_tables();
        $db_structure->sql_execute( "INSERT INTO " . SITE_TABLES['user_accounts'] . " (user_name, password) VALUES ('$site_admin', '$site_password')" );

        header( "Location: " . "index.php" );
        break;
    default:
        echo "Database Name <input type='text' name='db_name'>";
        lines(1);
        echo "Database username <input type='text' name='db_user'>";
        lines(1);
        echo "Database password <input type='text' name='db_pass'>";
        lines(1);
        echo "Database address/ IP <input type='text' name='db_loc'> <i>* Leave blank to set to 127.0.01 - recommended</i>";
        lines(1);
        $key = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        $rand = substr( $key, rand( 0, strlen( $key ) ), 3 );
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
