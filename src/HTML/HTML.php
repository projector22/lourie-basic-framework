<?php

namespace LBF\HTML;

use LBF\Auth\Hash;
use LBF\Errors\IO\InvalidInput;
use LBF\Errors\IO\MissingRequiredInput;
use LBF\HTML\Draw;
use LBF\HTML\HTMLMeta;
use LBF\HTML\Injector\CSSInjector;
use LBF\HTML\Injector\JSInjector;

/**
 * This class is to draw out basic HTML elements.
 * 
 * use LBF\HTML\HTML;
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @since   LRS 3.12.5
 * @since   LRS 3.17.2      Seperated out as a shortcut class to `src/HTML/HTMLElements.php`.
 * @since   LRS 3.28.0      Seperated out of `Lourie Registration System` into `Lourie Basic Framework`.
 *                          Namespace changed from `Framework` to `LBF`.
 * @since   LBF 0.1.5-beta  Added extension `HTMLMeta`.
 * @since   LBF 0.6.0-beta  Merged with `src/HTML/HTMLElements.php`, removing the need for a shortcut.
 */

class HTML extends HTMLMeta {

    use JSInjector;
    use CSSInjector;

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

    public static function div(array $params = []): string {
        $element = self::html_tag_open('div', $params);
        self::handle_echo($element, $params);
        return $element;
    }


    /**
     * Echo or return a closing div statement.
     * 
     * @param   integer         $count      The number of </div> elements to insert.
     *                                      Default: 1
     * @param   string|null     $comment    A bit of text to insert as a comment.
     *                                      Default: null
     * @param   bool|null       $echo       Overwrite whether the method echos or not.
     *                                      Default: false
     * 
     * @return  string
     * 
     * @static
     * @access  public
     * @since   LRS 3.12.5
     * @since   LBF 0.2.1-beta   Added param `$echo`
     */

    public static function close_div(int $count = 1, ?string $comment = null, ?bool $echo = null): string {
        $element = self::html_tag_close('div', $count, $comment);
        if (!is_null($echo)) {
            $hold = self::temporary_change_echo($echo);
        }
        self::handle_echo($element);
        if (!is_null($echo)) {
            self::restore_origonal_echo($hold);
        }
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

    public static function div_container(array $params = [], string $content = ''): string {
        $hold = self::temporary_change_echo(false);
        $element = self::div($params) . $content . self::close_div();
        self::restore_origonal_echo($hold);
        self::handle_echo($element, $params);
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

    public static function span(array $params = []): string {
        $element = self::html_tag_open('span', $params);
        self::handle_echo($element, $params);
        return $element;
    }


    /**
     * Echo or return a closing span statement.
     * 
     * @param   integer     $count      The number of </span> elements to insert.
     *                                  Default: 1
     * @param   string|null $comment    A bit of text to insert as a comment.
     *                                  Default: null
     * @param   bool|null   $echo       Overwrite whether the method echos or not.
     *                                  Default: false
     * 
     * @return  string
     * 
     * @static
     * @access  public
     * @since   LRS 3.12.5
     * @since   LBF 0.2.1-beta   Added param `$echo`
     */

    public static function close_span(int $count = 1, ?string $comment = null, ?bool $echo = null): string {
        $element = self::html_tag_close('span', $count, $comment);
        if (!is_null($echo)) {
            $hold = self::temporary_change_echo($echo);
        }
        self::handle_echo($element);
        if (!is_null($echo)) {
            self::restore_origonal_echo($hold);
        }
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

    public static function span_container(array $params = [], string $content = ''): string {
        $hold = self::temporary_change_echo(false);
        $element = self::span($params) . $content . self::close_span();
        self::restore_origonal_echo($hold);
        self::handle_echo($element, $params);
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

    public static function p(array $params = []): string {
        $element = self::html_tag_open('p', $params);
        self::handle_echo($element, $params);
        return $element;
    }


    /**
     * Echo or return a closing `p` (paragraph) statement.
     * 
     * @param   integer         $count      The number of </p> elements to insert.
     *                                      Default: 1
     * @param   string|null     $comment    A bit of text to insert as a comment.
     *                                      Default: null
     * @param   bool|null       $echo       Overwrite whether the method echos or not.
     *                                      Default: false
     * 
     * @return  string
     * 
     * @static
     * @access  public
     * @since   LBF 0.1.5-beta
     * @since   LBF 0.2.1-beta   Added param `$echo`
     */

    public static function close_p(int $count = 1, ?string $comment = null, ?bool $echo = null): string {
        $element = self::html_tag_close('span', $count, $comment);
        if (!is_null($echo)) {
            $hold = self::temporary_change_echo($echo);
        }
        self::handle_echo($element);
        if (!is_null($echo)) {
            self::restore_origonal_echo($hold);
        }
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

    public static function p_container(array $params = [], string $content = ''): string {
        $hold = self::temporary_change_echo(false);
        $element = self::p($params) . $content . self::close_p();
        self::restore_origonal_echo($hold);
        self::handle_echo($element, $params);
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

    public static function heading(int $size, string $content, array $params = []): string {
        $params['text'] = $content;
        $element = self::html_element_container("h{$size}", $params);
        self::handle_echo($element, $params);
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

    public static function form(array $params = [], bool $new_tab = false): string {
        $params['new_tab'] = $new_tab;
        $element = self::html_tag_open('form', $params);
        self::handle_echo($element, $params);
        return $element;
    }


    /**
     * draw a close form statement - `</form>`
     * 
     * @param   bool|null   $echo   Overwrite whether the method echos or not.
     *                              Default: false
     * 
     * @return  string
     * 
     * @static
     * @access  public
     * @since   LRS 3.12.7
     * @since   LBF 0.2.1-beta   Added param `$echo`
     */

    public static function close_form(?bool $echo = null): string {
        $element = '</form>';
        if (!is_null($echo)) {
            $hold = self::temporary_change_echo($echo);
        }
        self::handle_echo($element);
        if (!is_null($echo)) {
            self::restore_origonal_echo($hold);
        }
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
     * 
     * @deprecated  LBF 0.1.6-beta
     */

    public static function link(string $href, ?string $text, array $params = []): string {
        $element = "<a href='{$href}'";

        foreach ($params as $index => $value) {
            if ($index == 'new_tab') {
                $element .= ' ' . Draw::NEW_TAB;
                continue;
            }
            $element .= " {$index}='{$value}'";
        }

        $element .= ">{$text}</a>";
        self::handle_echo($element);
        return $element;
    }


    /**
     * Rebuild of `link`.
     * 
     * @param   array   $params The elements to add to the link
     * 
     * @return  string
     * 
     * @throws  MissingRequiredInput   If $params['text'] missing.
     * 
     * @static
     * @access  public
     * @since   LBF 0.1.6-beta
     */

    public static function a(array $params): string {
        if (!isset($params['href'])) {
            $params['href'] = 'javascript:void(0)';
        }

        if (!isset($params['text'])) {
            throw new MissingRequiredInput("Param 'text' required");
        }

        if (!isset($params['id'])) {
            $params['id'] = Hash::random_id_string();
        }

        $skip_params = [
            'echo', 'text',
        ];

        $item = self::html_element_container('a', $params, $skip_params);

        self::handle_echo($item, $params);
        return $item;
    }


    /**
     * Draw out an image tag.
     * 
     * @param   array   $params The elements to add to the image
     * 
     * @return  string
     * 
     * @throws  MissingRequiredInput   If param `src` is missing.
     * 
     * @static
     * @access  public
     * @since   LRS 3.17.1
     */

    public static function img(array $params = []): string {
        if (!isset($params['src'])) {
            throw new MissingRequiredInput("Path to file not set. Please set param 'src");
        }

        $unit = $params['unit'] ?? 'px';
        $dimensions = ['height', 'width'];
        foreach ($dimensions as $dimension) {
            if (isset($params[$dimension])) {
                if (is_numeric($params[$dimension])) {
                    $params[$dimension] = "{$params[$dimension]}{$unit}";
                }
            }
        }

        $item = self::html_tag_open('img', $params, ['unit']);

        self::handle_echo($item, $params);
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
     * @throws  MissingRequiredInput   If params src & srcdoc are missing.
     * 
     * @static
     * @access  public
     * @since   LRS 3.11.1
     * @since   LRS 3.27.1  Moved from `src/Framework/HTML/Draw.php` to `src/Framework/HTML/HTMLElements.php`
     */

    public static function iframe(array $params): string {
        if (!isset($params['src']) && !isset($params['srcdoc'])) {
            throw new MissingRequiredInput("An iframe must have either the parameter 'src' or 'srcdoc'.");
        }

        if (!isset($params['id'])) {
            $params['id'] = Hash::random_id_string();
        }
        if (!isset($params['loading'])) {
            $params['loading'] = 'lazy';
        }

        if (!isset($params['default_unit'])) {
            $params['default_unit'] = 'px';
        }

        if (!isset($params['width'])) {
            $params['width'] = '100%';
        } else {
            if (is_numeric($params['width'])) {
                $params['width'] .= $params['default_unit'];
            }
        }

        if (!isset($params['height'])) {
            $params['height'] = '300px';
        } else {
            if (is_numeric($params['height'])) {
                $params['height'] .= $params['default_unit'];
            }
        }

        $params['text'] = 'Sorry, your browser does not allow iframe previews.';

        $item = self::html_element_container('iframe', $params, ['default_unit']);

        self::handle_echo($item, $params);
        return $item;
    }


    /**
     * Draw out an ordered list.
     * 
     * ## Possible params
     * 
     * Besides the global HTML attributes, `aria` attributes, `data` attributes,
     * and `event` attributes, the following may be specified in the `$params` array:
     * 
     * | Attribute | Value    | Description |
     * | --------- | -------- | ----------- |
     * | data      | `array`  | The entries to list out. Set each entry as an array with an entry `li` to set attributes for each entry |
     * | reversed  | `bool`   | Specifies that the list order should be reversed (9,8,7...) |
     * | start     | `int`    | Specifies the start value of an ordered list |
     * | type      | `string` | Specifies the kind of marker to use in the list |
     * 
     * ## List types
     * 
     * - `1`
     * - `A`
     * - `a`
     * - `I`
     * - `i`
     * 
     * @see https://www.w3schools.com/tags/tag_ol.asp
     * 
     * @param   array   $params.    Must include `$params['data']` to draw out the list.
     * 
     * @return  string
     * 
     * @throws  MissingRequiredInput if `$params['data']` not set.
     * @throws  MissingRequiredInput if `$params['data']` entries do not correctly list entries indexed as 'li'.
     * @throws  InvalidInput If `$params['data']` is not an array.
     * 
     * @static
     * @access  public
     * @since   0.1.5-beta
     */

    public static function ol(array $params): string {
        $item = self::list('ol', $params);
        self::handle_echo($item, $params);
        return $item;
    }


    /**
     * Draw out an unordered list.
     * 
     * ## Possible params
     * 
     * Besides the global HTML attributes, `aria` attributes, `data` attributes,
     * and `event` attributes, the following may be specified in the `$params` array:
     * 
     * | Attribute | Value    | Description |
     * | --------- | -------- | ----------- |
     * | data      | `array`  | The entries to list out. Set each entry as an array with an entry `li` to set attributes for each entry |
     * 
     * @see https://www.w3schools.com/tags/tag_ul.asp
     * 
     * @param   array   $params.    Must include `$params['data']` to draw out the list.
     * 
     * @return  string
     * 
     * @throws  MissingRequiredInput if `$params['data']` not set.
     * @throws  MissingRequiredInput if `$params['data']` entries do not correctly list entries indexed as 'li'.
     * @throws  InvalidInput If `$params['data']` is not an array.
     * 
     * @static
     * @access  public
     * @since   0.1.5-beta
     */

    public static function ul(array $params): string {
        $item = self::list('ul', $params);
        self::handle_echo($item, $params);
        return $item;
    }


    /**
     * Draw out a list of items, used by methods `ol` and `ul`.
     * 
     * @param   string  $tag    The tag to draw. Either `ol` or `ul`.
     * @param   array   $params The params to parse onto the tag. Must contain an entry `['data']` which
     *                          contains the list of entries to list.
     * 
     * @return  string
     * 
     * @throws  MissingRequiredInput if $params['data'] not set.
     * @throws  MissingRequiredInput if $params['data'] entries do not correctly list entries indexed as 'li'.
     * @throws  InvalidInput If $params['data'] is not an array.
     * 
     * @static
     * @access  private
     * @since   0.1.5-beta
     */

    private static function list(string $tag, array $params): string {
        $item = '';
        if (!isset($params['data'])) {
            echo "<pre>";
            throw new MissingRequiredInput('$params[\'data\'] must be set.');
            echo "</pre>";
        }
        if (!is_array($params['data'])) {
            echo "<pre>";
            throw new InvalidInput('$params[\'data\'] must be an array.');
            echo "</pre>";
        }

        $skip_params = [
            'data',
        ];

        $item .= "<{$tag}";
        foreach ($params as $key => $value) {
            if (in_array($key, $skip_params)) {
                continue;
            }
            switch ($key) {
                case 'reversed':
                    $item .= $value ? ' reversed' : '';
                    break;
                default:
                    $item .= " {$key}='{$value}'";
            }
        }
        $item .= '>';
        foreach ($params['data'] as $value) {
            if (is_array($value)) {
                if (!isset($value['li'])) {
                    echo "<pre>";
                    throw new MissingRequiredInput("Entry param 'li' missing from data entry.");
                    echo "</pre>";
                }
                $item .= "<li";
                foreach ($value as $index => $entry) {
                    if ($index == 'li') {
                        continue;
                    }
                    $item .= " {$index}='{$entry}'";
                }
                $item .= ">{$value['li']}</li>";
            } else {
                $item .= "<li>{$value}</li>";
            }
        }
        $item .= "</{$tag}>";
        return $item;
    }


    /**
     * Draw a `<script>` tag onto the screen.
     * 
     * @param   string  $js     The Javascript to put between the <script> tag. Default: ''
     * @param   array   $src    Any params to inject onto the script tag. Things like `src`, `module` etc. Default: []
     * 
     * @return  string
     * 
     * @static
     * @since   LRS 3.7.6
     * @since   LRS 3.12.5      Moved from `PageElements` to `Framework\HTML\Scripts`.
     *                          Added `$return` & `$param` $src.
     * @since   LBF 0.6.0-beta  Revamped and moved from JS to HTML.
     */

    public static function script(string $js = '', array $params = []): string {
        $params['text'] = $js;
        $element = self::html_element_container("script", $params);
        self::handle_echo($element, $params);
        return $element;
    }
}
