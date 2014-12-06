<?php
namespace Mcustiel\PhpSimpleConversion;

use Mcustiel\PhpSimpleConversion\Exception\TryingInvalidConversionException;

class ConversionService
{
    private $container;

    public function __construct()
    {
        $this->container = ConverterContainer::getInstance();
    }

    public function convert($object, $toClass)
    {
        $from = $this->getTypeOf($object);
        $converter = $this->container->getConverter($from, $toClass);
        
        return $converter->convert($object);
    }

    private function gettypeOf($object)
    {
        $type = gettype($object);
        switch ($type) {
            case 'string':
            case 'array':
                return $type;
            case 'object':
                return get_class($object);
            default:
                throw new TryingInvalidConversionException(
                    "Trying to convert from '{$type}'. " .
                         'Can only convert from string, array or object');
        }
    }

    public function registerConverter(ConverterBuilder $builder)
    {
        $this->container->addConverter($builder);
    }
}
