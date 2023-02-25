<?php

declare(strict_types=1);

namespace Test\Auth;

use LBF\Auth\Cookie;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

final class CookieTest extends TestCase {

    public function test_set_value() {
        $expire = time() + 1;
        $this->assertTrue( Cookie::set_value( 'cheese', 'cake', $expire ) );

        $cookie = new Cookie;
        $reflector = new ReflectionClass( $cookie );
        $method = $reflector->getMethod( 'encode_cookie' );
        $method->setAccessible( true );

        $value = $method->invokeArgs( $cookie, ['cake'] );

        $this->assertEquals( Cookie::get_cookie_list(), [
            0 => [
                'name' => 'cheese',
                'value' => $value,
                'expires' => $expire,
                'path' => "",
                'domain' => "",
                'secure' => false,
                'httponly' => false,
            ]
        ] );
    }

}