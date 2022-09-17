<?php

namespace LBF;

use LBF\Errors\FileNotFoundError;

class Autoloader {

    


    public function __construct(

        private string $src_path,

        private readonly bool $debug_mode = false,

    ) {}


    public function load(): bool {
        return spl_autoload_register( [$this, 'load_class'] );
    }

    public function unload(): bool {
        return spl_autoload_unregister( [$this, 'load_class'] );;
    }

    public function change_path( string $src_path ): void {
        $this->src_path = $src_path;
    }





    /**
     * Dynamically load a class when called. Class name needs to be the same as the file in which it is found
     * 
     * @param   string  $class  Name of the class being called
     * 
     * @access  private
     * @since   LRS 3.1.0
     * @since   LBF 0.1.7-beta  Converted to a class method.
     */

    private function load_class( string $class ) {
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


    /**
     * Draw out the debug feedback table.
     * 
     * @param   string  $class      The class name parsed.
     * @param   string  $file       The modified namespace file path.
     * @param   string  $full_path  The full path (realpath()) of the of the file that should be called.
     * 
     * @access  private
     * @since   LBF 0.1.7-beta
     */

    private function draw_out_feedback_table( string $class, string $file, string $full_path ): void {
        $data = [
            'Called Class'        => $class,
            'Namespace File Name' => $file,
            'Relative Full Path'  => $this->src_path . $file,
            'Realpath Full Path'  => $full_path,
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


    public static function load_debugger(): void {
        ob_start();
        if ( is_callable( ['\Debugger\Debug', '__constructStatic'] ) ) {
            \Debugger\Debug::__constructStatic();
        }
        ob_end_clean();
        return true;
    }



}