<?php
namespace Fwhat\JsonMapper\Tests\Stub;

trait ArrayHelper {
    public function toArray (): array {
        return $this->_toArray(get_object_vars($this));
    }

    public function _toArray (array $var): array {
        return array_map(function ($value) {
            if (method_exists($value, 'toArray')) {
                return $value->toArray();
            } elseif (is_array($value)) {
                return $this->_toArray($value);
            } else {
                return $value;
            }
        }, $var);
    }
}