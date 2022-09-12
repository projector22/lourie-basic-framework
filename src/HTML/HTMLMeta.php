<?php

namespace LBF\HTML;

use LBF\Auth\Hash;

/**
 * This class contains meta classes & properties for the LBF\HTML namespace.
 * 
 * use LBF\HTML\HTMLMeta;
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @since   LBF 0.1.5-beta
 */

class HTMLMeta {

    /**
     * Whether or not to echo or return the html element
     * 
     * @var    boolean     $echo   Default: true
     * 
     * @static
     * @access  public
     * @since   LRS 3.6.0
     * @since   LBF 0.1.5-beta  Moved to HTMLMeta
     */

    public static bool $echo = true;

    /**
     * Contains the temporary values of $echo as required.
     * 
     * @var array   $temporary_echo     Default: []
     * 
     * @static
     * @since   LBF 0.1.6-beta
     */

    public static array $temporary_echo = [];


    /**
     * Handle the echoing of elements as desired. If `$param['echo'] is
     * parsed, it will overwrite self::$echo.
     * 
     * @param   string  $element    The element to echo.
     * 
     * @static
     * @access  protected
     * @since   0.1.5-beta
     */

    protected static function handle_echo( string $element, array $params = []  ): void {
        if ( isset( $params['echo'] ) ) {
            if ( $params['echo'] ) {
                echo $element;
            }
        } else if ( self::$echo ) {
            echo $element;
        }
    }


    /**
     * Perform a temporary change to the $echo value.
     * 
     * Paired with `restore_origonal_echo` method.
     * 
     * @param   bool    $temp_echo  The echo value to change to.
     * 
     * @return  string  The id of the change, used in restoring the origonal value.
     * 
     * @static
     * @access  protected
     * @since   0.1.6-beta
     */

    protected static function temporary_change_echo( bool $temp_echo ): string {
        $id = Hash::random_id_string();
        self::$temporary_echo[$id] = self::$echo;
        self::$echo = $temp_echo;
        return $id;
    }


    /**
     * Restore the origonal echo value, as identified by $id.
     * 
     * Paired with `temporary_change_echo` method.
     * 
     * @param   string  $id The id of the echo to restore to.
     * 
     * @static
     * @access  protected
     * @since   0.1.6-beta
     */
    protected static function restore_origonal_echo( string $id ): void {
        self::$echo = self::$temporary_echo[$id];
        unset( self::$temporary_echo[$id] );
    }


    /**
     * Create an opening tag, as defined by the param `$tag`.
     * 
     * @param   string  $tag                The tag to create.
     * @param   array   $params             Any params to add to the tag.
     * @param   array   $skip_params_extra  Any extra skip params besides the already defined list to apply to the tag.
     * 
     * @return  string
     * 
     * @static
     * @access  protected
     * @since   0.1.5-beta
     */

    protected static function html_tag_open( string $tag, array $params, array $skip_params_extra = [] ): string {
        $skip_params = array_merge( [
            'maxmin',
            'echo',
        ], $skip_params_extra );
        $element = "<{$tag}";
        foreach ( $params as $key => $value ) {
            if ( in_array( $key, $skip_params ) ) {
                continue;
            }
            switch ( $key ) {
                default:
                    $element .= " {$key}='{$value}'";
            }
        }
        $element .= '>';
        return $element;
    }


    /**
     * Create a closing tag, as defined by the param `$tag`.
     * 
     * @param   string      $tag        The tag to create.
     * @param   int         $count      The number of closing tags to draw.
     * @param   string|null $comment    The HTML comment to add after the tag. Skips if null.
     *                                  Default: null
     * 
     * @return  string
     * 
     * @static
     * @access  protected
     * @since   0.1.5-beta
     */

    protected static function html_tag_close( string $tag, int $count, ?string $comment ): string {
        $element = '';
        for ( $i = 0; $i < $count; $i++ ) {
            $element .= "</{$tag}>";
        }
        if ( !is_null ( $comment ) ) {
            $element .= "<!-- {$comment} -->";
        }
        return $element;
    }

}