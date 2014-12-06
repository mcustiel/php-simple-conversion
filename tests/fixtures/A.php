<?php
namespace Fixtures;

class A {
    private $id;
    private $jsonString;

    public function __construct($id, $jsonString)
    {
        $this->id = $id;
        $this->jsonString = $jsonString;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function getJsonString()
    {
        return $this->jsonString;
    }

    public function setJsonString($jsonString)
    {
        $this->jsonString = $jsonString;
        return $this;
    }
}