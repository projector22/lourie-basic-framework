<?php

declare(strict_types=1);

namespace Test\Auth;

use LBF\Auth\Cookie;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

final class CookieTest extends TestCase {

    public function test_set_value() {
        $expire = time() + 1;
        /**
         * Tests `Cookie::set_value`
         */
        $this->assertTrue( Cookie::set_value( 'cheese', 'cake', $expire ) );

        $cookie = new Cookie;
        $reflector = new ReflectionClass( $cookie );
        $method = $reflector->getMethod( 'encode_cookie' );
        $method->setAccessible( true );

        $value = $method->invokeArgs( $cookie, ['cake'] );

        /**
         * Tests:
         * - private static array $cookie_list
         * - get_cookie_list()
         */
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

        $this->assertTrue( $cookie->inject_cookies( true ) );
    }

    public function test_encode_decode() {
        $cookie = new Cookie;
        $reflector = new ReflectionClass( $cookie );
        $encode = $reflector->getMethod( 'encode_cookie' );
        $encode->setAccessible( true );

        $decode = $reflector->getMethod( 'decode_cookie' );
        $decode->setAccessible( true );

        $encode_data = $encode->invokeArgs( $cookie, ['cheese'] );
        $decode_data = $decode->invokeArgs( $cookie, [$encode_data] );

        $this->assertEquals( 'cheese', $decode_data );
    }


    public function test_set_default_duration() {
        $this->assertTrue( Cookie::set_default_duration( 500 ) );
        $this->assertTrue( Cookie::set_default_duration( 500, true ) );
        $this->assertTrue( Cookie::set_default_duration( '+1 day' ) );
    }


    public function test_call_static() {
        $this->expectException( 'Exception' );
        Cookie::mouse( ['cat'] );
    }


    public function test_value_exists() {
        $this->assertFalse( Cookie::value_exists( 'mouse' ) );
        $this->expectException( 'Exception' );
        Cookie::get_value( 'mouse' );
    }


    public function test_destroy_value() {
        $this->assertTrue( Cookie::destroy_all_values() );
    }

}