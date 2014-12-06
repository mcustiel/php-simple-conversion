<?php
namespace Fixtures;

use Mcustiel\PhpSimpleConversion\Converter;

class AToBConverter implements Converter
{
    public function convert($a)
    {
        if (! ($a instanceof A)) {
            throw new \InvalidArgumentException("Should convert only from A");
        }
        $return = new B();

        $return->setId($a->getId());
        $object = json_decode($a->getJsonString());
        $return->setFirstName($object->firstName);
        $return->setLastName($object->lastName);
        $return->setAge($object->age);

        return $return;
    }
}
