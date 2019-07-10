<?php

/**
 * Common buttons and links etc that can be called onto any page
 * 
 * @author  Gareth  Palmer  @evangeltheology
 * 
 * @since   0.1 Pre-alpha
 */

class PageElements {
    
    /**
     * A button to send the user back to an app defined page
     * 
     * @param   string  $loc    The location to which the button must point
     * 
     * @since   0.1 Pre-alpha
     */
    
    public static function back_button( $loc ){
        $place = '"' . $loc . '"';
        echo "<div class='backbutton'>";
        echo "<input class='submit_button_one' type='button' onClick='window.location.href = $place' value='Back'>";
        echo "</div>";
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

    public static function element_spacer_one() {
        echo "<span class='element_spacer_one'></span><span></span>";    
    }

    public static function dot( $k=1 ){
        for( $i = 0; $i < $k; $i++){
            echo "<b>.</b>";
        }//for
    }

    public static function lines( $k ){
        for( $i = 0; $i < $k; $i++){
            echo "<br>";
        }//for
    }

    public static function page_header(){
        if ( defined( SRC_PATH ) ){
            require_once( SRC_PATH . 'header.php' );
        } else {
            require_once( __DIR__ . '/../header.php' );
        }
    }
    
    public static function footer(){
        if ( defined( SRC_PATH ) ){
            require_once( SRC_PATH . 'footer.php' );
        } else {
            require_once( __DIR__ . '/../footer.php' );
        }
    }
    
}