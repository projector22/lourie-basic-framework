<?php

namespace LBF\HTML;

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
     * @since   LRS 3.12.5
     * @since   LBF 0.1.5   Moved to HTMLMeta
     */

    public static bool $echo = true;


    /**
     * Handle the echoing of elements as desired.
     * 
     * @param   string  $element    The element to echo.
     * 
     * @static
     * @access  protected
     * @since   0.1.5-beta
     */

    protected static function handle_echo( string $element ): void {
        if ( self::$echo ) {
            echo $element;
        }
    }


    /**
     * Create an opening tag, as defined by the param `$tag`.
     * 
     * @param   string  $tag    The tag to create.
     * @param   array   $params Any params to add to the tag
     * 
     * @return  string
     * 
     * @static
     * @access  protected
     * @since   0.1.5-beta
     */

    protected static function html_tag_open( string $tag, array $params ): string {
        $skip_params = [
            'maxmin',
        ];
        $element = "<{$tag}";
        foreach ( $params as $key => $value ) {
            if ( in_array( $key, $skip_params ) ) {
                continue;
            }
            $element .= " {$key}='{$value}'";
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