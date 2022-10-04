<?php

namespace LBF;

use LBF\Errors\Files\FileNotFound;

/**
 * Perform app autoloading tasks.
 * 
 * Folder structure should be as follows (note the case):
 * 
 * `file\path\to\Class.php`
 * 
 * All directories should be lowercase, but when calling the class as
 * a namespace, call like this (note the case):
 * 
 * `use File\Path\To\Class;`
 * 
 * use LBF\Autoloader;
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @since   LBF 0.1.7-beta
 */

class Autoloader {

    /**
     * Class constructor.
     * 
     * @param   string  $src_path   The base path where the classes are saved. The full path to the called class
     *                              will be `$this->src_path . namespace . class.php`.
     * @param   boolean $debug_mode Whether to perform debug operations to the autoload process.
     * 
     * @access  public
     * @since   LBF 0.1.7-beta
     */

    public function __construct(

        /**
         * The base path where the classes are saved. The full path to the called class
         * will be `$this->src_path . namespace . class.php`.
         * 
         * @var string  $src_path
         * 
         * @access  private
         * @since   LBF 0.1.7-beta
         */

        private string $src_path,

        /**
         * Whether to perform debug operations to the autoload process.
         * 
         * @var boolean $debug_mode
         * 
         * @readonly
         * @access  private
         * @since   LBF 0.1.7-beta
         */

        private readonly bool $debug_mode = false,

    ) {}


    /**
     * Perform the autoload task by calling `spl_autoload_register`.
     * 
     * @return  bool
     * 
     * @access  public
     * @since   LBF 0.1.7-beta
     */

    public function load(): bool {
        return spl_autoload_register( [$this, 'load_class'] );
    }


    /**
     * Unload the previously loaded autoloader by calling `spl_autoload_unregister`.
     * 
     * @return  bool
     * 
     * @access  public
     * @since   LBF 0.1.7-beta
     */

    public function unload(): bool {
        return spl_autoload_unregister( [$this, 'load_class'] );;
    }


    /**
     * Change the source path, from which the autoloader autoloads from.
     * 
     * @param   string  $src_path   The new source path.
     * 
     * @access  public
     * @since   LBF 0.1.7-beta
     */
    public function change_path( string $src_path ): void {
        $this->src_path = $src_path;
    }


    /**
     * Dynamically load a class when called. Class name needs to be the same as the file in which it is found.
     * 
     * @param   string  $class  Name of the class being called.
     * 
     * @return  boolean
     * 
     * @access  private
     * @since   LRS 3.1.0
     * @since   LBF 0.1.7-beta  Converted to a class method.
     */

    private function load_class( string $class ): bool {
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
                throw new FileNotFound( "Called class {$class} not found.", 404 );
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


    /**
     * Autoload Debug Tools environment as needed and if possible.
     * 
     * @static
     * @access  public
     * @since   0.1.7-beta
     */

    public static function load_debugger(): void {
        ob_start();
        if ( is_callable( ['\Debugger\Debug', '__constructStatic'] ) ) {
            \Debugger\Debug::__constructStatic();
        }
        ob_end_clean();
    }

}