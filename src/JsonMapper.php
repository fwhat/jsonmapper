<?php


namespace Fwhat\JsonMapper;


use Fwhat\JsonMapper\Types\Map;
use ReflectionException;

/**
 * Class JsonMapper
 * @package Fwhat\JsonMapper\
 */
class JsonMapper implements MapperInterface {
    /**
     * @var array ['class_key' => ClassRef]
     */
    private array $classRefMap = [];

    protected array $simpleTypes = [
        'bool',
        'int',
        'string',
        'float',
        'array',
    ];

    /**
     * @param $data
     * @param object $object
     */
    public function map ($data, object $object) {
        if (is_string($data)) {
            $data = json_decode($data, true);
        }

        $this->mapJsonArray($data, $object);
    }

    public function warpArray($type, $data) {
        if (ends_with($type, '[]')) {
            $subType = substr($type, 0, -2);
            $value = [];
            foreach ($data as $key => $item) {
                $value[$key] = $this->warpArray($subType, $item);
            }
            return $value;
        } else {
            if (in_array($type, $this->simpleTypes) || $type == 'mixed') {
                return $data;
            } else {
                $subObject = new $type;
                $this->mapJsonArray($data, $subObject);
                return $subObject;
            }
        }
    }

    public function mapJsonArray (array $data, object $object) {
        $classRef = $this->classRef($object);

        foreach ($classRef->properties as $propertyRef) {
            if (!array_key_exists($propertyRef->name, $data)) {
                continue;
            }

            $type = $propertyRef->type;
            if (in_array($type, $this->simpleTypes) || $type == 'mixed' || $data[$propertyRef->name] == null) {
                $value = $data[$propertyRef->name];
            } elseif (ends_with($type, '[]')) {
                $value = $this->warpArray($type, $data[$propertyRef->name]);
            } elseif (starts_with($type, 'Map::')) {
                $value = new Map();
                $subType = substr($type, 5);
                if (in_array($subType, $this->simpleTypes)) {
                    $value->setItems($data[$propertyRef->name]);
                } else {
                    foreach ($data[$propertyRef->name] as $key => $datum) {
                        $subObject = new $subType;
                        $this->mapJsonArray($datum, $subObject);
                        $value->set($key, $subObject);
                    }
                }
            } else {
                $propertyObject = new $type;
                $this->mapJsonArray($data[$propertyRef->name], $propertyObject);

                $value = $propertyObject;
            }

            if ($propertyRef->setterMethod) {
                $object->{$propertyRef->setterMethod}($value);
            } else {
                $object->{$propertyRef->name} = $value;
            }
        }
    }

    /**
     * @param object $object
     * @return ClassRef
     * @throws ReflectionException
     */
    public function classRef (object $object): ClassRef {
        $propertiesKey = get_class($object);
        if (isset($this->classRefMap[$propertiesKey])) {
            return $this->classRefMap[$propertiesKey];
        } else {
            return $this->classRefMap[$propertiesKey] = new ClassRef($object);
        }
    }
}