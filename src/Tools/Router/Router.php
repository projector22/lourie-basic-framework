<?php

namespace LBF\Tools\Router;

use LBF\HTML\Draw;
use Debugger\Debug;

class Router {

    private readonly string $page;
    private readonly string $subpage;
    private readonly string $class;

    private int $status_code = 0;

    public function __construct(
        private readonly array $routing_data
    ) {
        Debug::$display->data( $routing_data );
        Debug::$display->data( $_SERVER['REQUEST_URI'] );

        Draw::lines( 1 );

        $self = explode( '/', $_SERVER['PHP_SELF'] );
        $self = array_map( function ( $item ) {
            if ( str_contains( $item, '.php' ) ) {
                return '';
            }
            return $item;
        }, $self );
        $self = rtrim( implode( '/', $self ), '/' );

        Debug::$display->data( $self );
        
        $set_route = str_replace( $self, '', $_SERVER['REQUEST_URI'] );
        Draw::lines( 1 );
        Debug::$display->data( $set_route );
        if ( str_contains( $set_route, '*' ) ) {
            // If Wildcard used.
        } else {
            // If directly specified
            if ( isset( $this->routing_data[$set_route] ) ) {
                $this->class =  $this->routing_data[$set_route];
                $route = explode( '/', $set_route );
                $this->page    = $route[1];
                $this->subpage = $route[2];
                $this->status_code = 200;
            } else {
                // throw 404
                $this->status_code = 404;
            }
        }
    }

    public function __destruct() {
        Debug::$display->page_data();        
    }

    public function get_page(): string {
        return $this->page;
    }

    public function get_subpage(): string {
        return $this->subpage;
    }

    public function get_class(): string {
        return $this->class;
    }

    public function get_status_code(): string {
        return $this->class;
    }



}