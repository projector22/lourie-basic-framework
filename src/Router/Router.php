<?php

namespace LBF\Router;

use LBF\App\Config;
use LBF\HTML\HTML;

class Router {

    private readonly array $path;

    private readonly string $page;

    private readonly string $subpage;

    private readonly string $tab;



    public function __construct() {
        $this->path = array_values( 
            array_filter( 
                // REDIRECT_URL is generate by apache2
                explode( '/', ( $_SERVER['REDIRECT_URL'] ?? '' ) ), 
                function($value) {
                    return trim($value) !== '';
                }
            )
        );
        $this->page = $this->path[0] ?? 'index';
        $this->subpage = $this->path[1] ?? '';
        $this->tab = $this->path[2] ?? '';
        Config::load( ['current_page' => [
            'page'    => $this->page,
            'subpage' => $this->subpage,
            'tab'     => $this->tab,
        ]] );
        $this->render_webpage();

    }


    public function render_webpage(): void {
        $page = $this->page == 'home' ? 'index' : $this->page;

        $page_class = 'Web\\' . ucfirst( $page ) . 'Page';

        $page = new $page_class( $this->path );
        ob_start();
        $code = $page->construct_page();
        if ( $code == 200 ) {
            $html = ob_get_clean();
        }
        echo $html;
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


    public function get_page(): string {
        return $this->page;
    }
    public function get_subpage(): string {
        return $this->subpage;
    }
    public function get_tab(): string {
        return $this->tab;
    }
}
