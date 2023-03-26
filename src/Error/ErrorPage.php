<?php

namespace LBF\Error;

class ErrorPage {

    private static int $code;

    private static bool $skip_check = false;

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



    public static function set_error( int $code, $skip_check = false ): void {
        self::$code = $code;
        self::$skip_check = $skip_check;
    }

    public static function get_error_code(): ?int {
        return self::$code ?? null;
    }

    public static function skip_check(): bool {
        return self::$skip_check;
    }
}