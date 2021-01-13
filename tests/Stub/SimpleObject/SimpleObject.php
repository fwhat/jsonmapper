<?php


namespace Fwhat\JsonMapper\Tests\Stub\SimpleObject;



use Fwhat\JsonMapper\Tests\Stub\ArrayHelper;

class SimpleObject {
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
}