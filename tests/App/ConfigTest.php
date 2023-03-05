<?php

declare(strict_types=1);

namespace Test\App;

use LBF\App\Config;
use PHPUnit\Framework\TestCase;
use stdClass;

final class ConfigTest extends TestCase {

    public function test_standard_load() {
        Config::load( ['cheese' => [
            'cake' => 'chocolate',
            'mouse' => 'trap',
        ]]);

        $test = new stdClass;
        $test->meta = [
            'app_name'        => 'YOUR APP NAME',
            'description'     => 'A basic PHP Framework',
            'project_version' => '0.1.0',
            'project_status'  => '',
            'page_title'      => 'Lourie Basic Framework',
            'favicon'         => '',
            'site_language'   => 'en',
            'block_robots'    => false,
        ];
        $test->static_routes = [];
        $test->cheese = [
            'cake' => 'chocolate',
            'mouse' => 'trap',
        ];
        $this->assertEquals( Config::$payload, $test );
    }


    public function test_multi_load() {
        Config::load( ['cheese' => [
            'cake' => 'chocolate',
            'mouse' => 'trap',
        ]] );
        Config::load( ['cat' => [
            'rat' => 'trap',
        ]] );

        $test = new stdClass;
        $test->meta = [
            'app_name'        => 'YOUR APP NAME',
            'description'     => 'A basic PHP Framework',
            'project_version' => '0.1.0',
            'project_status'  => '',
            'page_title'      => 'Lourie Basic Framework',
            'favicon'         => '',
            'site_language'   => 'en',
            'block_robots'    => false,
        ];
        $test->static_routes = [];
        $test->cheese = [
            'cake' => 'chocolate',
            'mouse' => 'trap',
        ];
        $test->cat = ['rat' => 'trap'];
        $this->assertEquals( Config::$payload, $test );
    }


    public function test_overwrite_load() {
        Config::load( ['cheese' => [
            'cake' => 'chocolate',
            'mouse' => 'trap',
        ]] );
        Config::load( ['cat' => [
            'rat' => 'trap',
        ]], true );

        $test = new stdClass;
        $test->meta = [
            'app_name'        => 'YOUR APP NAME',
            'description'     => 'A basic PHP Framework',
            'project_version' => '0.1.0',
            'project_status'  => '',
            'page_title'      => 'Lourie Basic Framework',
            'favicon'         => '',
            'site_language'   => 'en',
            'block_robots'    => false,
        ];
        $test->static_routes = [];
        $test->cat = ['rat' => 'trap'];
        $this->assertEquals( Config::$payload, $test );
    }

}
