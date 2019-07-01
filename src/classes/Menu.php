<?php

/**
 * 
 * Class for showing various menus as needed throughout the app
 * 
 * @author  Gareth  Palmer  @evangeltheology
 * @since   0.1 Pre-alpha
 */

class Menu {

    /**
     * Consructor method, things to do when the class is loaded
     * @since   0.1 Pre-alpha
     */
    
    public function __construct(){

    }//__construct

    /**
     * 
     * A template method for displaying the menu
     * 
     * @param   array   $menu   A list of the menu items
     * @param   string  $class  Define the class of the ul element. Default: 'menu'
     * 
     * @since   0.1 Pre-alpha
     */

    private function menu_structure( $menu, $class='menu'){
        echo "<ul class='$class'>";
        foreach ( $menu as $location => $place ){
            echo "<li>";
            echo "<a href=$location>$place</a>";
            echo "</li>";
        }
        echo "</ul>";
    }//private function menu_structure()
    
    /**
     * The site's main menu. Define your own elements
     * 
     * @since   0.1 Pre-alpha
     */

    public function main_menu(){
        $menu = array( 'index.php' => 'Home',
                       'admin.php' => 'Admin',
                       'help.php'  => 'Help',
                       '?logout=1' => 'Logout' );
        $this->menu_structure( $menu );
    }//main menu

    /**
     * Destructor method, things to do when the class is closed
     * 
     * @since   0.1 Pre-alpha
     */

    public function __destruct(){

    }//__destruct

}