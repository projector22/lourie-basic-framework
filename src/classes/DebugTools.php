<?php

/**
 * 
 * A set of tools that can be called to help in the development and debugging of this app.
 * 
 * @author  Gareth  Palmer  @evangeltheology
 * 
 * @since   0.1 Pre-alpha
 */

class DebugTools extends DatabaseControl {

    /**
     * Consructor method, things to do when the class is loaded
     * 
     * @since   0.1 Pre-alpha
     */
    public function __construct(){

    }//__construct

    /**
     * 
     * Perform an uninstall - deletes the config.php file and drops the table
     * 
     * @since   0.1 Pre-alpha
     */

    public function perform_uninstall(){
        $test = $this->sql_execute( "DROP DATABASE " . DB_NAME );
        if ( !$test ){
            echo "<br>Could not drop the database for the above stated reason<br>";
        } else {
            echo "Database dropped<br>";
        }
        if ( file_exists( INCLUDES_PATH . 'config.php' ) ){
            unlink( INCLUDES_PATH . 'config.php' );
            echo "Config file successfully removed<br>";
        } else {
            echo "Config file already removed<br>";
        }
    }

    /**
     * 
     * Adds the form element for running an uninstall, uses the get method and creates a $_GET['perform_uninstall'] == 1
     * 
     * @since   0.1 Pre-alpha
     */


    public function uninstall_form(){
        echo "<form method='get'>";
        echo "<input type='submit' value='Uninstall'>";
        echo "<input type='hidden' name='perform_uninstall' value='1'>";
        echo "</form>";
    }

    /**
     * 
     * Displays all the variables neatly in the $_SERVER variable
     * 
     *
     * @since 0.1 Pre-alpha
     */

    public static function serverlist(){
        $array = $_SERVER;
        $list_title = array_keys( $array );
        echo "<table>";
        echo "<tr><th>Index</th><th>Key</th></tr>";
    
        foreach ( $array as $key => $list ){
            $arr_key = array_search( $key, array_keys( $array ) );
            $item = $list_title[ $arr_key ];
            echo "<tr><td>$item</td><td>$list</td></tr>";
        }//foreach
        echo "</table>";
    }

    /**
     * 
     * Performs a <pre>print_r</pre> on any array
     * 
     * @param   array   $data   Any array
     * @return  false   If $data is not array
     * 
     * @since   0.1 Pre-alpha
     */
    
    public static function display_array( $data ){
        if ( !is_array( $data ) ){
            return "<h2>Error - data is not an array</h2>";
        }
        echo "<pre>";
        print_r( $data );
        echo "</pre>";
    }

    /**
     * 
     * See what has been posted
     * 
     * @param   array   $post   $_POST data
     * 
     * @since 0.1 Pre-alpha
     */
    
    public function see_whats_posted( $post ){
        foreach ( $post as $i => $k ){
            echo "<b>$i</b>: $k<br>";
        }
    }

    /**
     * Destructor method, things to do when the class is closed
     * 
     * @since   0.1 Pre-alpha
     */

    public function __destruct(){

    }//__destruct
}