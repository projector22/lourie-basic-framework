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


    public function testIsAssociative(): void {
        $test = ['name' => 'John', 'age' => 30];
        $this->assertFalse(ArrayTool::is_associative($test));
        $test = ['apple', 'banana', 'cherry'];
        $this->assertTrue(ArrayTool::is_associative($test));
        $test = [0 => 'apple', 2 => 'banana', 1 => 'cherry'];
        $this->assertFalse(ArrayTool::is_associative($test));
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
        $test_nums = [true, true, true];
        $this->assertIsNumeric(ArrayTool::add($test_nums));
        $this->assertEquals(0, ArrayTool::add($test_nums));
    }


    public function testAverage(): void {
        $test_nums = [3, 5, 12, 44, 3.2, 0.75, 1];
        $this->assertIsNumeric(ArrayTool::average($test_nums));
        $this->assertEquals(9.85, ArrayTool::average($test_nums));
        $test_nums = [1];
        $this->assertIsNumeric(ArrayTool::average($test_nums));
        $this->assertEquals(1, ArrayTool::average($test_nums));
        $test_nums = ["1"];
        $this->assertIsNumeric(ArrayTool::average($test_nums));
        $this->assertEquals(1, ArrayTool::average($test_nums));
        $test_nums = [null];
        $this->assertIsNumeric(ArrayTool::average($test_nums));
        $this->assertEquals(0, ArrayTool::average($test_nums));
        $test_nums = [['a' => 'b'], 1];
        $this->assertIsNumeric(ArrayTool::average($test_nums));
        $this->assertEquals(1, ArrayTool::average($test_nums));
        $test_nums = [new stdClass, 2, 5];
        $this->assertIsNumeric(ArrayTool::average($test_nums));
        $this->assertEquals(3.5, ArrayTool::average($test_nums));
        $test_nums = [true, true, true];
        $this->assertIsNumeric(ArrayTool::add($test_nums));
        $this->assertEquals(0, ArrayTool::add($test_nums));
    }


    public function testMax(): void {
        $test_nums = [3, 5, 12, 44, 3.2, 0.75, 1];
        $this->assertIsNumeric(ArrayTool::max($test_nums));
        $this->assertEquals(44, ArrayTool::max($test_nums));
        $test_nums = [1];
        $this->assertIsNumeric(ArrayTool::max($test_nums));
        $this->assertEquals(1, ArrayTool::max($test_nums));
        $test_nums = ["1"];
        $this->assertIsNumeric(ArrayTool::max($test_nums));
        $this->assertEquals(1, ArrayTool::max($test_nums));
        $test_nums = [['a' => 'b'], 1];
        $this->assertIsNumeric(ArrayTool::max($test_nums));
        $this->assertEquals(1, ArrayTool::max($test_nums));
        $test_nums = [new stdClass, 2, 5];
        $this->assertIsNumeric(ArrayTool::max($test_nums));
        $this->assertEquals(5, ArrayTool::max($test_nums));
        $test_nums = [true, true, true];
        $this->expectException(\ValueError::class);
        $this->assertIsNumeric(ArrayTool::max($test_nums));
    }


    public function testMaxException(): void {
        $test_nums = [null];
        $this->expectException(\ValueError::class);
        $this->assertIsNumeric(ArrayTool::max($test_nums));
    }


    public function testMin(): void {
        $test_nums = [3, 5, 12, 44, 3.2, 0.75, 1];
        $this->assertIsNumeric(ArrayTool::min($test_nums));
        $this->assertEquals(0.75, ArrayTool::min($test_nums));
        $test_nums = [1];
        $this->assertIsNumeric(ArrayTool::min($test_nums));
        $this->assertEquals(1, ArrayTool::min($test_nums));
        $test_nums = ["1"];
        $this->assertIsNumeric(ArrayTool::min($test_nums));
        $this->assertEquals(1, ArrayTool::min($test_nums));
        $test_nums = [['a' => 'b'], 1];
        $this->assertIsNumeric(ArrayTool::min($test_nums));
        $this->assertEquals(1, ArrayTool::min($test_nums));
        $test_nums = [new stdClass, 2, 5];
        $this->assertIsNumeric(ArrayTool::min($test_nums));
        $this->assertEquals(2, ArrayTool::min($test_nums));
        $test_nums = [true, true, true];
        $this->expectException(\ValueError::class);
        $this->assertIsNumeric(ArrayTool::min($test_nums));
    }


    public function testMinException(): void {
        $test_nums = [null];
        $this->expectException(\ValueError::class);
        $this->assertIsNumeric(ArrayTool::min($test_nums));
    }


    public function testRemove(): void {
        $test_array = [
            'a' => 'b',
            'c' => 'd',
            'e' => 'f',
        ];
        $test = ArrayTool::remove('c', $test_array);

        $this->assertIsArray($test_array);
        $this->assertIsString($test);
        $this->assertArrayNotHasKey('c', $test_array);
        $this->assertEquals([
            'a' => 'b',
            'e' => 'f',
        ], $test_array);
        $this->assertEquals('d', $test);

        // Test the case where there are 2 of the same key (should discard the first one automatically).
        $test_array = [
            'a' => 'b',
            'c' => 'd',
            'e' => 'f',
            'c' => 'g',
        ];
        $test = ArrayTool::remove('c', $test_array);
        $this->assertIsArray($test_array);
        $this->assertIsString($test);
        $this->assertArrayNotHasKey('c', $test_array);
        $this->assertEquals([
            'a' => 'b',
            'e' => 'f',
        ], $test_array);
        $this->assertEquals('g', $test);

        $test_array = [
            'a' => 'b',
            'c' => 'd',
            'e' => 'f',
        ];
        $this->expectException(IndexNotInArray::class);
        $test = ArrayTool::remove('g', $test_array);
    }


    public function testRemoveMax(): void {
        $test_nums = [3, 5, 12, 44, 3.2, 0.75, 1];
        $max = ArrayTool::remove_max($test_nums);
        $this->assertIsNumeric($max);
        $this->assertEquals(44, $max);
        $this->assertEquals([3, 5, 12, 3.2, 0.75, 1], $test_nums);

        $test_nums = [3, 5, 12, 44, 3.2, 0.75, 1];
        $max = ArrayTool::remove_max($test_nums, false);
        $this->assertIsNumeric($max);
        $this->assertEquals(44, $max);
        $this->assertEquals([0 => 3, 1 => 5, 2 => 12, 4 => 3.2, 5 => 0.75, 6 => 1], $test_nums);

        $test_nums = [3, true, 12, 44, [], 0.75, new stdClass];
        $max = ArrayTool::remove_max($test_nums);
        $this->assertIsNumeric($max);
        $this->assertEquals(44, $max);
        $this->assertEquals([3, true, 12, [], 0.75, new StdClass], $test_nums);

        $test_nums = [null, [], new stdClass, true];
        $this->expectException(\ValueError::class);
        ArrayTool::remove_max($test_nums);
    }


    public function testRemoveMin(): void {
        $test_nums = [3, 5, 12, 44, 3.2, 0.75, 1];
        $min = ArrayTool::remove_min($test_nums);
        $this->assertIsNumeric($min);
        $this->assertEquals(0.75, $min);
        $this->assertEquals([3, 5, 12, 44, 3.2, 1], $test_nums);

        $test_nums = [3, 5, 12, 44, 3.2, 0.75, 1];
        $min = ArrayTool::remove_min($test_nums, false);
        $this->assertIsNumeric($min);
        $this->assertEquals(0.75, $min);
        $this->assertEquals([0 => 3, 1 => 5, 2 => 12, 3 => 44, 4 => 3.2, 6 => 1], $test_nums);

        $test_nums = [3, true, 12, 44, [], 0.75, new stdClass];
        $min = ArrayTool::remove_min($test_nums);
        $this->assertIsNumeric($min);
        $this->assertEquals(0.75, $min);
        $this->assertEquals([3, true, 12, 44, [], new StdClass], $test_nums);

        $test_nums = [null, [], new stdClass, true];
        $this->expectException(\ValueError::class);
        ArrayTool::remove_min($test_nums);
    }


    public function testKeysExist(): void {
        $data = [
            'a' => 'cheese',
            'b' => 'cheese',
            'c' => 'cheese',
            'd' => null,
            'e' => 'cheese',
            'f' => 'cheese',
        ];

        $this->assertTrue(ArrayTool::keys_exists($data, ['c']));
        $this->assertTrue(ArrayTool::keys_exists($data, ['d']));
        $this->assertFalse(ArrayTool::keys_exists($data, ['g']));
        $this->assertTrue(ArrayTool::keys_exists($data, ['a', 'e']));
        $this->assertFalse(ArrayTool::keys_exists($data, ['a', 'h']));
        $this->assertTrue(ArrayTool::keys_exists($data, ['c', 'd']));
    }
}
