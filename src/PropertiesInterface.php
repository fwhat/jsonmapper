<?php


namespace Fwhat\JsonMapper;


interface PropertiesInterface {
    /**
     * @return PropertyRef[]
     */
    public function getProperties(): array;
}