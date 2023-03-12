<?php

namespace LBF\App;

use LBF\App\Config;
use LBF\Errors\Files\DirectoryNotFound;

/**
 * Load in config data into the App, to be used by the `Config` object.
 * 
 * use LBF\App\ConfigLoader;
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @since   LBF 0.6.0-beta
 */

class ConfigLoader {

    /**
     * Any directories to skip when loading in directories at a time.
     * 
     * @var array   $skip
     * 
     * @access  private
     * @since   LBF 0.6.0-beta
     */

    private array $skip = ['.', '..'];


    /**
     * Load in nominated data into the `Config` object.
     * 
     * @param   array   $config     The data to load.
     * 
     * @access  public
     * @since   LBF 0.6.0-beta
     */

    public function load_config( array $config ): void {
        Config::load( $config );
    }


    /**
     * Scan a nominated directory and load in any config data files from there.
     * 
     * A file should be something like this:
     * ```php
     * <?php
     * return [
     *  'key' => [
     *      'k1' => 'v1',
     *      'k2' => 'v2',
     *  ];
     * ];
     * ```
     * 
     * @param   string  $dirname    The name of the directory to scan for files.
     * 
     * @return  bool
     * 
     * @access  public
     * @since   LBF 0.6.0-beta
     */

    public function load_config_from_dir( string $dirname ): bool {
        $config = [];
        if ( !is_dir( $dirname ) ) {
            throw new DirectoryNotFound( "Cannot find directory {$dirname}" );
        }
        $files = scandir( $dirname );
        foreach ( $files as $file ) {
            if ( in_array( $file, $this->skip ) ) {
                continue;
            }
            $config = array_merge( $config, require $file );
        }
        if ( isset( $config['paths'] ) ) {
            foreach ( $config['paths'] as $key => $value ) {
                $this->replace_vars( $config, "@{$key}", $value );
            }
        }
        $this->load_config( $config );
        return true;
    }


    /**
    * Recursively replace shortcuts in the config.
    * 
    * @param    array   $the_config     The Config array data.
    * @param    string  $search         The value to search for.
    * @param    string  $replace        The replacement value.
    * 
    * @access   private
    * @since    LBF 0.6.0-beta
    */

    private function replace_vars( mixed &$the_config, string $search, string $replace ): void {
        foreach ( $the_config as &$value ) {
            if ( is_array( $value ) ) {
                $this->replace_vars( $value, $search, $replace );
            } else if ( is_object( $value ) ) {
               // Skip - object properties should not use shortcuts.
               continue;
           } else if ( is_string( $value ) ) {
               $value = str_replace( $search, $replace, $value );
           }
        }
    }
   
}