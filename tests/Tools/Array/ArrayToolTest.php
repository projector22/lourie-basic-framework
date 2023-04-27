<?php

declare(strict_types=1);

namespace Test\Tools\Array;

use LBF\Errors\Array\IndexNotInArray;
use LBF\Errors\Array\PropertyNotInObject;
use LBF\Errors\Array\ScalarVariable;
use LBF\Tools\Array\ArrayTool;
use PHPUnit\Framework\TestCase;
use stdClass;

final class ArrayToolTest extends TestCase {

    public function testIndexBy(): void {
        $test_data = [
            ['a' => 'Cheese', 'b' => 'Cake', 'c' => 'United'],
            ['a' => 'Mouse', 'b' => 'Rat', 'c' => 'Dog'],
            ['a' => 'Water', 'b' => 'Wind', 'c' => 'Fire'],
        ];

        $this->assertIsArray(ArrayTool::index_by('a', $test_data));

        $index = ArrayTool::index_by('a', $test_data, false);

        $this->assertEquals(
            [
                'Cheese' => ['a' => 'Cheese', 'b' => 'Cake', 'c' => 'United'],
                'Mouse' => ['a' => 'Mouse', 'b' => 'Rat', 'c' => 'Dog'],
                'Water' => ['a' => 'Water', 'b' => 'Wind', 'c' => 'Fire'],
            ],
            $index
        );

        $index = ArrayTool::index_by('a', $test_data, true);

        $this->assertEquals(
            [
                'Cheese' => ['b' => 'Cake', 'c' => 'United'],
                'Mouse' => ['b' => 'Rat', 'c' => 'Dog'],
                'Water' => ['b' => 'Wind', 'c' => 'Fire'],
            ],
            $index
        );

        $obja = new stdClass;
        $obja->a = 'Cheese';
        $obja->b = 'Cake';
        $obja->c = 'United';

        $objb = new stdClass;
        $objb->a = 'Mouse';
        $objb->b = 'Rat';
        $objb->c = 'Dog';

        $objc = new stdClass;
        $objc->a = 'Water';
        $objc->b = 'Wind';
        $objc->c = 'Fire';

        $test_data = [
            $obja,
            $objb,
            $objc,
        ];

        $index = ArrayTool::index_by('a', $test_data, false);

        $this->assertEquals(
            [
                'Cheese' => $obja,
                'Mouse' => $objb,
                'Water' => $objc,
            ],
            $index
        );

        $index = ArrayTool::index_by('a', $test_data, true);

        unset($obja->a);
        unset($objb->a);
        unset($objc->a);

        $this->assertEquals(
            [
                'Cheese' => $obja,
                'Mouse' => $objb,
                'Water' => $objc,
            ],
            $index
        );
    }


    public function testScalarVariableExceptionForIndexBy(): void {
        $test_data = [null, false, true, 100, 'cheese'];
        $this->expectException(ScalarVariable::class);
        $index = ArrayTool::index_by('a', $test_data);
    } 


    public function testIndexNotInArrayExceptionForIndexBy(): void {
        $test_data = [
            [null, false, true, 100, 'cheese']
        ];
        $this->expectException(IndexNotInArray::class);
        $index = ArrayTool::index_by('a', $test_data);
    }


    public function testPropertyNotInObjectForIndexBy(): void {
        $obj = new stdClass;
        $obj->b = 'cheese';
        $test_data = [$obj];
        $this->expectException(PropertyNotInObject::class);
        $index = ArrayTool::index_by('a', $test_data);
    }


    public function testMap(): void {
        $test_data = [
            ['a' => 'Cheese', 'b' => 'Cake', 'c' => 'United'],
            ['a' => 'Mouse', 'b' => 'Rat', 'c' => 'Dog'],
            ['a' => 'Water', 'b' => 'Wind', 'c' => 'Fire'],
        ];

        $this->assertIsArray(ArrayTool::map($test_data, 'a', 'c'));

        $test = ArrayTool::map($test_data, 'a', 'c');
        $this->assertEquals([
            'Cheese' => 'United',
            'Mouse' => 'Dog',
            'Water' => 'Fire',
        ], $test);

        $obja = new stdClass;
        $obja->a = 'Cheese';
        $obja->b = 'Cake';
        $obja->c = 'United';

        $objb = new stdClass;
        $objb->a = 'Mouse';
        $objb->b = 'Rat';
        $objb->c = 'Dog';

        $objc = new stdClass;
        $objc->a = 'Water';
        $objc->b = 'Wind';
        $objc->c = 'Fire';

        $test_data = [
            $obja,
            $objb,
            $objc,
        ];

        $this->assertIsArray(ArrayTool::map($test_data, 'a', 'c'));

        $test = ArrayTool::map($test_data, 'a', 'c');
        $this->assertEquals([
            'Cheese' => 'United',
            'Mouse' => 'Dog',
            'Water' => 'Fire',
        ], $test);
    }


    public function testScalarVariableExceptionForMap(): void {
        $test_data = [null, false, true, 100, 'cheese'];
        $this->expectException(ScalarVariable::class);
        ArrayTool::map($test_data, 'a', 'b');
    } 


    public function testIndexNotInArrayExceptionForMapMissingA(): void {
        $test_data = [
            [null, 'a' => false, true, 100, 'cheese'],
            [null, 'b' => false, true, 100, 'cheese'],
        ];
        $this->expectException(IndexNotInArray::class);
        ArrayTool::map($test_data, 'a', 'b');
    }


    public function testIndexNotInArrayExceptionForMapMissingB(): void {
        $test_data = [
            [null, 'a' => false, true, 100, 'cheese'],
            [null, 'b' => false, true, 100, 'cheese'],
        ];
        $this->expectException(IndexNotInArray::class);
        ArrayTool::map($test_data, 'a', 'b');
    }


    public function testPropertyNotInObjectForMapMissingA(): void {
        $obj = new stdClass;
        $obj->b = 'cheese';
        $test_data = [$obj];
        $this->expectException(PropertyNotInObject::class);
        ArrayTool::map($test_data, 'a', 'b');
    }


    public function testPropertyNotInObjectForMapMissingB(): void {
        $obj = new stdClass;
        $obj->a = 'cheese';
        $test_data = [$obj];
        $this->expectException(PropertyNotInObject::class);
        ArrayTool::map($test_data, 'a', 'b');
    }

}
