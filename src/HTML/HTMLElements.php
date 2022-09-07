<?php

namespace LBF\HTML;

use Exception;
use LBF\Auth\Hash;
use LBF\HTML\Draw;
use LBF\HTML\HTMLMeta;

/**
 * This class is to draw out basic HTML elements
 * 
 * use LBF\HTML\HTMLElements;
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @since   LRS 3.12.5
 * @since   LRS 3.28.0  Seperated out of `Lourie Registration System` into `Lourie Basic Framework`.
 *                      Namespace changed from `Framework` to `LBF`.
 */

class HTMLElements extends HTMLMeta {

    /**
     * Echo or return a div element.
     * 
     * @param   array   $params     Any items to insert into the div. For example - class='myClass' should be returned as part of the 
     *                              array as ['class' => 'myClass'].
     *                              Default: []
     * 
     * @return  string
     * 
     * @static
     * @access  public
     * @since   LRS 3.12.5
     */

    public static function div( array $params = [] ): string {
        $element = self::html_tag_open( 'div', $params );
        self::handle_echo( $element );
        return $element;
    }


    /**
     * Echo or return a closing div statement.
     * 
     * @param   integer|null    $count      The number of </div> elements to insert.
     *                                      Default: 1
     * @param   string          $comment    A bit of text to insert as a comment.
     *                                      Default: null
     * 
     * @return  string
     * 
     * @static
     * @access  public
     * @since   LRS 3.12.5
     */

    public static function close_div( int $count = 1, ?string $comment = null ): string {
        $element = self::html_tag_close( 'div', $count, $comment );
        self::handle_echo( $element );
        return $element;
    }


    /**
     * Draw out a container div with contents.
     * 
     * Result: 
     * ```html
     * <div param='value'>$content</div>
     * ```
     * 
     * @param   array   $params     The params of the div element.
     *                              Default: []
     * @param   string  $content    The content of the div.
     *                              Default: ''
     * 
     * @return  string
     * 
     * @static
     * @access  public
     * @since   LRS 3.12.7
     */

    public static function div_container( array $params = [], string $content = '' ): string {
        $echo_hold = self::$echo;
        self::$echo = false;
        $element = self::div( $params ) . $content . self::close_div();
        self::$echo = $echo_hold;
        self::handle_echo( $element );
        return $element;
    }


    /**
     * Echo or return a span element.
     * 
     * @param   array   $params     Any items to insert into the span. For example - class='myClass' should be returned as part of the 
     *                              array as ['class' => 'myClass'].
     *                              Default: []
     * 
     * @return  string
     * 
     * @static
     * @access  public
     * @since   LRS 3.12.5
     */

    public static function span( array $params = [] ): string {
        $element = self::html_tag_open( 'span', $params );
        self::handle_echo( $element );
        return $element;
    }


    /**
     * Echo or return a closing span statement.
     * 
     * @param   integer $count          The number of </span> elements to insert.
     *                                  Default: 1
     * @param   string  $comment        A bit of text to insert as a comment.
     *                                  Default: null
     * 
     * @return  string
     * 
     * @static
     * @access  public
     * @since   LRS 3.12.5
     */

    public static function close_span( int $count = 1, ?string $comment = null ) {
        $element = self::html_tag_close( 'span', $count, $comment );
        self::handle_echo( $element );
        return $element;
    }


    /**
     * Draw out a container `span` with contents.
     * 
     * Result: 
     * ```html
     * <span param='value'>$content</span>
     * ````
     * 
     * @param   array   $params     The params of the div element.
     *                              Default: []
     * @param   string  $content    The content of the div.
     *                              Default: ''
     * 
     * @return  string
     * 
     * @static
     * @access  public
     * @since   LRS 3.12.7
     */

    public static function span_container( array $params = [], string $content = '' ): string {
        $echo_hold = self::$echo;
        self::$echo = false;
        $element = self::span( $params ) . $content . self::close_span();
        self::$echo = $echo_hold;
        self::handle_echo( $element );
        return $element;
    }


    /**
     * Echo or return a `p` (paragraph) element.
     * 
     * @param   array   $params     Any items to insert into the span. For example - class='myClass' should be returned as part of the 
     *                              array as ['class' => 'myClass'].
     *                              Default: []
     * 
     * @return  string
     * 
     * @static
     * @access  public
     * @since   LBF 0.1.5-beta
     */

    public static function p( array $params = [] ): string {
        $element = self::html_tag_open( 'p', $params );
        self::handle_echo( $element );
        return $element;
    }


    /**
     * Echo or return a closing `p` (paragraph) statement.
     * 
     * @param   integer $count          The number of </span> elements to insert.
     *                                  Default: 1
     * @param   string  $comment        A bit of text to insert as a comment.
     *                                  Default: null
     * 
     * @return  string
     * 
     * @static
     * @access  public
     * @since   LBF 0.1.5-beta
     */

    public static function close_p( int $count = 1, ?string $comment = null ) {
        $element = self::html_tag_close( 'span', $count, $comment );
        self::handle_echo( $element );
        return $element;
    }


    /**
     * Draw out a container `p` paragraph with contents.
     * 
     * Result: 
     * ```html
     * <div param='value'>$content</div>
     * ```
     * 
     * @param   array   $params     The params of the div element.
     *                              Default: []
     * @param   string  $content    The content of the div.
     *                              Default: ''
     * 
     * @return  string
     * 
     * @static
     * @access  public
     * @since   LBF 0.1.5-beta
     */

    public static function p_container( array $params = [], string $content = '' ): string {
        $echo_hold = self::$echo;
        self::$echo = false;
        $element = self::span( $params ) . $content . self::close_span();
        self::$echo = $echo_hold;
        self::handle_echo( $element );
        return $element;
    }


    /**
     * Draw out a <h$n> element (<h1><h2> etc.)
     * 
     * @param   integer $size       The number of the <h> element 1 - 6.
     * @param   string  $content    The content of the heading.
     * @param   array   $params     Any html parameters added to the element
     * 
     * @return  string
     * 
     * @static
     * @access  public
     * @since   LRS 3.12.7
     */

    public static function heading( int $size, string $content, array $params = [] ): string {
        $element = "<h{$size}";
        foreach ( $params as $index => $value ) {
            $element .= " {$index}='$value'";
        }
        $element .= ">{$content}</h{$size}>";
        self::handle_echo( $element );
        return $element;
    }


    /**
     * Draw an open form statement (`<form>`) and attach parameters as required
     * 
     * @param   array   $params     Any parameters to add to the form tag, like action='example' or method='example'
     *                              Default: []
     * @param   boolean $new_tab    Whether to cause the form to submit to a new tab. This is done by inserting Draw::NEW_TAB
     * 
     * @return  string
     * 
     * @static
     * @access  public
     * @since   LRS 3.12.7
     */    

    public static function form( array $params = [], bool $new_tab = false ): string {
        $element = '<form';
        foreach ( $params as $item => $value ) {
            $element .= " {$item}='{$value}'";
        }
        if ( $new_tab ) {
            $element .= ' ' . Draw::NEW_TAB;
        }
        $element .= '>';
        self::handle_echo( $element );
        return $element;
    }


    /**
     * draw a close form statement - `</form>`
     * 
     * @return  string
     * 
     * @static
     * @access  public
     * @since   LRS 3.12.7
     */

    public static function close_form(): string {
        $element = '</form>';
        self::handle_echo( $element );
        return $element;
    }


    /**
     * Draw an html <a></a> link onto the screen.
     * 
     * Set 'new_tab' => true to add the appropriate fields to open link in a new tab.
     * 
     * @param   string  $href       The link address.
     * @param   string  $text       The text of the link, rather than the raw link.
     * @param   array   $params     Extra elements to add to the link, for example, new_tab
     * 
     * @return  string
     * 
     * @static
     * @access  public
     * @since   LRS 3.12.8
     */

    public static function link( string $href, ?string $text, array $params = [] ): string {
        $element = "<a href='{$href}'";

        foreach ( $params as $index => $value ) {
            if ( $index == 'new_tab' ) {
                $element .= ' ' . Draw::NEW_TAB;
                continue;
            }
            $element .= " {$index}='{$value}'";
        }

        $element .= ">{$text}</a>";
        self::handle_echo( $element );
        return $element;
    }


    /**
     * Draw out an image tag.
     * 
     * @param   array   $params The elements to add to the image
     * 
     * @return  string
     * 
     * @static
     * @access  public
     * @since   LRS 3.17.1
     */

    public static function img( array $params = [] ): string {
        if ( !isset( $params['src'] ) ) {
            throw new Exception( "Path to file not set. Please set param 'src" );
        }

        $skip_params = ['unit'];

        $unit = $params['unit'] ?? 'px';
        $dimensions = ['height', 'width'];
        foreach ( $dimensions as $dimension ) {
            if ( isset( $params[$dimension] ) ) {
                if ( is_numeric( $params[$dimension] ) ) {
                    $params[$dimension] = "{$params[$dimension]}{$unit}";
                }
            }
        }
        
        $item = '<img';

        foreach ( $params as $index => $value ) {
            if ( in_array( $index, $skip_params ) ) {
                continue;
            }
            $item .= " {$index}='{$value}'";
        }

        $item .= '>';

        self::handle_echo( $item );
        return $item;
    }


    /**
     * Draw out an HTML iframe.
     * 
     * ## Common params:
     * 
     * - src
     * - srcdoc
     * - default_unit
     * - height
     * - width
     * - loading [eager, lazy]
     * 
     * @see https://www.w3schools.com/tags/tag_iframe.ASP for all tags.
     * 
     * @param   array   $params     The data to be parsed into the iframe. Must at least
     *                              contain 'src' or 'srcdoc'.
     * 
     * @return  string
     * 
     * @static
     * @access  public
     * @since   LRS 3.11.1
     * @since   LRS 3.27.1  Moved from `src/Framework/HTML/Draw.php` to `src/Framework/HTML/HTMLElements.php`
     */

    public static function iframe( array $params ): string {
        $skip_params = [
            'default_unit',
        ];

        if ( !isset( $params['src'] ) && !isset( $params['srcdoc'] ) ) {
            throw new Exception( "An iframe must have either the parameter 'src' or 'srcdoc'." );
        }

        if ( !isset( $params['id'] ) ) {
            $params['id'] = Hash::random_id_string();
        }
        if ( !isset( $params['loading'] ) ) {
            $params['loading'] = 'lazy';
        }

        if ( !isset( $params['default_unit'] ) ) {
            $params['default_unit'] = 'px';
        }

        if ( !isset( $params['width'] ) ) {
            $params['width'] = '100%';
        } else {
            if ( is_numeric( $params['width'] ) ) {
                $params['width'] .= $params['default_unit'];
            }
        }

        if ( !isset( $params['height'] ) ) {
            $params['height'] = '300px';
        } else {
            if ( is_numeric( $params['height'] ) ) {
                $params['height'] .= $params['default_unit'];
            }
        }

        $item = '<iframe';

        foreach ( $params as $key => $value ) {
            if ( in_array( $key, $skip_params ) ) {
                continue;
            }
            $item .= " {$key}='{$value}'";
        }

        $item .= '>Sorry, your browser does not allow previews.';
        $item .= '</iframe>';

        self::handle_echo( $item );
        return $item;
    }

}