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
     * @since   LBF 0.1.5-beta
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
     * @access  public
     * @since   LBF 0.1.6-beta
     */

    public static function temporary_change_echo( bool $temp_echo ): string {
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
     * @access  public
     * @since   LBF 0.1.6-beta
     */
    public static function restore_origonal_echo( string $id ): void {
        self::$echo = self::$temporary_echo[$id];
        unset( self::$temporary_echo[$id] );
    }


    /**
     * The default skip params used by this class.
     * 
     * @var array   $default_skip_params
     * 
     * @static
     * @access  private
     * @since   LBF 0.1.6-beta
     */

    private static array $default_skip_params = [
        'maxmin',
        'echo',
        'text',
        'content',
        'data',
        'label',
    ];


    /**
     * Create a complete tag container.
     * 
     * @param   string  $tag                The tag to create.
     * @param   array   $params             Any params to add to the tag.
     * @param   array   $skip_params_extra  Any extra skip params besides the already defined list to apply to the tag.
     * 
     * @return  string
     * 
     * @static
     * @access  protected
     * @since   LBF 0.1.6-beta
     */

    protected static function html_element_container( string $tag, array $params, array $skip_params_extra = [] ): string {
        $skip_params = array_merge( self::$default_skip_params, $skip_params_extra );
        if ( $tag == 'textarea' ) {
            if ( isset( $params['value'] ) ) {
                $params['text'] = $params['value'];
                unset( $params['value'] );
            }
        }
        $inner_text = $params['text'] ?? $params['content'] ?? $params['data'] ?? '';
        $element = "<{$tag}";
        $element .= self::assign_key_values( $params, $skip_params );
        $element .= ">{$inner_text}</{$tag}>";
        return $element;
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
     * @since   LBF 0.1.5-beta
     */

    protected static function html_tag_open( string $tag, array $params, array $skip_params_extra = [] ): string {
        $skip_params = array_merge( self::$default_skip_params, $skip_params_extra );
        $element = "<{$tag}";
        $element .= self::assign_key_values( $params, $skip_params );
        $element .= '>';
        return $element;
    }


    /**
     * Assign the key value pairs to the element.
     * 
     * @param   array   $params         Any params to add to the tag.
     * @param   array   $skip_params    All the params to skip.
     * 
     * @return  string
     * 
     * @static
     * @access  private
     * @since   LBF 0.1.6-beta
     */

    private static function assign_key_values( array $params, array $skip_params ): string {
        $element = '';
        foreach ( $params as $key => $value ) {
            if ( in_array( $key, $skip_params ) ) {
                continue;
            }
            switch ( $key ) {
                case 'new_tab':
                    if ( !isset( $params['href'] ) ) {
                        $element .= $value ? " target='_blank' rel='noopener'" : '';
                    }
                    break;
                case 'autofocus':
                    $element .= $value ? ' autofocus' : '';
                    break;
                case 'disabled':
                    $element .= $value ? ' disabled' : '';
                    break;
                case 'readonly':
                    $element .= $value ? ' readonly' : '';
                    break;
                case 'required':
                    $element .= $value ? ' required' : '';
                    break;
                case 'multiple':
                    $element .= $value ? ' multiple' : '';
                    break;
                case 'checked':
                    $element .= $value ? ' checked' : '';
                    break;
                case 'indeterminate':
                    $element .= $value ? ' indeterminate' : '';
                    break;
                case 'href':
                    if ( isset( $params['new_tab'] ) && $params['new_tab'] ) {
                        $element .= " onClick='javascript:window.open(\"{$value}\", \"_blank\")'";
                    } else {
                        $element .= " onClick='window.location.href = \"{$value}\"'";
                    }
                    break;
                default:
                    $element .= " {$key}='{$value}'";
            }
        }
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
     * @since   LBF 0.1.5-beta
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