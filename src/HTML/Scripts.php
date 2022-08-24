<?php

namespace LBS\HTML;

use App\Db\Data\ScheduleData;
use App\Db\Data\GeneralConfigData;
use LBS\HTML\HTML;

/**
 * This class is to draw out various inline Javascript elements withing <script> tags.
 * 
 * use LBS\HTML\Scripts;
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @since   3.12.5
 */

class Scripts {

    /**
     * Whether or not to echo or return the html element
     * 
     * @var    boolean     $echo   Default: true
     * 
     * @access  public
     * @since   3.12.5
     */

    public static bool $echo = true;

    /**
     * Draw a script onto the screen
     * 
     * @param   string  $script Any kind of JavaScript or JS function call
     * @param   string  $src    An src element on the <script> tag
     *                          Default: null
     * 
     * @return  string  The formed script element
     * 
     * @since   3.7.6
     * @since   3.12.5  Moved from PageElements to LBS\HTML\Scripts. Added return & param $src
     */

    public static function script( string $script, ?string $src = null ) {
        $add_src = is_null( $src ) ? '' : " src='{$src}'";
        $element = "<script{$add_src}>{$script}</script>";
        if ( self::$echo ) {
            echo $element;
        } else {
            return $element;
        }
    }


    /**
     * Draw a script onto the screen, with type = module
     * 
     * @param   string  $script Any kind of JavaScript or JS function call
     * @param   string  $src    An src element on the <script> tag
     *                          Default: null
     * 
     * @return  string  The formed script element
     * 
     * @since   3.13.0
     */

    public static function script_module( $script, $src = null ) {
        $add_src = is_null( $src ) ? '' : " src='{$src}'";
        $element = "<script{$add_src} type='module'>{$script}</script>";
        if ( self::$echo ) {
            echo $element;
        } else {
            return $element;
        }
    }


    /**
     * A method of drawing a script tag with an src element to load in JS files
     * 
     * @param   string  $src    Path to the JS file
     * 
     * @return  string  The  formed script element
     * 
     * @since   3.12.5
     */

    public static function script_loader( string $src ) {
        $element = "<script src='{$src}'></script>";
        if ( self::$echo ) {
            echo $element;
        } else {
            return $element;
        }
    }


    /**
     * A method of drawing a script tag, with the modal type and with an src element to load in JS files
     * 
     * @param   string  $src    Path to the JS file
     * 
     * @return  string  The  formed script element
     * 
     * @since   3.13.0
     */

    public static function script_module_loader( string $src ) {
        $element = "<script src='{$src}' type='module'></script>";
        if ( self::$echo ) {
            echo $element;
        } else {
            return $element;
        }
    }


    /**
     * Draw an inline Javascript Alert
     * 
     * @param   string  $text   The text of the alert
     * 
     * @access  public
     * @since   3.12.5
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
     * @access  public
     * @since   3.1.0
     * @since   3.12.5  Moved from PageElements to LBS\HTML\Scripts
     */

    public static function clipboardButton( string $id = '.btn' ) {
        self::script_loader( 'src/js/thirdparty/clipboard.min.js' );
        self::script( "const clipboard = new ClipboardJS('{$id}');" );
    }


    /**
     * This draws out the daily routine time, by pulling in data from dailyroutine.json and generates the Javascript
     * 
     * Originally built by Kalden Swart
     * @link    https://github.com/InterferenceObject/
     * 
     * @access  public
     * @since   3.1.0
     * @since   3.8.0   Reworked to work with it's own JS file (src/js/routine.js)
     * @since   3.12.0  Reworked to pull data from the database rather than bin/json/routine.json
     * @since   3.12.5  Moved from PageElements to LBS\HTML\Scripts
     */

    public static function daily_routine() {
        $config = new GeneralConfigData;
        $config->set_index_data_by( 'config_key' );
        $config->select_all( ['config_group' => 'routine'] );
        $num_of_periods  = $config->data['number_of_periods']->config_value;
        $weeks_per_cycle = $config->data['weeks_per_cycle']->config_value;
        $am_register = isset( $config->data['am_registration'] ) ? ', ' . $config->data['am_registration']->config_value : null;
        $pm_register = isset( $config->data['pm_registration'] ) ? ', ' . $config->data['pm_registration']->config_value : null;

        $schedule_data = new ScheduleData( true );
        $schedule = $schedule_data->sort_data_week_day_period();
        $json = json_encode( $schedule );
        HTML::div_container( ['class' => 'routine_timer', 'id' => 'routine_container'] );
        self::script_module( "import Routine from './src/js/lib/routine.js';
        const daily_routine = new Routine($json, {$num_of_periods}{$am_register}{$pm_register});
        window.setInterval(function refresh() {
            daily_routine.timer_element();
        }, 0o500);" ); // the number is 0500 but the o is required in strict mode
    }


    /**
>>>>>>> 363b49f (Fixed namespace)
     * Draw out the JS to change default behaviour of a button press
     * 
     * @param   string  $input      The id of the item to be monitored
     * @param   string  $button     The button to be clicked when the appropriate button is pressed
     * @param   int     $keycode    The key to be monitored, Default: 13 (Enter)
     * @param   boolean $do_nothing Instruction to do nothing when the key is pressed
     * 
     * @access  public
     * @since   3.2.2
     * @since   3.4.5   $do_nothing added
     * @since   3.12.5  Moved from PageElements to LBS\HTML\Scripts
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
     * @access  public
     * @since   3.2.2
     * @since   3.12.5  Moved from PageElements to LBS\HTML\Scripts
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
     * @access  public
     * @since   3.4.1
     * @since   3.7.0   Added @param $function_name - Reworked logic to use MutationObserver
     * @since   3.7.4   Added @param $content
     * @since   3.12.5  Moved from PageElements to LBS\HTML\Scripts
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
     * @access  public
     * @since   3.6.0
     * @since   3.12.5  Moved from PageElements to LBS\HTML\Scripts
     */
    
    public static function insert_keyboard_shortcuts( string $desired_function ): void {
        /**
         * @see src\js\lib\keyboard_shortcuts.js
         * -> Keyboard shortcut functions should all be in this library
         */
        self::script_module( "import { $desired_function } from './src/js/lib/keyboard_shortcuts.js';
        document.addEventListener('keydown', function(event) {
            $desired_function(event);
        });" );
    }


    /**
     * Draw the shift multiselect onLoad script
     * 
     * @access  public
     * @since   3.6.4
     * @since   3.12.5  Moved from PageElements to LBS\HTML\Scripts
     */

    public static function insert_shift_multiselect(): void {
        $id = 'sm' . random_id_string();
        self::script_module( "
import Table_Filter from './src/js/lib/table_filters.js';
const $id = new Table_Filter;
$id.shift_multiselect();" );
    }
}