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

    public static function show_lorium( $count ){
        for ($i = 0; $i < $count; $i++){
            echo "<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc id suscipit lorem. Aliquam erat volutpat. Quisque congue dapibus pulvinar. Maecenas viverra elementum velit. Suspendisse ullamcorper quis tortor sed aliquet. Vestibulum congue ligula semper arcu euismod egestas. Ut vel placerat sapien, sit amet lacinia nibh. Duis egestas orci nec est rutrum elementum. Suspendisse vel vulputate dolor. Donec dapibus lorem eget diam ornare, vitae feugiat nunc mattis. Curabitur vel congue mi, ut iaculis urna. Nullam non quam ultricies, pulvinar lorem sed, cursus nulla. Sed id vehicula leo. Cras ante massa, sagittis consectetur ipsum in, aliquet cursus dolor. Nulla facilisi. In quis molestie lorem.</p>
            <p>Suspendisse porta sollicitudin dolor non tincidunt. Duis eget vulputate ipsum, eu tincidunt justo. Ut eget tincidunt orci. Suspendisse ac quam et nulla interdum imperdiet non at ante. Aenean condimentum nec nisl vitae faucibus. Nam diam elit, finibus vel quam vel, luctus ultrices tellus. Phasellus in lorem vitae nisl gravida bibendum eget in ante. Sed maximus venenatis maximus. Aliquam rutrum, leo sed dignissim commodo, nisl nisi ultrices tellus, sed tempus urna tortor vel ante. Aliquam congue orci id tortor elementum, at tempus nulla egestas. Nunc mattis lacus id odio mollis, vitae lacinia massa ornare. Ut ultricies felis lacus, et mollis nunc eleifend sit amet. Quisque arcu sem, faucibus eu dolor in, pretium tempor dolor. Duis convallis auctor mi, in accumsan nibh rhoncus eget. Mauris at est libero.</p>
            <p>Cras justo mi, fringilla quis arcu sed, viverra congue quam. Duis rhoncus metus diam, quis fermentum leo dignissim eget. Nullam fringilla enim ac turpis vulputate dapibus. Aliquam quis dolor sapien. Duis a arcu mauris. Ut eget erat sagittis, efficitur mi sed, porta justo. Fusce porta purus convallis eleifend tempor. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Proin vitae iaculis lacus. Sed in eleifend est. Quisque id nunc porttitor, accumsan massa in, sagittis elit. Phasellus auctor viverra iaculis.</p>
            <p>Etiam sed orci orci. Fusce semper leo vel ullamcorper vulputate. Duis id nibh eu dui sagittis malesuada. Mauris magna tortor, facilisis vitae augue nec, efficitur imperdiet magna. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Suspendisse condimentum quis sem at porta. Duis non interdum nulla. Cras sit amet eleifend sapien. Nunc tellus enim, aliquam eget felis quis, viverra viverra mi. Integer pharetra libero arcu, vehicula scelerisque erat volutpat eget. Nam malesuada leo sed nulla imperdiet, eget vestibulum lorem maximus. Nam placerat, elit at suscipit tincidunt, dolor leo ultricies neque, in ultricies justo massa vel libero. Duis eu risus id nisl ullamcorper pellentesque et a nunc. Maecenas luctus nibh non est rhoncus, et euismod ligula dictum.</p>
            <p>Nam id posuere urna. Aenean velit justo, aliquet nec mauris sed, placerat vehicula justo. Cras et urna id ligula blandit commodo. Sed sed quam ornare, condimentum ex nec, accumsan justo. Donec id auctor mauris. Morbi accumsan mi nec lorem faucibus faucibus. Duis suscipit fringilla tellus id efficitur. Etiam ultrices enim vitae arcu posuere, ac auctor urna volutpat. Cras vel porta metus, quis porttitor sapien. Duis maximus, magna et porta tempor, libero augue ultrices libero, quis ultrices eros turpis at tortor. Aliquam imperdiet malesuada risus aliquam rutrum.</p>";
            lines(2);
        }//for
    }

}