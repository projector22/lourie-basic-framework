<?php

declare(strict_types=1);

use LBF\App\Config;
use PHPUnit\Framework\TestCase;

final class ConfigTest extends TestCase {

    public function test_standard_load() {
        Config::load( ['cheese' => [
            'cake' => 'chocolate',
            'mouse' => 'trap',
        ]]);

        $test = new stdClass;
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
        $test->cat = ['rat' => 'trap'];
        $this->assertEquals( Config::$payload, $test );
    }

}
