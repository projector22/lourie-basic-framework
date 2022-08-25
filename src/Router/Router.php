<?php

namespace LBS\Router;

use LBS\Auth\Api;
use App\Auth\Permissions;
use LBS\Auth\Api;

/**
 * Handle the routing of requests throughout the app.
 * 
 * use LBS\Router\Router;
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @since   3.15.0
 */

class Router {

    /**
     * The page context to route to
     * 
     * @var string  $page_to_load
     * 
     * @access  private
     * @since   3.15.0
     */

    private $page_to_load;

    /**
     * An response code to requests
     * Based on standard http response codes
     * 
     * If 0 - nothing has happened yet
     * 
     * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Status
     * 
     * @var integer $response_code  Default: 0
     * 
     * @access  public
     * @since   3.15.0
     */

    public $response_code = 0;

    /**
     * Based on the request type, this will route the path of the page appropriately
     * 
     * @var string  $path   Default: 'http'
     * 
     * @access  public
     * @since   3.15.0
     * 
     * @todo    Make more general
     */

    public $path = 'http';


    /**
     * Constructor method, things to do when the class is loaded
     * 
     * @access  public
     * @since   3.15.0
     */

    public function __construct( $route_to_page, $type ) {
        $this->path = $type;
        $this->page_to_load = implode( '', array_map( 'ucfirst', explode( '-', $route_to_page ) ) );

        $this->check_file_exists();
        if ( $type !== 'maintenance' && $type !== 'dev-tools' ) {
            /**
             * Perform site permissions checks
             * 
             * @since   3.15.0
             */
            $permits = new Permissions;
            /**
             * @todo
             * Move back to apps
             */
            $permits->check_user_page_permission();
            if ( $permits->permission_error ) {
                $this->response_code = 404;
            } else if ( !$permits->can_access ) {
                $this->response_code = 403;
            }
            if ( !is_null( Api::get_key() ) && $this->path == 'http' ) {
                $this->response_code = 401;
            }
            if ( $this->path == 'docs' ) {
                $this->response_code = 200;
            }
        }

        if ( $this->response_code == 0 ) {
            $this->response_code = 200;
        }
    }


    /**
     * Perform the actual routing.
     * 
     * @access  public
     * @since   3.15.0
     */

    public function route() {
        switch ( $this->response_code ) {
            case 200:
                switch ( $this->path ) {
                    case 'pdf':
                        $class = "\\App\\PDF\\{$this->page_to_load}PDF";
                        break;
                    case 'docs':
                        $class = "\\Framework\\Docs\\DocLoader";
                        break;
                    case 'download':
                        require APP_PATH . 'Actions' . DIR_SEP . 'DownloadHandler.php';
                        die;
                    case 'maintenance':
                        $class = "\\App\\Pages\\Maintenance\\Dashboard";
                        break;
                    case 'dev-tools':
                        $class = "\\Framework\\DevTools\\Dashboard";
                        break;
                    default: // (http)
                        $class = "\\App\\Web\\{$this->page_to_load}Page";
                }
                $page = new $class;
                $page->construct_page();
                break;
            case 404:
                require ERROR_404;
                break;
            case 403:
                require ERROR_403;
                break;
            case 401:
                require ERROR_401;
                break;
            default:
                // "It broke properly - should be a 500";
                require ERROR_404;
        }
    }


    /**
     * Perform a check to see if the route is available
     * 
     * @access  private
     * @since   3.15.0
     */

    private function check_file_exists() {
        switch ( $this->path ) {
            case 'http':
                if ( !is_file( WEB_APP_PATH . $this->page_to_load . 'Page.php' ) ) {
                    $this->response_code = 404;
                }
                break;
            case 'pdf':
                if ( !is_file( PDF_APP_PATH . $this->page_to_load . 'PDF.php' ) ) {
                    $this->response_code = 404;
                }
                break;
            case 'docs':
                $this->response_code = 200;
                break;
        }
    }

}