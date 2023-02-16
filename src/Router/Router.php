<?php

namespace LBF\Router;

use Debugger\Debug;
use LBF\HTML\HTML;

class Router {


    private readonly array $path;

    public function __construct(
        public readonly array $config
    ) {
        $this->path = array_values( 
            array_filter( 
                // REDIRECT_URL is generate by apache2
                explode( '/', ( $_SERVER['REDIRECT_URL'] ?? '' ) ), 
                function($value) {
                    return trim($value) !== '';
                }
            )
        );
        // Debug::$display->data($this->path);

        $page_class = 'Web\\' . ucfirst( $this->path[0] ?? 'index' ) . 'Page';

        $page = new $page_class( $this->path );

        $page_method = $this->path[1] ?? $page_class::DEFAULT_PAGE;

        $page->$page_method();

    }


    public function route() {





        $injector = new HTML;

        /**
         * - [x] Load in vendors, Autoloader, Exception Handler, functions, Session
         * - [x] Get all of the config data saved to an array. Parse it to Router.
         * - [ ] Generate the page data and save it to a variable.
         * - [ ] Render the <head> tags, with css & js injected.
         * - [ ] Render the <body> with css & hs injected, then the rendered page data, based on the error code.
         * - [ ] Render the <footer> followed by the final injected css & js.
         * - [ ] Close the </body> tag
         */

        // Insert Header

        // Generate Body

        // Insert Footer




    }


    public static function load_lrs_functions(): void {
        require __DIR__ . '/../Functions/functions.php';
    }
}
