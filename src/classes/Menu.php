<?php

class Menu {
    
    public function __construct(){

    }//__construct

    private function menu_structure( $menu, $class='menu'){
        echo "<ul class='$class'>";
        foreach ( $menu as $location => $place ){
            echo "<li>";
            echo "<a href=$location>$place</a>";
            echo "</li>";
        }
        echo "</ul>";
    }//private function menu_structure()
    
    public function main_menu(){
        $menu = array( 'index.php' => 'Home',
                       'admin.php' => 'Admin',
                       'help.php'  => 'Help' );
        $this->menu_structure( $menu );
    }//main menu

    public function __destruct(){

    }//__destruct

}