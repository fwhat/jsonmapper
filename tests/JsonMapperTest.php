<?php

namespace Fwhat\JsonMapper\Tests;

use Fwhat\JsonMapper\JsonMapper;
use Fwhat\JsonMapper\Tests\Stub\DocArrayObject\DocArrayObject;
use Fwhat\JsonMapper\Tests\Stub\MapObject\MapObject;
use Fwhat\JsonMapper\Tests\Stub\NestingObject\NestingObject;
use Fwhat\JsonMapper\Tests\Stub\SetObject\SetObject;
use Fwhat\JsonMapper\Tests\Stub\SimpleObject\SimpleObject;
use PHPUnit\Framework\TestCase;

class JsonMapperTest extends TestCase {
    public function testSetObject () {
        $jsonStr = file_get_contents(__DIR__.'/Stub/SetObject/object.json');
        $mapper = new JsonMapper;

        $object = new SetObject();
        $mapper->map($jsonStr, $object);

        self::assertEquals(
            self::except($object->toArray(), ['string']),
            self::except(json_decode($jsonStr, true), ['string']),
        );

        self::assertEquals(
            $object->string,
            "from_set_".json_decode($jsonStr, true)['string'],
        );
    }

    public function testSimpleObject () {
        $jsonStr = file_get_contents(__DIR__.'/Stub/SimpleObject/object.json');
        $mapper = new JsonMapper;

        $object = new SimpleObject();
        $mapper->map($jsonStr, $object);

        self::assertEquals($object->toArray(), json_decode($jsonStr, true));
    }

    public function testNestingObject () {
        $jsonStr = file_get_contents(__DIR__.'/Stub/NestingObject/object.json');

        $mapper = new JsonMapper;

        $object = new NestingObject();
        $mapper->map($jsonStr, $object);

        self::assertEquals($object->toArray(), json_decode($jsonStr, true));
    }

    public function testMapObject () {
        $jsonStr = file_get_contents(__DIR__.'/Stub/MapObject/object.json');

        $mapper = new JsonMapper;

        $object = new MapObject();
        $mapper->map($jsonStr, $object);

        $decode = json_decode($jsonStr, true);
        $decode['mapObjectMap']['key']['mapObjectMapNull'] = null;

        self::assertEquals($decode, json_decode(json_encode($object->toArray()), true));
    }

    public function testDocArrayObject () {
        $jsonStr = file_get_contents(__DIR__.'/Stub/DocArrayObject/object.json');

        $mapper = new JsonMapper;

        $object = new DocArrayObject();
        $mapper->map($jsonStr, $object);

        foreach ($object->nestingObjectArray as $nestingObject) {
            self::assertInstanceOf(NestingObject::class, $nestingObject);
        }

        foreach ($object->docArrayObjectArray as $nestingObject) {
            self::assertInstanceOf(DocArrayObject::class, $nestingObject);
        }

        foreach ($object->nestingObjectArrayDoc as $nestingObject) {
            self::assertInstanceOf(NestingObject::class, $nestingObject);
        }

        foreach ($object->nestingObjectArray2 as $nestingObjects) {
            foreach ($nestingObjects as $nestingObject) {
                self::assertInstanceOf(NestingObject::class, $nestingObject);
            }
        }

        foreach ($object->nestingObjectArray3 as $nestingObjectss) {
            foreach ($nestingObjectss as $nestingObjects) {
                foreach ($nestingObjects as $nestingObject) {
                    self::assertInstanceOf(NestingObject::class, $nestingObject);
                }
            }
        }
        $decode = json_decode($jsonStr, true);
        $decode['docArrayObjectArray'][0]['nestingObjectArrayNull'] = null;

        self::assertEquals($decode, $object->toArray());
    }

    public function testPerformanceSimple() {
        $simpleObject = file_get_contents(__DIR__.'/Stub/SetObject/object.json');
        $count = 10000;
        $mapper = new JsonMapper;
        $start = microtime(true);
        for ($i = 0; $i < 10000; $i ++) {
            $object = new SetObject();
            $mapper->map($simpleObject, $object);
        }
        $end = microtime(true);

        $speed = $end - $start;
        echo PHP_EOL . "count $count simpleObject speed $speed s rate: " . (1 / $speed * $count) . PHP_EOL;
        self::assertGreaterThan(150000, 1 / $speed * $count);
    }

    public function testPerformanceNesting() {
        $jsonStr = file_get_contents(__DIR__.'/Stub/DocArrayObject/object.json');
        $count = 10000;
        $mapper = new JsonMapper;
        $start = microtime(true);
        for ($i = 0; $i < 10000; $i ++) {
            $object = new DocArrayObject();
            $mapper->map($jsonStr, $object);
        }
        $end = microtime(true);

        $speed = $end - $start;
        echo PHP_EOL . "count $count nestingObject speed $speed s rate: " . (1 / $speed * $count) . PHP_EOL;
        self::assertGreaterThan(10000, 1 / $speed * $count);
    }

    // --------------------------------- copy from laravel function --------------------------------- //
    /**
     * Remove one or many array items from a given array using "dot" notation.
     *
     * @param array $array
     * @param array|string $keys
     * @return void
     */
    public static function forget(array &$array, $keys)
    {
        $original = &$array;

        $keys = (array) $keys;

        if (count($keys) === 0) {
            return;
        }

        foreach ($keys as $key) {
            // if the exact key exists in the top-level, remove it
            if (array_key_exists($key, $array)) {
                unset($array[$key]);

                continue;
            }

            $parts = explode('.', $key);

            // clean up before each pass
            $array = &$original;

            while (count($parts) > 1) {
                $part = array_shift($parts);

                if (isset($array[$part]) && is_array($array[$part])) {
                    $array = &$array[$part];
                } else {
                    continue 2;
                }
            }

            unset($array[array_shift($parts)]);
        }
    }

    /**
     * Get all of the given array except for a specified array of keys.
     *
     * @param array $array
     * @param array|string $keys
     * @return array
     */
    public static function except(array $array, $keys): array {
        static::forget($array, $keys);

        return $array;
    }
}