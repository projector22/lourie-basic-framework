<?php

/**
 * This page will load if the home page or index.php page is called
 * 
 * @author  Gareth  Palmer  @evangeltheology
 * 
 * @since   0.1 Pre-alpha
 */

$menu->show_menu();

if ( isset( $_GET['perform_uninstall'] ) && $_GET['perform_uninstall'] == '1' ) {
    $debug_tools = new DebugTools;
    $debug_tools->perform_uninstall();
    header( 'Location: index.php' );
}//if


DebugTools::uninstall_form();

echo "<h1>This is the home page</h1>";
echo "The rain in Spain stays mainly on the plain!";
PageElements::lines(2);
DebugTools::show_lorium( 5 );

// $debug_tools->display_array( $_SESSION );

// $debug_tools->serverlist();

PageElements::footer();
