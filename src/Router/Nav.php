<?php

namespace LBF\Router;

class Nav {

    public static function redirect( string $location ): never {
        header( "Location: {$location}" );
        die;
    }
}