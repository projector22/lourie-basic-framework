<?php

namespace LBF\Actions;

use LBF\Actions\ActionsTemplate;
use LBF\App\Config;
use LBF\Config\AppMode;
use LBF\Errors\Classes\MethodNotFound;

/**
 * Base tools for handling Action API calls.
 * 
 * use LBF\Actions\ActionHandler;
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @since   LBF 0.6.0-beta
 */

abstract class ActionHandler implements ActionsTemplate {

    /**
     * The token parsed to by the AJAX request. This is used to direct the action handler to the correct method.
     * 
     * @var string|null  $token
     * 
     * @readonly
     * @access  protected
     * @since   LBF 0.6.0-beta
     */

    protected readonly ?string $token;

    /**
     * The defined routing class according to several conditions.
     * 
     * @var string  $routing_class
     * 
     * @readonly
     * @access  protected
     * @since   LBF 0.6.0-beta
     */

    protected readonly string $routing_class;


    /**
     * Class Constructor, sets `$this->token` & `$this->routing_class`.
     * 
     * @access  public
     * @since   LBF 0.6.0-beta
     */

    public function __construct() {
        $route_token = str_replace( '.php', '', $_POST['route_token'] );
        if ( $route_token == 'home' ) {
            $route_token = 'index';
        }
        if ( Config::ENVIRONMENT() == AppMode::MAINTENANCE ) {
            $route_token = 'maintenance';
        }

        if ( file_exists( Config::paths( 'HOME_PATH' ) . 'FIRST_RUN' ) ) {
            $route_token = 'FirstRun';
        }

        $this->routing_class = 'Actions\\Pages\\' . prepare_routed_filename( $route_token ) . 'Actions';

        $this->token = get_token();
    }


    /**
     * Performs the default task of executing the called token method.
     * 
     * @param   object  $actions    The object which has the default task.
     * 
     * @throws  MethodNotFound  If the requested method does not exist.
     * 
     * @access  public
     * @since   LBF 0.6.0-beta
     */

    public function execute_default_action( object $actions ): void {
        if ( $this->token == '' || is_null( $this->token ) ) {
            return;
        }
        if ( !method_exists( $actions, $this->token ) ) {
            throw new MethodNotFound( "Method '{$this->token}' does not exist on in Class '{$this->routing_class}'", 404 );
        }
        $task = $this->token;
        $actions->$task();
    }
}