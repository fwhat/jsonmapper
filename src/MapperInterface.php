<?php


namespace Fwhat\JsonMapper;


interface MapperInterface {
    public function map($data, object $object);
}