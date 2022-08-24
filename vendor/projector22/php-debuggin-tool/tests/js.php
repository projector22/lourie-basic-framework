<?php

use Debugger\Debug;

/**
 * See this page for examples of how to autoload the Debugger
 */
require 'common.php';

echo "<h3>Open the console window (F12) to see detected keystrokes</h3>";
Debug::$js->detect_keystroke();