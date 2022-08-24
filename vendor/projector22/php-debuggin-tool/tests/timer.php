<?php

use Debugger\Debug;

/**
 * See this page for examples of how to autoload the Debugger
 */
require 'common.php';

Debug::$timer->start();

sleep( 1 );

Debug::$timer->timestamp();
sleep( 2 );

Debug::$timer->timestamp( 'Second Timestamp' );
sleep( 1 );

Debug::$timer->timestamp();
sleep( 3 );

Debug::$timer->timestamp( 'Last Timestamp' );

Debug::$timer->end( true );
