<?php

namespace LBF\HTML;

use LBF\Auth\Hash;
use LBF\HTML\HTMLMeta;
use LBF\HTML\Injector\PagePositions;

/**
 * This class is to draw out various inline Javascript elements withing <script> tags.
 * 
 * use LBF\HTML\JS;
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @since   LRS 3.12.5
 * @since   LRS 3.17.2      Seperated out as a shortcut class to `src/HTML/Scripts.php`.
 * @since   LRS 3.28.0      Seperated out of `Lourie Registration System` into `Lourie Basic Framework`.
 *                          Namespace changed from `Framework` to `LBF`.
 * @since   LBF 0.1.6-beta  Added extension `HTMLMeta`.
 * @since   LBF 0.6.0-beta  Merged with `src/HTML/Scripts.php`, removing the need for a shortcut.
 */

class JS extends HTMLMeta {

    /**
     * Draw a script onto the screen
     * 
     * @param   string      $script Any kind of JavaScript or JS function call
     * @param   string|null $src    An src element on the <script> tag
     *                              Default: null
     * 
     * @return  string
     * 
     * @static
     * @since   LRS 3.7.6
     * @since   LRS 3.12.5  Moved from PageElements to Framework\HTML\Scripts. Added return & param $src
     * 
     * @deprecated  LBF 0.6.0-beta
     */

     public static function script( string $script, ?string $src = null ): string {
        $add_src = is_null( $src ) ? '' : " src='{$src}'";
        $element = "<script{$add_src}>{$script}</script>";
        self::handle_echo( $element );
        return $element;
    }


    /**
     * Draw a script onto the screen, with type = module
     * 
     * @param   string      $script Any kind of JavaScript or JS function call
     * @param   string|null $src    An src element on the <script> tag
     *                              Default: null
     * 
     * @return  string
     * 
     * @static
     * @access  public
     * @since   LRS 3.13.0
     * 
     * @deprecated  LBF 0.6.0-beta
     */

    public static function script_module( string $script, ?string $src = null ): string {
        $add_src = is_null( $src ) ? '' : " src='{$src}'";
        $element = "<script{$add_src} type='module'>{$script}</script>";
        self::handle_echo( $element );
        return $element;
    }


    /**
     * A method of drawing a script tag with an src element to load in JS files
     * 
     * @param   string  $src    Path to the JS file
     * 
     * @return  string
     * 
     * @static
     * @access  public
     * @since   LRS 3.12.5
     * 
     * @deprecated  LBF 0.6.0-beta
     */

    public static function script_loader( string $src ): string {
        $element = "<script src='{$src}'></script>";
        self::handle_echo( $element );
        return $element;
    }


    /**
     * A method of drawing a script tag, with the modal type and with an src element to load in JS files
     * 
     * @param   string  $src    Path to the JS file
     * 
     * @return  string
     * 
     * @static
     * @access  public
     * @since   LRS 3.13.0
     * 
     * @deprecated  LBF 0.6.0-beta
     */

    public static function script_module_loader( string $src ): string {
        $element = "<script src='{$src}' type='module'></script>";
        self::handle_echo( $element );
        return $element;
    }


    /**
     * Draw an inline Javascript Alert.
     * 
     * @param   string  $text   The text of the alert
     * 
     * @static
     * @access  public
     * @since   LRS 3.12.5
     */

    public static function alert( string $text ): void {
        HTML::inject_js( <<<JS
        alert('{$text}');
        JS );
    }


    /**
     * Adds the JS elements to copy data to clipboard
     * 
     * Credit to:
     * @link https://clipboardjs.com/
     * 
     * @static
     * @access  public
     * @since   LRS 3.1.0
     * @since   LRS 3.12.5  Moved from PageElements to Framework\HTML\Scripts
     */

    public static function clipboardButton( string $id = '.btn' ): void {
        HTML::inject_js( <<<JS
        const clipboard = new ClipboardJS('{$id}');
        JS );
    }


    /**
     * Draw out the JS to change default behaviour of a button press
     * 
     * @param   string  $input      The id of the item to be monitored
     * @param   string  $button     The button to be clicked when the appropriate button is pressed
     * @param   int     $keycode    The key to be monitored, Default: 13 (Enter)
     * @param   boolean $do_nothing Instruction to do nothing when the key is pressed
     * 
     * @static
     * @access  public
     * @since   LRS 3.2.2
     * @since   LRS 3.4.5   $do_nothing added
     * @since   LRS 3.12.5  Moved from PageElements to Framework\HTML\Scripts
     */

    public static function change_button_behaviour( string $input, string $button, int $keycode = 13, bool $do_nothing = false ): void {
        $id = Hash::random_id_string(4);
        HTML::inject_js(<<<JS
            const input{$id} = document.getElementById('{$input}');
            input{$id}.addEventListener('keyup', event => {
                if (event.keyCode === $keycode) {
                    event.preventDefault();
                    if (!$do_nothing) {
                        document.getElementById('$button').click();
                    }
                }
            });
        JS, PagePositions::BOTTOM_OF_PAGE );
    }


    /**
     * Draw out the JS to stop the default behaviour of a defined key press
     * 
     * @param   int  $keycode    The key to be monitored, Default: 13 (Enter)
     * 
     * @static
     * @access  public
     * @since   LRS 3.2.2
     * @since   LRS 3.12.5  Moved from PageElements to Framework\HTML\Scripts
     */

    public static function block_default_button_press( int $keycode = 13 ): void {
        HTML::inject_js( <<<JS
        window.addEventListener('keydown', function (event) {
            if (event.keyCode === $keycode) {
                event.preventDefault();
                return false;
            }
        });
        JS );
    }


    /**
     * Draw out the scripting elements which will hide something after an interval
     * 
     * @param   string  $id             ID of element to hide
     * @param   string  $function_name  The name of the function to generate - use if multiple of these are needed
     *                                  Default: 'hide_element
     * @param   integer $time           The time to delay
     *                                  Default: 1200
     * @param   string  $content        The content to put in the area being hidden.
     *                                  Default: ''
     * 
     * @static
     * @access  public
     * @since   LRS 3.4.1
     * @since   LRS 3.7.0   Added @param $function_name - Reworked logic to use MutationObserver
     * @since   LRS 3.7.4   Added @param $content
     * @since   LRS 3.12.5  Moved from PageElements to Framework\HTML\Scripts
     */

    public static function hide_element_after_time( string $id, string $function_name = 'hide_element', int $time = 1200, string $content = '' ): void {
        HTML::inject_js(<<<JS
            window.addEventListener('load', () => {
                const element = document.getElementById('$id');
                const MutationObserver = window.MutationObserver || window.WebKitMutationObserver || window.MozMutationObserver;
                const observer = new MutationObserver($function_name);
                observer.observe(element, {
                    childList: true
                });
                function $function_name() {
                    setTimeout(function() {
                    element.innerHTML = '$content';
                    }, $time);
                }
            });
        JS, PagePositions::BOTTOM_OF_PAGE );
    }


    /**
     * Insert a listener for keyboard shortcuts
     * 
     * @param   string  $desired_function   The name of the desired Javascript function
     * 
     * @static
     * @access  public
     * @since   LRS 3.6.0
     * @since   LRS 3.12.5  Moved from PageElements to Framework\HTML\Scripts
     */

    public static function insert_keyboard_shortcuts( string $desired_function ): void {
        /**
         * @see src\js\lib\keyboard_shortcuts.js
         * -> Keyboard shortcut functions should all be in this library
         * 
         * @todo    Make this universal for library & LRS.
         */
        HTML::inject_js( <<<JS
        import { $desired_function } from 'lrs-keyboard-shortcuts';
        JS );
        HTML::inject_js( <<<JS
        document.addEventListener('keydown', function(event) {
            $desired_function(event);
        });
        JS );
    }


    /**
     * Draw the shift multiselect onLoad script
     * 
     * @static
     * @access  public
     * @since   LRS 3.6.4
     * @since   LRS 3.12.5  Moved from PageElements to Framework\HTML\Scripts
     * @since   LBF 0.6.0-beta  Revamped to use HTML::inject_js
     */

    public static function insert_shift_multiselect(): void {
        $id = 'sm' . Hash::random_id_string();
        HTML::inject_js(<<<JS
            import Table_Filter from 'lrs-table-filters';
        JS);
        HTML::inject_js(<<<JS
            const $id = new Table_Filter;
            $id.shift_multiselect();
        JS);
    }

}