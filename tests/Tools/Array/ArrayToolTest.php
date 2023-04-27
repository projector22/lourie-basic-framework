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

    private ?array $test_array;
    private ?array $test_object;

    protected function setUp(): void {
        $this->test_array = [
            ['a' => 'Cheese', 'b' => 'Cake', 'c' => 'United'],
            ['a' => 'Mouse', 'b' => 'Rat', 'c' => 'Dog'],
            ['a' => 'Water', 'b' => 'Wind', 'c' => 'Fire'],
        ];

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

        $this->test_object = [
            $obja,
            $objb,
            $objc,
        ];
    }


    protected function tearDown(): void {
        $this->test_array = null;
        $this->test_object = null;
    }


    public function testIndexBy(): void {
        $this->assertIsArray(ArrayTool::index_by('a', $this->test_array));

        $index = ArrayTool::index_by('a', $this->test_array, false);

        $this->assertEquals(
            [
                'Cheese' => ['a' => 'Cheese', 'b' => 'Cake', 'c' => 'United'],
                'Mouse' => ['a' => 'Mouse', 'b' => 'Rat', 'c' => 'Dog'],
                'Water' => ['a' => 'Water', 'b' => 'Wind', 'c' => 'Fire'],
            ],
            $index
        );

        $index = ArrayTool::index_by('a', $this->test_array, true);

        $this->assertEquals(
            [
                'Cheese' => ['b' => 'Cake', 'c' => 'United'],
                'Mouse' => ['b' => 'Rat', 'c' => 'Dog'],
                'Water' => ['b' => 'Wind', 'c' => 'Fire'],
            ],
            $index
        );

        $index = ArrayTool::index_by('a', $this->test_object, false);
        list($obja, $objb, $objc) = $this->test_object;

        $this->assertEquals(
            [
                'Cheese' => $obja,
                'Mouse' => $objb,
                'Water' => $objc,
            ],
            $index
        );

        $index = ArrayTool::index_by('a', $this->test_object, true);

        foreach ($this->test_object as &$obj) {
            unset($obj->a);
        }

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
        $test_data = [null, 'a' => false, true, 100, 'cheese'];
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
        $this->assertIsArray(ArrayTool::map($this->test_array, 'a', 'c'));

        $test = ArrayTool::map($this->test_array, 'a', 'c');
        $this->assertEquals([
            'Cheese' => 'United',
            'Mouse' => 'Dog',
            'Water' => 'Fire',
        ], $test);


        $this->assertIsArray(ArrayTool::map($this->test_object, 'a', 'c'));

        $test = ArrayTool::map($this->test_object, 'a', 'c');
        $this->assertEquals([
            'Cheese' => 'United',
            'Mouse' => 'Dog',
            'Water' => 'Fire',
        ], $test);
    }


    public function testScalarVariableExceptionForMap(): void {
        $test_data = [null, 'a' => false, 'b' => true, 100, 'cheese'];
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


    public function testColumn(): void {
        $this->assertIsArray(ArrayTool::column($this->test_array, 'b'));

        $test = ArrayTool::column($this->test_array, 'b');
        $this->assertEquals(
            ['Cake', 'Rat', 'Wind'],
            $test
        );

        $test = ArrayTool::column($this->test_object, 'b');
        $this->assertEquals(
            ['Cake', 'Rat', 'Wind'],
            $test
        );
    }


    public function testScalarVariableExceptionForColumn(): void {
        $test_data = [null, 'b' => false, true, 100, 'cheese'];
        $this->expectException(ScalarVariable::class);
        ArrayTool::column($test_data, 'b');
    }


    public function testIndexNotInArrayExceptionForColumn(): void {
        $test_data = [
            [null, 'a' => false, true, 100, 'cheese'],
            [null, 'c' => false, true, 100, 'cheese'],
        ];
        $this->expectException(IndexNotInArray::class);
        ArrayTool::column($test_data, 'b');
    }


    public function testPropertyNotInObjectForColumn(): void {
        $obj = new stdClass;
        $obj->a = 'cheese';
        $test_data = [$obj];
        $this->expectException(PropertyNotInObject::class);
        ArrayTool::column($test_data, 'b');
    }


    public function testAdd(): void {
        $test_nums = [3, 5, 12, 44, 3.2, 0.75, 1];
        $this->assertIsNumeric(ArrayTool::add($test_nums));
        $this->assertEquals(68.95, ArrayTool::add($test_nums));
        $test_nums = [1];
        $this->assertIsNumeric(ArrayTool::add($test_nums));
        $this->assertEquals(1, ArrayTool::add($test_nums));
        $test_nums = ["1"];
        $this->assertIsNumeric(ArrayTool::add($test_nums));
        $this->assertEquals(1, ArrayTool::add($test_nums));
        $test_nums = [null];
        $this->assertIsNumeric(ArrayTool::add($test_nums));
        $this->assertEquals(0, ArrayTool::add($test_nums));
        $test_nums = [['a' => 'b'], 1];
        $this->assertIsNumeric(ArrayTool::add($test_nums));
        $this->assertEquals(1, ArrayTool::add($test_nums));
        $test_nums = [new stdClass, 2, 5];
        $this->assertIsNumeric(ArrayTool::add($test_nums));
        $this->assertEquals(7, ArrayTool::add($test_nums));
        $test_nums = [1, 2, 3, 4, 5.5];
    }
}
