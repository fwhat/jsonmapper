<?php


namespace Fwhat\JsonMapper\Tests\Stub\SetObject;


use Fwhat\JsonMapper\Tests\Stub\ArrayHelper;

class SetObject {
    use ArrayHelper;

    public bool $bool;
    public int $int;
    public ?string $string = null;
    public array $arrayString;
    public array $arrayInt;
    public float $float;

    /**
     * @var array
     */
    public array $arrayWithDoc;

    public function setString (string $str) {
        $this->string = "from_set_".$str;
    }
}