<?php

namespace LBF\Actions;

use LBF\App\Config;
use LBF\Config\AppMode;

class ActionHandler {

    public string $token;

    public string $routing_class;

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
}