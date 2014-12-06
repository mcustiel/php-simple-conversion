<?php
namespace Mcustiel\PhpSimpleConversion\Exception;

class ObjectIsNotConverterException extends PhpSimpleConversionException
{
    const DEFAULT_CODE = 1;

    public function __construct($message, $previous = null)
    {
        parent::__construct($message, self::DEFAULT_CODE, $previous);
    }
}
