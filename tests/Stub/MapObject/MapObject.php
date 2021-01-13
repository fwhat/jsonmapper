<?php


namespace Fwhat\JsonMapper\Tests\Stub\MapObject;


use Fwhat\JsonMapper\Tests\Stub\NestingObject\NestingObject;
use Fwhat\JsonMapper\Tests\Stub\ArrayHelper;
use Fwhat\JsonMapper\Types\Map;

class MapObject {
    use ArrayHelper;

    public bool $bool;
    public int $int;
    public string $string;
    public array $arrayString;
    public array $arrayInt;
    public float $float;

    /**
     * @var array
     */
    public array $arrayWithDoc;

    public NestingObject $nestingObject;

    /**
     * @var Map<NestingObject>
     */
    public Map $nestingObjectMap;

    /**
     * @var \Fwhat\JsonMapper\Types\Map<MapObject>
     */
    public Map $mapObjectMap;

    /**
     * @var \Fwhat\JsonMapper\Types\Map<MapObject>
     */
    public ?Map $mapObjectMapNull = null;
}