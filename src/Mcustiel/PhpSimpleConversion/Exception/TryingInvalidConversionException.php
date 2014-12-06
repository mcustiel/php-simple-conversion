<?php
namespace Mcustiel\PhpSimpleConversion\Exception;

class TryingInvalidConversionException extends PhpSimpleConversionException
{
    const DEFAULT_CODE = 3;

    public function __construct($message, $previous = null)
    {
        parent::__construct($message, self::DEFAULT_CODE, $previous);
    }
}
