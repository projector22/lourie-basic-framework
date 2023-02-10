<?php

namespace LBF\Router;

use LBF\HTML\HTML;

class Router {
    public function __construct() {


    }


    public function route() {
        $injector = new HTML;

        /**
         * - [ ] Load in vendors, Autoloader, Exception Handler, functions, Session
         * - [ ] Get all of the config data saved to an array. Parse it to Router.
         * - [ ] Generate the page data and save it to a variable.
         * - [ ] Render the <head> tags, with css & js injected.
         * - [ ] Render the <body> with css & hs injected, then the rendered page data, based on the error code.
         * - [ ] Render the <footer> followed by the final injected css & js.
         * - [ ] Close the </body> tag
         */

    }


    public static function load_lrs_functions(): void {
        require __DIR__ . '/../Functions/functions.php';
    }
}
