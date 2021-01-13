<?php


namespace Fwhat\JsonMapper\Tests\Stub\DocArrayObject;


use Fwhat\JsonMapper\Tests\Stub\ArrayHelper;
use Fwhat\JsonMapper\Tests\Stub\NestingObject\NestingObject;

class DocArrayObject {
    use ArrayHelper;

    public bool $bool;
    public int $int;
    public string $string;
    /**
     * @var array<string>
     */
    public array $arrayString;

    /**
     * @var int[]
     */
    public array $arrayInt;
    public float $float;

    /**
     * @var array
     */
    public array $arrayWithDoc;

    public NestingObject $nestingObject;

    /**
     * @var NestingObject[] withDoc
     */
    public array $nestingObjectArray;

    /**
     * @var NestingObject[][] withDoc
     */
    public array $nestingObjectArray2;

    /**
     * @var NestingObject[][][] withDoc
     */
    public array $nestingObjectArray3;

    /**
     * @var DocArrayObject[]withDoc
     */
    public array $docArrayObjectArray;

    /**
     * @var array<NestingObject>
     */
    public array $nestingObjectArrayDoc;

    /**
     * @var array|null
     */
    public ?array $nestingObjectArrayNull = null;
}