<?php

namespace LBF\Error;

class ErrorPage {

    private static int $code;

    public function construct_page() {
        $file = match( self::$code ) {
            401     => '401.php',
            403     => '403.php',
            404     => '404.php',
            500     => '500.php',
            default => '500.php',
        };

        $path = realpath( __DIR__ . '/../Error/' . $file );
        require $path;
    }



    public static function set_error( int $code ): void {
        self::$code = $code;
    }
}