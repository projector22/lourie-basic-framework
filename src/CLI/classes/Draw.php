<?php

/**
 * Some static tools for displaying things on the screen
 * 
 * Current options
 * - end_of_script() End the script appropriately
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @since   3.14.0
 */

class Draw {

    /**
     * Draw a number of line breaks
     * 
     * @param   integer $n  Number of lines
     * 
     * @access  public
     * @since   3.14.0
     */

    public static function lines( int $n ): void {
        for ( $i = 0; $i < $n; $i++ ) {
            echo "\n";
        }
    }


    /**
     * Return a number of tab breaks
     * 
     * @param   integer $n  Number of tabs
     * 
     * @return  string
     * 
     * @access  public
     * @since   3.14.0
     */

    public static function tabs( int $n ): string {
        $t = '';
        for ( $i = 0; $i < $n; $i++ ) {
            $t .= "\t";
        }
        return $t;
    }


    /**
     * Text to put at the end of all scripts.
     * 
     * @access  public
     * @since   3.14.0
     */

    public static function end_of_script(): void {
        self::lines( 2 );
    }
    
}