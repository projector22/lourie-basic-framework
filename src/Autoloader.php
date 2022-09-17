<?php

namespace LBF;

use LBF\Errors\FileNotFoundError;

class Autoloader {

    


    public function __construct(

        private readonly string $src_path,

        private readonly bool $debug_mode = false,

    ) {
        spl_autoload_register( [$this, 'load_class'] );
    }








    /**
     * Dynamically load a class when called. Class name needs to be the same as the file in which it is found
     * 
     * @param   string  $class  Name of the class being called
     * 
     * @since   LRS 3.1.0
     */

    public function load_class( string $class ) {
        $file = str_replace( '\\', '/', $class ) . '.php';

        $file = implode( '/', array_map( function ( string $item ): string {
            if ( str_contains( $item, '.php' ) ) {
                return $item;
            }
            return strtolower( $item );
        }, explode( '/', $file ) ) );

        $path = realpath( $this->src_path . $file );
        if ( !file_exists ( $path ) ) {
            if ( $this->debug_mode ) {
                $this->draw_out_feedback_table( $class, $file, $path );
            } else {
                throw new FileNotFoundError( "Called class {$class} not found.", 404 );
            }
            return false;
        }
        require $path;

        if ( method_exists( $class, '__constructStatic' ) ) {
            $class::__constructStatic();
        }

        return true;
    }


    private function draw_out_feedback_table( string $class, string $file, string $full_path ): void {
        $data = [
            'Called Class'        => $class,
            'Namespace File Name' => $file,
            'Full Path of File'   => $full_path,
        ];
        
        echo "<table>";
        foreach ( $data as $heading => $feedback ) {
            echo "<tr>";
            echo "<th>{$heading}</th>";
            echo "<td>{$feedback}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }




}