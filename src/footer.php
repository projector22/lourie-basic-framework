<?php

/**
 * Page is called at the end of pages to show the footer section and to close off the body and html tags properly
 * 
 * @author  Gareth  Palmer  @evangeltheology
 * 
 * @since   0.1 Pre-alpha
 */

echo "\n</div>";//wrapper
echo "\n<footer>";

echo "<div class='foot_details'>";
echo "© " . SITE_AUTHOR . " ";
$currentYear = date("Y");
if ( !defined( 'START_YEAR' ) ){
    echo $currentYear;
} else if ( $currentYear > START_YEAR ) {
    echo START_YEAR . " - " . $currentYear;
} else {
    echo START_YEAR;
}//if - tests which year the app was installed
PageElements::lines(1);
echo "Version " . PROGRAM_VERSION;
echo "</div>";//foot_details

if ( file_exists( INCLUDES_PATH . 'config.php' ) ){
    $permit     = new SitePermissions;
    $db_control = new DatabaseControl;

    echo "<div class='foot_login'>";
    if ( isset( $_SESSION[$permit->session_login_var] ) ){
        $accounts = USER_ACCOUNTS;
        $username = $_SESSION[$permit->session_login_var];
        echo "Logged in: ";
        $results = $db_control->sql_select( "SELECT * FROM $accounts WHERE account_name='$username'" );
        if ( count( $results ) > 0 ){
            $row = $results[0];
            if ( $row['first_name'] != '' || $row['first_name'] != null &&
                 $row['last_name'] != '' || $row['last_name'] != null ){
                echo $row['first_name'] . ' ' . $row['last_name'];
            } else if ( $row['last_name'] != '' || $row['last_name'] != null ){
                echo $row['last_name'];
            } else {
                echo $username;
            }
        } else {//if count( $results ) > 0
            echo $username;
        }
        PageElements::lines( 1 );
        echo "Not you? <a href='?logout=1'>Logout</a>";
    }//if isset( $_SESSION['account'] ) - check on a logged in user
}

echo "</div>";//foot_login
echo "</footer>";

echo "\n</body>
</html>";