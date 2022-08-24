<?php

namespace Debugger\Tools;

/**
 * A set of tools that can be called executing cmd or terminal commands.
 * 
 * @author  Gareth Palmer  @evangeltheology
 * 
 * @since   1.0.2
 */

class Cmd {

    /**
     * Show the terminal output directly from a command
     * 
     * @link    https://stackoverflow.com/questions/20107147/php-reading-shell-exec-live-output
     * 
     * @access  public
     * @since   1.0.2
     */

    public function show_output( $cmd ) {
        while ( @ob_end_flush() ); // end all output buffers if any
        $proc = popen( $cmd, 'r' );
        echo '<pre>';
        while ( !feof( $proc ) ) {
            echo fread( $proc, 4096 );
            @flush();
        }
        echo '</pre>';
    }


}