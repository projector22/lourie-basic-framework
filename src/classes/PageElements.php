<?php

/**
 * 
 * Common buttons and links etc that can be called onto any page
 * 
 * @author  Gareth  Palmer  @evangeltheology
 * 
 * @since   0.1 Pre-alpha
 */

class PageElements {


    /**
     * Consructor method, things to do when the class is loaded
     * 
     * @since   0.1 Pre-alpha
     */
    public function __construct(){

    }//__construct

    
    /**
     * 
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
     * 
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
     * Destructor method, things to do when the class is closed
     * 
     * @since   0.1 Pre-alpha
     */

    public function __destruct(){

    }//__destruct
}