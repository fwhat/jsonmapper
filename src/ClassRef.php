<?php


namespace Fwhat\JsonMapper;


use Fwhat\JsonMapper\Types\Map;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionProperty;

class ClassRef implements PropertiesInterface {
    public function getProperties (): array {
        return $this->properties;
    }
    /**
     * @var PropertyRef[]
     */
    public array $properties;

    /**
     * @var string[]
     */
    public array $useClasses;
    public string $namespace;

    public ReflectionClass $class;
    public string $name;

    public array $simpleTypes = [
        'bool',
        'int',
        'string',
        'float',
        'array',
    ];

    /**
     * ClassRef constructor.
     * @param $object
     * @throws ReflectionException
     */
    public function __construct ($object) {
        $this->class = new ReflectionClass($object);

        $this->useClasses = self::parseUseClasses($this->class->getFileName());
        $this->namespace = $this->class->getNamespaceName();
        $this->name = $this->class->getShortName();
        $this->loadProperties();
    }

    public function loadProperties() {
        $methods = array_flip(array_map(fn($method) => $method->name, $this->class->getMethods(ReflectionMethod::IS_PUBLIC)));

        $this->properties = array_map(function (ReflectionProperty $reflectionProperty) use ($methods) {
            $setMethod = 'set'.studly_case($reflectionProperty->name);

            $property = new PropertyRef();
            $property->type = $this->getType($reflectionProperty, $this);
            $property->name = $reflectionProperty->name;
            $property->setterMethod = isset($methods[$setMethod]) ? $setMethod : '';

            return $property;
        }, $this->class->getProperties(ReflectionProperty::IS_PUBLIC));
    }

    public static function parseUseClasses ($classFile): array {
        $useClasses = [];

        $fp = fopen($classFile, 'r');
        while (($line = ltrim(fgets($fp))) !== false) {
            if (starts_with($line, 'class')) {
                break;
            }
            if (starts_with($line, 'use')) {
                $useStr = rtrim(ltrim($line, 'use '), "; \n");
                if (str_contains($useStr, 'as')) {
                    [$fullClass, $useName] = explode('as', $useStr);
                    $useClasses[trim($useName)] = trim($fullClass);
                } else {
                    $useClasses[class_basename($useStr)] = $useStr;
                }
            }
        }
        fclose($fp);

        return $useClasses;
    }

    protected function getType (ReflectionProperty $property, self $classRef): string {
        if ($property->getType()) {
            $type = $property->getType()->getName();
            switch ($type) {
                case 'array':
                    return $this->parseArrayType($property, $classRef);
                case Map::class:
                    return $this->parseMapType($property, $classRef);
                default:
                    return $type;
            }
        }

        return 'mixed';
    }

    protected function parseMapType (ReflectionProperty $property, self $classRef): string {
        $doc = $property->getDocComment();
        if (!$doc) return 'Map::';

        foreach (explode("\n", $doc) as $line) {
            if (str_contains($line, '@var')) {
                $match = [];
                $type = null;
                if (preg_match('/.*@var\s*Map<(.*)>/', $line, $match) && count($match) > 1) {
                    $types = explode(',', $match[1]);
                    $type = array_pop($types);
                }

                /*** @var Map $_ */
                if (preg_match('/.*@var\s*\\\Fwhat\\\JsonMapper\\\Types\\\Map<(.*)>/', $line, $match) && count($match) > 1) {
                    $types = explode(',', $match[1]);
                    $type = array_pop($types);
                }

                if ($type !== null) {
                    if (in_array($type, $this->simpleTypes)) {
                        return "Map::".$type;
                    }
                    if (isset($classRef->useClasses[$type])) {
                        return "Map::".$classRef->useClasses[$type];
                    }

                    return "Map::".$classRef->namespace.'\\'.$type;
                }

                break;
            }
        }

        return 'Map::';
    }

    protected function parseArrayType (ReflectionProperty $property, self $classRef): string {
        $doc = $property->getDocComment();
        if (!$doc) return 'array';

        foreach (explode("\n", $doc) as $line) {
            if (str_contains($line, '@var')) {
                $match = [];
                $type = null;
                if (preg_match('/.*@var\s*(.*)\[]/', $line, $match) && count($match) > 1) {
                    $type = $match[1];
                }
                $arrayDeepCount = 1;
                while ($type && ends_with($type, '[]')) {
                    $type = substr($type, 0, -2);
                    $arrayDeepCount ++;
                }

                if (preg_match('/.*@var\s*array<(.*)>/', $line, $match) && count($match) > 1) {
                    $types = explode(',', $match[1]);
                    $type = array_pop($types);
                }

                if ($type !== null) {
                    if (in_array($type, $this->simpleTypes)) {
                        return $type.str_repeat('[]', $arrayDeepCount);
                    }
                    if (isset($classRef->useClasses[$type])) {
                        return $classRef->useClasses[$type].str_repeat('[]', $arrayDeepCount);
                    }

                    return $classRef->namespace.'\\'.$type.str_repeat('[]', $arrayDeepCount);
                }

                break;
            }
        }

        return 'array';
    }
}