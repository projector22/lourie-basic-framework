<?php

use Debugger\Debug;

/**
 * See this page for examples of how to autoload the Debugger
 */
require 'common.php';

// String
Debug::$log->to_file( 'This is your log' );

// Array
$arr = ['Cheese', 'Cake', 'Mouse' => 'trap'];
Debug::$log->to_file( $arr );

// Object
$obj = new stdClass();
$obj->objCheese = 'Cake';
$obj->objMouse = 'Trap';
Debug::$log->to_file( $obj ); // Object

echo "Please check in <i>php-debuggin-tools/logs/dev.log</i> for the logs you've just written.";