<?php
namespace Mcustiel\PhpSimpleConversion\Exception;

class ConverterDoesNotExistException extends PhpSimpleConversionException
{
    const DEFAULT_CODE = 2;

    public function __construct($message, $previous = null)
    {
        parent::__construct($message, self::DEFAULT_CODE, $previous);
    }
}
