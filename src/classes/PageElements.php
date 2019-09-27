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
        // echo "\n\t\t<div><h1>Heading</h1></div>";
        // echo "\n\t\t<div><h2>Subheading</h2></div>";
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
        $menu->show_menu();
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


    /**
     * Draws an appropriate error message
     * 
     * @param   string  $info   What message to show    Default: ''
     * 
     * @since   0.1 Pre-alpha
     */
    
    public static function action_error( $info='' ){
        if ( $info == '' ){
            echo "An error has occured ";
        } else {
            echo "An error has occured, $info ";
        }
    }


    /**
     * Draw out a <span> which is used in the description in an admin or discipline page element
     * 
     * @param   string  $input  The string to be displayed on the screen
     * @param   int     $lines  The number of lines to be drawn before the text     Default: 1
     * 
     * @since   0.1 Pre-alpha
     */

    public static function item_description( $input, $lines=1 ){
        PageElements::lines( $lines );
        echo "<span>$input</span>";
    }

    /**
     * Draw out a span which will be used to draw the response text from JS AJAX calls 
     * 
     * @param   string  $id     Desired span id elemenet
     * 
     * @since   0.1 Pre-alpha
     */

    public static function response_text( $id ){
        echo "<span id='$id'></span>"; 
    }


    /**
     * Draw out the JS to change default behaviour of a button press
     * 
     * @param   string  $input      The id of the item to be monitored
     * @param   string  $button     The button to be clicked when the appropriate button is pressed
     * @param   string  $keycode    The key to be monitored, Default: 13 (Enter)
     * 
     * @since   0.1 Pre-alpha
     */

    public static function change_button_behaviour( $input, $button, $keycode = 13 ){
        echo "\n<script>
        var input = document.getElementById('$input');
        input.addEventListener('keyup', function(event) {
            if (event.keyCode === $keycode) {
                event.preventDefault();
                document.getElementById('$button').click();
            }
        });\n</script>\n";
    }

    
    /**
     * Draw out the JS to stop the default behaviour of a defined key press
     * 
     * @param   string  $keycode    The key to be monitored, Default: 13 (Enter)
     * 
     * @since   0.1 Pre-alpha
     */

    public static function block_default_button_press( $keycode = 13 ){
        echo "\n<script>
        window.addEventListener('keydown', function (event) {
            if (event.keyCode === $keycode) {
                event.preventDefault();
                return false;
            }
        });\n</script>\n";
    }    
    
}