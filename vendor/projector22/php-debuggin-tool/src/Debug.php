<?php

namespace Debugger;

use Debugger\Tools\Js;
use Debugger\Tools\Cmd;
use Debugger\Tools\Log;
use Debugger\Tools\Lorium;
use Debugger\Tools\Timing;
use Debugger\Tools\DisplayData;

/**
 * Class for providing a number of Debugging tools.
 * 
 * @author  Gareth Palmer   @evangeltheology
 * 
 * @version 1.0.0
 */

class Debug {

    /**
     * Object for using timing tools.
     * 
     * @var object  $timer
     * 
     * @access  public
     * @since   1.0.0
     */

    public static object $timer;

    /**
     * Object for using display tools.
     * 
     * @var object  $display
     * 
     * @access  public
     * @since   1.0.0
     */

    public static object $display;

    /**
     * Object for using a lorium generator tools.
     * 
     * @var object  $lorium
     * 
     * @access  public
     * @since   1.0.0
     */

    public static object $lorium;

    /**
     * Object for using a number of terminal tools.
     * 
     * @var object  $cmd
     * 
     * @access  public
     * @since   1.0.1
     */

    public static object $cmd;

    /**
     * Object for using a number of javascript tools.
     * 
     * @var object  $js
     * 
     * @access  public
     * @since   1.0.1
     */

    public static object $js;

    /**
     * Object for using a number of logging tools.
     * 
     * @var object  $log
     * 
     * @access  public
     * @since   1.0.1
     */

    public static object $log;


    /**
     * Constructor method, should be placed in the autoloader or called 
     * before any of the properties above are called.
     * 
     * @access  public
     * @since   1.0.0
     */
    
    public static function __constructStatic() {
        self::$timer   = new Timing;
        self::$display = new DisplayData;
        self::$lorium  = new Lorium;
        self::$cmd     = new Cmd;
        self::$js      = new Js;
        self::$log     = new Log;
    }

}