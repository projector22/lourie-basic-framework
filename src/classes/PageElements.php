<?php

/**
 * Common buttons and links etc that can be called onto any page
 * 
 * @author  Gareth Palmer  @evangeltheology
 * 
 * @since   0.1 Pre-alpha
 */

class PageElements {
    
    /**
     * A button to send the user back to an app defined page
     * 
     * @param   string  $loc    The location to which the button must point
     * @param   string  $class  The class called to draw the button with        Default: 'todobttns'
     * 
     * @since   0.1 Pre-alpha
     */

    public static function back_button( $loc, $class='todobttns' ){
        $place = '"' . $loc . '"';
        if ( $class == 'todobttns' ){
            echo "<div class='backbutton'>";
        }//if class is default
        echo "<input class='$class' type='button' onClick='window.location.href = $place' value='Back'>";
        if ( $class == 'todobttns' ){
            echo "</div>";
        }//if class is default
    }     


    /**
     * Place the site logo anywhere on the site as required
     * 
     * @param   int $width  Define width of logo. Default: 200px
     * 
     * @since   0.1 Pre-alpha
     */

    public static function site_logo( $width='200' ){
        echo "<img src='src/img/" . SITE_LOGO . "' alt='Logo Placeholder' width='$width" . "px'>";
    }


    /**
     * Providing a spacing element as required
     * 
     * @since   0.1 Pre-alpha
     */

    public static function element_spacer_one() {
        echo "<span class='element_spacer_one'></span><span></span>";    
    }


    /**
     * Providing a spacing element as required
     * 
     * @since   0.1 Pre-alpha
     */

    public static function element_spacer_two() {
        echo "<span class='element_spacer_two'></span><span></span>";    
    }


    /**
     * Provide the HTML elements which make up the site's header section
     * 
     * @since   0.1 Pre-alpha
     */

    public static function site_page_header(){
        echo "\n\t<div class='pagehead'>";
        //Your code here
        // echo "\n\t\t<div><h1>" . SCH_NAME . "</h1></div>";
        // echo "\n\t\t<div><h2>School Registration</h2></div>";
        echo "\n\t</div>\n";
    }
    

    /**
     * Draws the top elements of the page, including the title, subtitle & menu
     * 
     * @since   0.1 Pre-alpha
     */

    public static function top_of_page(){
        self::site_page_header();
        $menu = new Menu;
        $menu->main_menu();
    }

    /**
     * Draws the number of html line breaks required
     * 
     * @param   int     $k  The number of lines to draw     Required
     * 
     * @since   0.1 Pre-alpha
     */


    public static function dot( $k=1 ){
        for( $i = 0; $i < $k; $i++){
            echo "<b>.</b>";
        }//for
    }


    /**
     * Draws the number of html line breaks required
     * 
     * @param   int     $k  The number of lines to draw     Required
     * 
     * @since   0.1 Pre-alpha
     */

    public static function lines( $k ){
        for( $i = 0; $i < $k; $i++){
            echo "<br>";
        }//for
    }

    
    /**
     * Draws the header.php elements onto a page
     * 
     * @since   0.1 Pre-alpha
     */

    public static function page_header(){
        if ( defined( SRC_PATH ) ){
            require_once( SRC_PATH . 'header.php' );
        } else {
            require_once( __DIR__ . '/../header.php' );
        }
    }


    /**
     * Draws the footer.php elements onto a page
     * 
     * @since   0.1 Pre-alpha
     */

    public static function footer(){
        if ( defined( SRC_PATH ) ){
            require_once( SRC_PATH . 'footer.php' );
        } else {
            require_once( __DIR__ . '/../footer.php' );
        }
    }


    /**
     * Draws a page break when printing
     * 
     * @since   0.1 Pre-alpha
     */
    
    public static function page_break(){
        echo "\n<div class='page_break'></div>";
    }


    /**
     * Adds the <script> elements to copy data to clipboard
     * 
     * Credit to:
     * @link https://clipboardjs.com/
     * 
     * @since   0.1 Pre-alpha
     */

    public static function clipboardButton(){
        echo "\n<script src='src\js\clipboard.min.js'></script>";
        echo "\n<script>var clipboard = new Clipboard('.btn');</script>";
    }    
    
}