<?php

use Debugger\Debug;

/**
 * See this page for examples of how to autoload the Debugger
 */
require 'common.php';

Debug::$display->data( [['cheese' => 'a', 'cake' => 'b'], ['c', 'd']] );

Debug::$display->data( 'The big black cat', [['cheese' => 'a', 'cake' => 'b'], ['c', 'd']] );

Debug::$display->table( [['cheese' => 'a', 'cake' => 'b'], ['c', 'd']] );
Debug::$display->table( ['Cheese', 'Cake'] );

Debug::$display->page_data();
