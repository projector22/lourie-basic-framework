<?php

namespace LBF\Trek;

use Exception;
use LBF\Db\ConnectMySQL;

/**
 * Tools to be used in normal post update `trek` class
 * 
 * use LBF\Trek\Oxen;
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @since   LRS 3.14.0
 * @since   LRS 3.28.0  Seperated out of `Lourie Registration System` into `Lourie Basic Framework`.
 *                      Namespace changed from `Framework` to `LBF`.
 */

class Oxen extends ConnectMySQL {

    /**
     * If a new table is created and new data is to be assigned to it in this Post Update routine, then it needs to be defined
     * 
     * @example:
     * $this->define_new_table( 'PARENTS', 'parents' );
     * 
     * @param   string  $table_const    The table constant name.
     * @param   string  $table_name     The table name without it's prefix.
     * 
     * @access  protected
     * @since   LRS 3.8.0
     * @since   LRS 3.14.0  Moved to Oxen
     */

    protected function define_new_table( string $table_const, string $table_name ): void {
        if ( !defined ( $table_const ) ) {
            define( $table_const, getenv( 'TABLE_PREFIX' ) . $table_name );
        }
    }


    /**
     * Add a element to bin/config.php
     * 
     * @example:
     * $this->add_element_to_config( "define( 'ACCOUNTS_PERMISSIONS', getenv( 'TABLE_PREFIX' ) . 'user_permissions' );", <- last line of config.php
     *                               "define( 'PARENTS', getenv( 'TABLE_PREFIX' ) . 'parents' );" );                     <- line to insert
     * 
     * @param   string  $search_str         The point in the config from which to paste.
     * @param   string  $new_config_paste   The new content.
     * 
     * @access  protected
     * @since   LRS 3.8.0
     * @since   LRS 3.14.0  Moved to Oxen
     */

    protected function add_element_to_config( string $search_str, string $new_config_paste ): void {
        if ( !defined( 'CONFIG_FILE' ) ) {
            define( 'CONFIG_FILE', '' ); // hide the error.
            throw new Exception( "No config file found. Please define 'CONFIG_FILE'", 404 );
        }
        if ( !file_exists( CONFIG_FILE ) ) {
            throw new Exception( "App configuration file " . CONFIG_FILE . " missing.", 404 );
        }
        
        $new_config  = "{$search_str}\n{$new_config_paste}";
        $file  = file_get_contents( CONFIG_FILE );
        $paste = str_replace( $search_str, $new_config, $file );
        file_put_contents( CONFIG_FILE, $paste );
    }


    /**
     * Replace a piece of the config.
     * 
     * @param   string  $search_str     The string to be replaced
     * @param   string  $new_config     The replacement string
     * 
     * @access  protected
     * @since   LRS 3.17.4
     */

    protected function replace_config_element( string $search_str, string $new_config ): void {
        if ( !defined( 'CONFIG_FILE' ) ) {
            define( 'CONFIG_FILE', '' ); // hide the error.
            throw new Exception( "No config file found. Please define 'CONFIG_FILE'", 404 );
        }
        if ( !file_exists( CONFIG_FILE ) ) {
            throw new Exception( "App configuration file " . CONFIG_FILE . " missing.", 404 );
        }
        $file  = file_get_contents( CONFIG_FILE );
        $paste = str_replace( $search_str, $new_config, $file );
        file_put_contents( CONFIG_FILE, $paste );
    }

}