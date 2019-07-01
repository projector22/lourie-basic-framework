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
        echo "<input class='todobttns' type='button' onClick='window.location.href = $place' value='Back'>";
        echo "</div>";
    }


    /**
     * Destructor method, things to do when the class is closed
     * 
     * @since   0.1 Pre-alpha
     */

    public function __destruct(){

    }//__destruct
}