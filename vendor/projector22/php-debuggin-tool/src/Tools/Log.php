<?php

namespace Debugger\Tools;

/**
 * A set of tools that can be called logging commands.
 * 
 * @author  Gareth Palmer  @evangeltheology
 * 
 * @since   1.0.2
 */

class Log {

    /**
     * Log the requested data to file
     * 
     * @param   mixed   $data   The data to log.
     * @param   string  $file   The name of the file to log to. Default: 'dev'
     * 
     * @access  public
     * @since   1.0.2
     */

    public function to_file( mixed $data, string $file = 'dev' ): void {
        $timestamp = date( 'Y-m-d G:i:s' );
        if ( !str_contains( $file, '.log' ) ) {
            $file .= '.log';
        }
        if ( file_exists( $file ) ) {
            // If you name a full path to a file to log.
            $path = $file;
        } else {
            $log_path =  realpath( __DIR__ . "/../../logs" );
            $path = $log_path . '/' . $file;
        }

        if ( is_array( $data ) || is_object( $data ) ) {
            $text = json_encode( $data, JSON_PRETTY_PRINT );
        } else {
            $text = $data;
        }

        try {
            $fp = fopen( $path, 'a' );
            if ( is_bool( $fp ) ) {
                throw new \Exception( "Cannot create or write to file, please check folder write & user permissions." );
            }
            fwrite( $fp, "{$timestamp}\t\t{$text}\n" );
            fclose( $fp );
        } catch ( \Throwable $th ) {
            echo "Could not write to log file: <b>{$path}</b> - The following error was thrown:<br>";
            echo $th->getMessage();
            echo "<br><br>";
        }
    }

}