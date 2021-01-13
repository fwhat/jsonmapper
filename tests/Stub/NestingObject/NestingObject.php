<?php


namespace Fwhat\JsonMapper\Tests\Stub\NestingObject;


use Fwhat\JsonMapper\Tests\Stub\ArrayHelper;

class NestingObject {
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
}