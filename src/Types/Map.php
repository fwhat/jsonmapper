<?php


namespace Fwhat\JsonMapper\Types;


use ArrayAccess;
use Exception;
use Iterator;
use JsonSerializable;

/**
 * Class Map
 * @package Fwhat\JsonMapper\Types
 */
class Map implements JsonSerializable, Iterator, ArrayAccess {

    /**
     * @var T[]
     */
    private array $items;

    /**
     * Map constructor.
     * @param T[] $items
     */
    public function __construct ($items = []) {
        $this->items = $items;
    }

    /**
     * @param T[] $items
     */
    public function setItems ($items) {
        $this->items = $items;
    }

    /**
     * @param $key
     * @param T $value
     */
    public function set ($key, $value) {
        $this->items[$key] = $value;
    }

    /**
     * @param $key
     * @return T|null
     */
    public function get ($key) {
        return $this->items[$key] ?? null;
    }

    public function jsonSerialize () {
        if ($this->items) {
            return $this->items;
        } else {
            return $this;
        }
    }

    public function offsetExists ($offset): bool {
        return array_key_exists($offset, $this->items);
    }

    /**
     * @param mixed $offset
     * @return mixed
     * @throws Exception
     */
    public function offsetGet ($offset) {
        if (array_key_exists($offset, $this->items)) {
            return $this->items[$offset];
        }

        throw new Exception("Undefined offset: $offset");
    }

    public function offsetSet ($offset, $value) {
        $this->items[$offset] = $value;
    }

    public function offsetUnset ($offset) {
        unset($this->items[$offset]);
    }

    /**
     * @return T
     */
    public function current () {
        return current($this->items);
    }

    public function next () {
        next($this->items);
    }

    public function key () {
        return key($this->items);
    }

    public function valid (): bool {
        return key($this->items) !== null;
    }

    public function rewind () {
        reset($this->items);
    }

    public function filter(callable $callback): array {
        return array_filter($this->items, $callback);
    }

    public function map(callable $callback): array {
        $map = [];
        foreach ($this->items as $key => $value) {
            $map[] = $callback($value, $key);
        }

        return $map;
    }

    public function mapWithKey(callable $callback): array {
        $map = [];
        foreach ($this->items as $key => $value) {
            $map += $callback($value, $key);
        }

        return $map;
    }
}