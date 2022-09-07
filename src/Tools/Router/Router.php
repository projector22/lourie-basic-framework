<?php

namespace LBF\Tools\Router;

/**
 * Perform routing tasks for an application. Requires the parsing of an associative array with 
 * which indicates where uris should be parsed to.
 * 
 * The key indicates the URI, the value indicates class to be called for routing. Use wildcards
 * in the key to catch all within a page.
 * 
 * Static routes will be parsed before wildcard ones.
 * 
 * ### Example of Routes Array
 * 
 * ```php
 * $routes = [
 *  '/*'           => 'app\web\Home',
 *  '/admin/*'     => 'app\web\AdminPage',
 *  '/admin/users' => 'app\web\UserAccounts',
 * ];
 * ```
 * 
 * use LBF\Tools\Router\Router;
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @since   LBF 0.1.4-beta
 */

class Router {

    /**
     * The page which should be routed to, derived from the first part of the URI.
     * 
     * @var string  $page
     * 
     * @readonly
     * @access  private
     * @since   0.1.4-beta
     */

    private readonly string $page;

    /**
     * The subpage which should be routed to, derived from the second part of the URI.
     * 
     * @var string  $subpage
     * 
     * @readonly
     * @access  private
     * @since   0.1.4-beta
     */

    private readonly string $subpage;

    /**
     * The class to be called. Gotten from `$this->routing_data` if set.
     * 
     * @var string  $class
     * 
     * @readonly
     * @access  private
     * @since   0.1.4-beta
     */

    private readonly string $class;

    /**
     * The status code of the routing.
     * 
     * ### Common codes
     * 
     * | Code | Explanation |
     * | ---- | ----------- |
     * | 0    | Nothing has happened yet. |
     * | 200  | `200 OK`. The request succeeded. |
     * | 400  | `400 Bad Request`. The server cannot or will not process the request due to something that is perceived to be a client error (e.g., malformed request syntax, invalid request message framing, or deceptive request routing). |
     * | 401  | `401 Unauthorized`. Although the HTTP standard specifies "unauthorized", semantically this response means "unauthenticated". That is, the client must authenticate itself to get the requested response. |
     * | 403  | `403 Forbidden`. The client does not have access rights to the content; that is, it is unauthorized, so the server is refusing to give the requested resource. Unlike 401 Unauthorized, the client's identity is known to the server. |
     * | 404  | `404 Not Found`. The server can not find the requested resource. In the browser, this means the URL is not recognized. In an API, this can also mean that the endpoint is valid but the resource itself does not exist. Servers may also send this response instead of 403 Forbidden to hide the existence of a resource from an unauthorized client. This response code is probably the most well known due to its frequent occurrence on the web. |
     * 
     * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Status
     * 
     * @var int $status_code
     * 
     * @access  private
     * @since   0.1.4-beta
     */

    private int $status_code = 0;
    
    /**
     * A set task. If applicable. If not applicable, set to `null`. Set by parsing `$_POST['task']` or `$_GET['task']`.
     * 
     * @var string|null $task
     * 
     * @readonly
     * @access  private
     * @since   0.1.4-beta
     */

    private readonly ?string $task;


    /**
     * Class constructor. Does most of the routing immediately when the class is called.
     * 
     * @param   array   $routing_data   The data which tells the class how to route.
     * 
     * @access  public
     * @since   0.1.4-beta
     */

    public function __construct(

        /**
         * The data which tells the class how to route.
         * 
         * ### Example of Routes Array
         * 
         * ```php
         * $routes = [
         *  '/*'           => 'app\web\Home',
         *  '/admin/*'     => 'app\web\AdminPage',
         *  '/admin/users' => 'app\web\UserAccounts',
         * ];
         * ```
         * 
         * @var array   $routing_data
         * 
         * @readonly
         * @access  private
         * @since   0.1.4-beta
         */

        private readonly array $routing_data
    ) {
        /**
         * Set $this->task.
         */
        $this->task = $_POST['task'] ?? $_GET['task'] ?? null;

        /**
         * Filter out subfolders if relevant, if the page is something like `example.com/page/page/subpage`.
         */
        $self = explode( '/', $_SERVER['PHP_SELF'] );
        $self = array_map( function ( $item ) {
            if ( str_contains( $item, '.php' ) ) {
                return '';
            }
            return $item;
        }, $self );
        $self = rtrim( implode( '/', $self ), '/' );

        /**
         * Filter out entries in the URI which are behind a `#` or `?`.
         */
        $request_uri = explode( '#', $_SERVER['REQUEST_URI'] )[0];
        $request_uri = explode( '?', $request_uri )[0];
        $set_route = str_replace( $self, '', $request_uri );

        /**
         * Set `$this->page` and `$this->subpage`.
         */
        $route = explode( '/', $set_route );
        $this->page    = $route[1] ?? '';
        $this->subpage = $route[2] ?? '';

        /**
         * Perform the routing based on `$this->routing_data`.
         */
        if ( isset( $this->routing_data[$set_route] ) ) {
            // If directly specified
            $this->class       = $this->routing_data[$set_route];
            $this->status_code = 200;
        } else if ( $set_route == '/' && isset( $this->routing_data['/*'] ) ) {
            // If on the home page and a wildcard set.
            $this->class       = $this->routing_data['/*'];
            $this->status_code = 200;
        } else if ( isset( $this->routing_data["/{$route[1]}/*"] ) ) {
            // If Wildcard used.
            $this->class       = $this->routing_data["/{$route[1]}/*"];
            $this->status_code = 200;
        } else {
            // Throw 404
            $this->status_code = 404;
        }
    }


    /**
     * Return the page which should be routed to, derived from the first part of the URI. 
     * 
     * @return  string
     * 
     * @access  public
     * @since   0.1.4-beta
     */

    public function get_page(): string {
        return $this->page;
    }


    /**
     * Return the subpage which should be routed to, derived from the first part of the URI. 
     * 
     * @return  string
     * 
     * @access  public
     * @since   0.1.4-beta
     */

    public function get_subpage(): string {
        return $this->subpage;
    }


    /**
     * Return the class to be called if set.
     * 
     * @return  string
     * 
     * @access  public
     * @since   0.1.4-beta
     */

    public function get_class(): string {
        return $this->class;
    }


    /**
     * Return the status code of the routing once the process is complete.
     * 
     * ### Common codes
     * 
     * | Code | Explanation |
     * | ---- | ----------- |
     * | 0    | Nothing has happened yet. |
     * | 200  | `200 OK`. The request succeeded. |
     * | 400  | `400 Bad Request`. The server cannot or will not process the request due to something that is perceived to be a client error (e.g., malformed request syntax, invalid request message framing, or deceptive request routing). |
     * | 401  | `401 Unauthorized`. Although the HTTP standard specifies "unauthorized", semantically this response means "unauthenticated". That is, the client must authenticate itself to get the requested response. |
     * | 403  | `403 Forbidden`. The client does not have access rights to the content; that is, it is unauthorized, so the server is refusing to give the requested resource. Unlike 401 Unauthorized, the client's identity is known to the server. |
     * | 404  | `404 Not Found`. The server can not find the requested resource. In the browser, this means the URL is not recognized. In an API, this can also mean that the endpoint is valid but the resource itself does not exist. Servers may also send this response instead of 403 Forbidden to hide the existence of a resource from an unauthorized client. This response code is probably the most well known due to its frequent occurrence on the web. |
     * 
     * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Status
     * 
     * @return  int
     * 
     * @access  public
     * @since   0.1.4-beta
     */

    public function get_status_code(): int {
        return $this->status_code;
    }


    /**
     * Get the task if set.
     * 
     * @return  string
     * 
     * @access  public
     * @since   0.1.4-beta
     */

    public function get_task(): string {
        return $this->task;
    }

}