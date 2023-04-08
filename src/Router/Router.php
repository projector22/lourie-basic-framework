<?php

namespace LBF\Router;

use Exception;
use LBF\App\Config;
use LBF\Auth\Cookie;
use LBF\Config\AppMode;
use LBF\Error\ErrorPage;
use LBF\HTML\HTML;
use LBF\HTML\Injector\PagePositions;
use LBF\Layout\Layout;
use LBF\Router\Routes;
use Throwable;

/**
 * Application for doing the heavy lifting of routing the app, preparing method names dynamically and constructing
 * the page as per the request.
 * 
 * use LBF\Router\Router;
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @since   LBF 0.1.4-beta
 * @since   LBF 0.6.0-beta  Moved from `src/Tools/Router/Router.php` to `src/Router/Router.php` and revamped.
 */

class Router {

    /**
     * Array containing the various parts of the url path.
     * 
     * For example:
     * 
     * `lrs.net/admin/sync/upload`
     * becomes:
     * `
     * [*  0 => 'admin', 1 => 'sync', 2 => 'update']
     * `
     * 
     * @var array   $path
     * 
     * @access  private
     * @since   LBF 0.6.0-beta
     */

    private array $path;

    /**
     * The page which should be routed to, derived from the first part of the URI.
     * 
     * @var string  $page
     * 
     * @readonly
     * @access  private
     * @since   LBF 0.1.4-beta
     */

    private readonly string $page;

    /**
     * The subpage which should be routed to, derived from the second part of the URI.
     * 
     * @var string  $subpage
     * 
     * @readonly
     * @access  private
     * @since   LBF 0.1.4-beta
     */

    private readonly ?string $subpage;

    /**
     * The tab which should be routed to, derived from the third part of the URI.
     * 
     * @var string|null $tab
     * 
     * @readonly
     * @access  private
     * @since   LBF 0.6.0-beta
     */

    private readonly ?string $tab;

    /**
     * The defined route the app is taking.
     * 
     * @var Routes  $route
     * 
     * @readonly
     * @access  private
     * @since   LBF 0.6.0-beta
     */

    private readonly Routes $route;

    /**
     * The HTTP method (POST, GET etc.) being used in the app call.
     * 
     * @var HTTPMethod  $http_method
     * 
     * @readonly
     * @access  private
     * @since   LBF 0.6.0-beta
     */

    private readonly HTTPMethod $http_method;

    /**
     * Whether or not the route is a static route.
     * 
     * @var bool    $static_route
     * 
     * @readonly
     * @access  private
     * @since   LBF 0.6.0-beta
     */

    private readonly bool $static_route;

    /**
     * If a static route, if the static route is a wildcard.
     * 
     * @var bool    $wildcard
     * 
     * @readonly
     * @access  private
     * @since   LBF 0.6.0-beta
     */

    private readonly bool $wildcard;

    /**
     * If a wildcard, set the destinations as tasks. These tasks allow the bypassing of the standard route.
     * 
     * @var array   $tasks  Default: []
     * 
     * @access  private
     * @since   LBF 0.6.0-beta
     */

    private array $tasks = [];


    /**
     * Class contructor. Determines the route and the majority of class properties.
     * 
     * @access  public
     * @since   0.1.4-beta
     */

    public function __construct() {
        $this->route = $this->determine_route();
        Config::load(['route' => $this->route]);
        if ($this->route == Routes::CLI) {
            return;
        }

        if (isset($_SERVER['REDIRECT_URL'])) {
            $redirect_url = $_SERVER['REDIRECT_URL'];
            $this->wildcard = $this->is_wildcard();
            if (isset(Config::static_routes()[$redirect_url]) || $this->wildcard) {
                if ($this->wildcard) {
                    $this->path[0] = Config::static_routes()['/' . $this->path[0] . '/*'];
                } else {
                    $this->path[0] = Config::static_routes()[$redirect_url];
                }

                /**
                 * Handle Static Routes.
                 * Any defined static routes should be defined in the config. They should call
                 * the class desired for loading.
                 * 
                 * @example
                 * ```php
                 * return [
                 *  'static_routes' => [
                 *      '/home/cake' => 'Mouse\Hole',
                 *  ],
                 * ];
                 * ```
                 * 
                 * The above example will call the class `Mouse\Hole`;
                 */
                $this->static_route = true;
            } else {
                $this->static_route = false;
            }
        } else {
            $this->static_route = false;
        }

        $this->http_method = $this->determine_http_method();

        $this->page = $this->path[0]    ?? 'index';
        $this->subpage = $this->path[1] ?? null;
        $this->tab = $this->path[2]     ?? null;
        Config::load([
            'current_page' => [
                'page'    => $this->page,
                'subpage' => $this->subpage,
                'tab'     => $this->tab,
            ],
            'http_method' => $this->http_method,
        ]);
    }


    /**
     * Executes the routing task of the application. Route to as defined.
     * 
     * @access  public
     * @since   LBF 0.6.0-beta
     */

    public function route(): void {
        switch ($this->route) {
            case Routes::CLI:
                break;
            case Routes::API:
                break;
            case Routes::HTTP:
                $this->render_webpage();
                break;
            case Routes::PDF:
                $this->render_webpage();
                break;
            case Routes::DOWNLOAD:
                $this->render_webpage();
                break;
        }
    }


    /**
     * Render the actual called webpages with the appropriate headers, CSS & JS and HTML etc.
     * 
     * @access  public
     * @since   LBF 0.6.0-beta
     */

    public function render_webpage(): void {
        $injector = new HTML;
        $cookie   = new Cookie;
        $layout   = new Layout;

        if ($this->static_route) {
            $page_class = $this->page;
        } else {
            $page_class = 'Web\\' . implode('', array_map(function ($entry) {
                return ucfirst($entry);
            }, explode('-', $this->page))) . 'Page';
        }

        try {
            $page = new $page_class();
        } catch (Throwable $e) {
            if (Config::ENVIRONMENT() == AppMode::DEVELOPEMENT) {
                throw new Exception($e);
            }
            ErrorPage::set_error(404, true);
            $page = new ErrorPage;
        }

        if (!in_array($page_class, $this->tasks)) {
            ob_start();
            $page->construct_page();
            $html = ob_get_clean();
            if (!ErrorPage::skip_check()) {
                if (ErrorPage::get_error_code() !== null) {
                    $page = new ErrorPage;
                    ob_start();
                    $page->construct_page();
                    $html = ob_get_clean();
                }
            }

            $cookie->inject_cookies((Config::ENVIRONMENT() ?? AppMode::DEVELOPEMENT) !== AppMode::DEVELOPEMENT);

            $layout->init_header(
                Config::meta('page_title'),
                Config::meta('description'),
                Config::meta('site_language'),
                Config::meta('block_robots'),
            );
            $layout->set_favicon(Config::meta('favicon'));
            $layout->append_to_header($injector->insert_css(PagePositions::IN_HEAD));
            $layout->append_to_header($injector->insert_js(PagePositions::IN_HEAD));
            $layout->render_header();

            Layout::append_to_body($injector->insert_css(PagePositions::TOP_OF_PAGE), true);
            Layout::append_to_body($injector->insert_js(PagePositions::TOP_OF_PAGE), true);
            Layout::append_to_body("<main>{$html}</main>");
            $layout->render_body();

            $layout->append_to_footer($injector->insert_css(PagePositions::BOTTOM_OF_PAGE));
            $layout->append_to_footer($injector->insert_js(PagePositions::BOTTOM_OF_PAGE));
            $layout->render_footer();
        }
    }


    /**
     * Load up the site functions from LBF function list.
     * 
     * @static
     * @access  public
     * @since   LBF 0.6.0-beta
     */

    public static function load_lrs_functions(): void {
        require __DIR__ . '/../Functions/functions.php';
    }


    /**
     * Determine the route of the current request call.
     * 
     * @return  Routes
     * 
     * @access  private
     * @since   LBF 0.6.0-beta
     */

    private function determine_route(): Routes {
        if (PHP_SAPI == 'cli') {
            return Routes::CLI;
        }

        $this->path = $this->set_uri_path();

        if ($this->route_is_api()) {
            return Routes::API;
        }
        if (($this->path[0] ?? null) == 'pdf') {
            return Routes::PDF;
        }
        if (($this->path[0] ?? null) == 'download') {
            return Routes::DOWNLOAD;
        }
        return Routes::HTTP;
    }


    /**
     * Returns if the current route is an API call.
     * 
     * @return bool
     * 
     * @access  private
     * @since   LBF 0.6.0-beta
     */

    private function route_is_api(): bool {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            return true;
        }
        // Detect if a fetch is sent.
        return false;
    }


    /**
     * Determine which HTTP method used when loading or calling the page.
     * 
     * @return  HTTPMethod
     * 
     * @access  private
     * @since   LBF 0.6.0-beta
     */

    private function determine_http_method(): HTTPMethod {
        if (isset($_POST) && count($_POST) > 0) {
            return HTTPMethod::POST;
        } else {
            return match ($_SERVER['REQUEST_METHOD']) {
                'GET'    => HTTPMethod::GET,
                'POST'   => HTTPMethod::POST,
                'PUT'    => HTTPMethod::PUT,
                'DELETE' => HTTPMethod::DELETE,
                default  => HTTPMethod::GET,
            };
        }
    }


    /**
     * Return the page `page` value.
     * 
     * @return  string
     * 
     * @access  public
     * @since   LBF 0.6.0-beta
     */

    public function get_page(): string {
        return $this->page;
    }


    /**
     * Return the page `subpage` value.
     * 
     * @return  string
     * 
     * @access  public
     * @since   LBF 0.6.0-beta
     */

    public function get_subpage(): string {
        return $this->subpage;
    }


    /**
     * Return the page `tab` value.
     * 
     * @return  string
     * 
     * @access  public
     * @since   LBF 0.6.0-beta
     */

    public function get_tab(): string {
        return $this->tab;
    }


    /**
     * Add single task to `$this->tasks`.
     * 
     * @param   string  $task    The task to add.
     * 
     * @access  public
     * @since   LBF 0.6.0-beta
     */

    public function add_task(string $task): void {
        $this->tasks[] = $task;
    }


    /**
     * Add multiple tasks at once to `$this->tasks`.
     * 
     * @param   array   $tasks  The list of tasks to add.
     * 
     * @access  public
     * @since   LBF 0.6.0-beta
     */

    public function add_multiple_tasks(array $tasks): void {
        $this->tasks = array_merge($tasks, $this->tasks);
    }


    /**
     * Return the `$this->path` value as an array.
     * 
     * @return  array
     * 
     * @access  private
     * @since   LBF 0.6.0-beta
     */

    private function set_uri_path(): array {
        return array_values(
            array_filter(
                // REDIRECT_URL is generate by apache2
                explode('/', ($_SERVER['REDIRECT_URL'] ?? '')),
                function ($value) {
                    return trim($value) !== '';
                }
            )
        );
    }


    /**
     * Returns the route used by the app.
     * 
     * @return  Routes
     * 
     * @access  public
     * @since   LBF 0.6.0-beta
     */

    public function get_route(): Routes {
        return $this->route;
    }


    /**
     * Returns the HTTP Method used by the app.
     * 
     * @return  HTTPMethod
     * 
     * @access  public
     * @since   LBF 0.6.0-beta
     */

    public function get_http_method(): HTTPMethod {
        return $this->http_method;
    }


    /**
     * Return if the static route is a wildcard.
     * 
     * @return  bool
     * 
     * @access  private
     * @since   LBF 0.6.0-beta
     */

    private function is_wildcard(): bool {
        return isset(Config::static_routes()['/' . $this->path[0] . '/*']);
    }
}
