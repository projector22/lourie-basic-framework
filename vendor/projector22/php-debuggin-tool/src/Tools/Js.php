<?php

namespace Debugger\Tools;

/**
 * A set of tools that can be called executing javascript commands.
 * 
 * @author  Gareth Palmer  @evangeltheology
 * 
 * @since   1.0.2
 */

class Js {

    /**
     * Add some JS to detect what key has been pressed
     * 
     * @link    https://css-tricks.com/snippets/javascript/javascript-keycodes/
     * 
     * @access  public
     * @since   1.0.2
     */

    public static function detect_keystroke() {
        echo "<script>document.addEventListener('keydown', function(event) {console.log(event.which);})</script>";
    }

}