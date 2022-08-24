<?php

use Debugger\Debug;

/**
 * See this page for examples of how to autoload the Debugger
 */
require 'common.php';

if ( PHP_OS == 'Linux' ) {
    Debug::$cmd->show_output( 'ls' );
    echo "<br><br>";
    Debug::$cmd->show_output( 'date' );
} else {
    Debug::$cmd->show_output( 'dir' );
    echo "<br><br>";
    Debug::$cmd->show_output( 'time /t' );
}