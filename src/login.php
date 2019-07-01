<?php

//TO DO - Convert the LDAP logins to the LDAP Class when that is built
//TO DO - Test LDAP Logging

require_once 'includes/meta.php';
require_once 'includes/config.php';
require_once 'functions.php';
spl_autoload_register( 'load_class' );

$permit     = new SitePermissions;
$db_control = new DatabaseControl;
$login      = new Login;

$token = setToken();

$result = $db_control->sql_select( "SELECT * FROM " . LDAP_CONFIG );
if ( count( $result ) > 0){
    $ldap_enabled = $result[0]['ldap_enabled'];
    $address      = $result[0]['address'];
    $port         = $result[0]['port'];
} else {
    $ldap_enabled = $address = $port = '';
}

//This if tests for a login session id cookie, and if it exists, performs the tests needed to authenticate the user.
if ( isset( $_COOKIE[$permit->cookie_session_var] ) ){
    $cookie_test = $_COOKIE[$permit->cookie_session_var];
    $username = explode( '|', $cookie_test )[0];
    $password_test = explode( '|', $cookie_test )[1];
    $result = $db_control->sql_select( "SELECT * FROM " . USER_ACCOUNTS . " WHERE account_name='$username'" );
    if ( count( $result ) > 0 ){
        if ( $row['ldap_user'] == 0 ){
            //Not LDAP
            $fragment = $login->password_substr( $row['password'] );
            $session_test = $login->generate_session_id( $username, $fragment );
            if ( $cookie_test == $session_test ){
                $login->set_session( $username, $row['account_permissions'], $fragment );
                header( "Location: index.php" );
                die;
            }// test string compare                    
        } else {
            //LDAP
            $fragment = $row['ldap_password_fragment'];
            $session_test = $login->generate_session_id( $username, $fragment );
            if ( $cookie_test == $session_test ){
                $login->set_session( $username, $row['account_permissions'], $fragment );
                header( "Location: index.php" );
                die;
            }// test string compare
        }//if or if not ldap
    }//if $result > 0
}//isset( $_COOKIE[$permit->cookie_session_var]

switch ( $token ){
    case 'logmein':
        if ( isset( $_POST['password'] ) ){

            //get values from login form
            $username = DatabaseControl::protect( $_POST['username'] );
            $password = DatabaseControl::protect( $_POST['password'] );

            $result = $db_control->sql_select( "SELECT * FROM " . USER_ACCOUNTS . " WHERE account_name='$username'" );
            if ( count( $result ) > 0 ){
                foreach ( $result as $row ){
                    $dn     = $row['ldap_dn'];
                    $status = $row['account_status'];
                    
                    //Check for disabled account
                    if ( $status != 'active' ){
                        header( "Location: ../index.php?token=disable-error" );
                        die;
                    }
                    
                    if ( $row['ldap_user'] == '1' ){
                        if ( $ldap_enabled == '0' ){
                            header( "Location: ../index.php?token=disable-error" );
                            die;
                        }//check if ldap is enabled
                        
                        $ldap_con = ldap_connect( $address, $port );
                        ldap_set_option( $ldap_con, LDAP_OPT_PROTOCOL_VERSION, 3 );
                        ldap_bind( $ldap_con, $dn, $password );
                        if ( ldap_bind( $ldap_con, $dn, $password ) ) {
                            $password_fragment = $login->password_substr( password_hash( $password, PASSWORD_DEFAULT ) );
                            $db_control->sql_execute( "UPDATE " . USER_ACCOUNTS . " SET ldap_password_fragment='$password_fragment' WHERE account_name='$username'" );
                            mysqli_query( $link, $sql );
                            $login->set_session( $username, $row['account_permissions'], $password_fragment );
                            header( "Location: ../index.php" );
                        } else {
                            header( "Location: ../index.php?token=pass-error" );
                        }
                    //if not ldap user
                    } else {
                        if ( password_verify( $password, $row['password'] ) ){
                            $password_fragment = $login->password_substr( $row['password'] );
                            $login->set_session( $username, $row['account_permissions'], $password_fragment );
                            header( "Location: ../index.php" );
                        } else {
                            header( "Location: ../index.php?token=pass-error" );
                        }//if
                    }//if check if ldap user or not
                }//foreach
            } else {
                header( "Location: ../index.php?token=user-error" );
                echo "No user by that name found";
                PageElements::back_button( HOME_PAGE );
            }
        }// if isset
        break;
    case 'pass-error':
        $login->login_heading();
        echo "<div class='login_error'>Password incorrect</div>";
        PageElements::back_button( HOME_PAGE );
        break;
    case 'user-error':
        $login->login_heading();
        echo "<div class='login_error'>No user by that name found</div>";
        PageElements::back_button( HOME_PAGE );
        break;
    case 'disable-error':
        $login->login_heading();
        echo "<div class='login_error'>User is disabled</div>";
        PageElements::back_button( HOME_PAGE );
        break;          
    default:
        if( !isset( $_SESSION[$permit->session_login_var] ) ) {
            echo "<div class='logincontain'>";
            echo "<form class='loginForm' action='src/login.php' method='post'>";
            token( 'logmein' );
            $login->login_heading();
            echo "<div class='login_explain'>";
            echo "In order to access any content you need to log in.";
            lines(2);
            echo "For more information on the <i>" . PROGRAM_NAME . "</i> please see the <a href=#>help page</a>";
            lines(2);
            echo "If you are unsure of your login information or have forgotten your password, please contact the app administrator";
            echo "</div>";//login_explain

            echo "<div class='login_form_elements'>";
            echo "<div class='login_form_item'>";
            echo "<p class='login_form_items_head'><b>Username</b></p><br>";
            echo "<input class='login_form_items_input' type='text' name='username'>";
            echo "</div>";//login_form_item

            echo "<div class='login_form_item'>";
            echo "<p class='login_form_items_head'><b>Password</b></p><br>";
            echo "<input class='login_form_items_input' type='password' name='password'>";
            echo "</div>";//login_form_item

            echo "<div class='login_form_item'>";
            echo "</div>";//login_form_item
            echo "<input type='submit' name='submit' value='Login'>";
            echo "</div>";//login_form_elements
            echo "</form>";
            echo "</div>";//login container
        }//if isset $_SESSION['account']
        break;
}//switch