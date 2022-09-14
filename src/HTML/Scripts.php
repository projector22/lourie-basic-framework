<?php

namespace LBF\HTML;

use LBF\Auth\Hash;

/**
 * This class is to draw out various inline Javascript elements withing <script> tags.
 * 
 * use LBF\HTML\Scripts;
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @since   LRS 3.12.5
 * @since   LRS 3.28.0      Seperated out of `Lourie Registration System` into `Lourie Basic Framework`.
 *                          Namespace changed from `Framework` to `LBF`.
 * @since   LBF 0.1.6-beta  Added extension `HTMLMeta`.
 */

class Scripts extends HTMLMeta {

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
     * @access  public
     * @since   LRS 3.12.5
     */

    public static function alert( string $text ): void {
        self::script( "alert('{$text}')" );
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
        self::script_loader( 'src/js/thirdparty/clipboard.min.js' );
        self::script( "const clipboard = new ClipboardJS('{$id}');" );
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
        if ( $do_nothing ) {
            self::script( "var input = document.getElementById('$input');
            input.addEventListener('keyup', function(event) {
                if (event.keyCode === $keycode) {
                    event.preventDefault();
                }
            });" );

        } else {
            self::script( "var input = document.getElementById('$input');
            input.addEventListener('keyup', function(event) {
                if (event.keyCode === $keycode) {
                    event.preventDefault();
                    document.getElementById('$button').click();
                }
            });" );
        }
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
        self::script( "window.addEventListener('keydown', function (event) {
            if (event.keyCode === $keycode) {
                event.preventDefault();
                return false;
            }
        });" );
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
        self::script( "window.addEventListener('load', function () {
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
        });" );
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
        self::script_module( "import { $desired_function } from './src/js/lib/keyboard_shortcuts.js';
        document.addEventListener('keydown', function(event) {
            $desired_function(event);
        });" );
    }


    /**
     * Draw the shift multiselect onLoad script
     * 
     * @static
     * @access  public
     * @since   LRS 3.6.4
     * @since   LRS 3.12.5  Moved from PageElements to Framework\HTML\Scripts
     */

    public static function insert_shift_multiselect(): void {
        $id = 'sm' . Hash::random_id_string();
        self::script_module( "
import Table_Filter from './vendor/projector22/lourie-basic-framework/src/js/table_filters.js';
const $id = new Table_Filter;
$id.shift_multiselect();" );
    }
}