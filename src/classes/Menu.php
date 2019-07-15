<?php

/**
 * Class for showing various menus as needed throughout the app
 * 
 * @author  Gareth Palmer  @evangeltheology
 * @since   0.1 Pre-alpha
 */

class Menu {

    /**
     * @var string  The name of the menu called
     * 
     * @since   0.1 Pre-alpha
     */

    private $menu_called;


    /**
     * This method can be used to call any menu within the structure of the app
     * 
     * @param   string  $menu   The name of the menu to be called, for example defining 
     * $menu as 'sub' will call the private method 'sub_menu'. Leave blank to call main menu.
     * Default: 'main'
     * 
     * @since   0.1 Pre-alpha
     */

    public function show_menu( $menu='main' ){
        $this->menu_called = $menu;
        $call_menu = $this->menu_called . '_menu';
        $this->$call_menu();
    }//__construct


    /**
     * A template method for displaying the menu
     * 
     * @param   array   $menu   A list of the menu items
     * 
     * @since   0.1 Pre-alpha
     */

    private function menu_structure( $menu_list ){
        echo "\n<nav class='" . $this->menu_called . "_menu'>";//menu element

        if ( $this->menu_called == 'main' ){
            echo "\n\t<div class='menu_toggle'>\n";
            echo "\t\t<input type='checkbox'>";
            for ( $i = 0; $i < 3; $i++ ){
                echo "<span></span>";
            }//for
            echo "\n\t\t<ul class='main_menu_items'>";
        } else {
            echo "\n\t\t<ul>";
        }

        foreach ( $menu_list as $location => $place ){
            echo "\n\t\t\t<li>";
            echo "<a href=$location>$place</a>";
            echo "</li>";
        }//foreach
        echo "\n\t\t</ul>\n";

        if ( $this->menu_called == 'main' ){
            echo "\t</div>\n";
        }
        PageElements::site_logo( 30 );
        echo "</nav>\n";
    }
    
    
    /**
     * The site's main menu. Define your own elements
     * 
     * @since   0.1 Pre-alpha
     */

    private function main_menu(){
        $menu_list = array( 'index.php' => 'Home',
                            'admin.php' => 'Admin',
                            SITE_HELP  => 'Help',
                            '?logout=1' => 'Logout' );
        $this->menu_structure( $menu_list );
    }//main menu

}