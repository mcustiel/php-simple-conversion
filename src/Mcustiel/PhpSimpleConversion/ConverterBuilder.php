<?php
namespace Mcustiel\PhpSimpleConversion;

use Mcustiel\PhpSimpleConversion\Exception\ObjectIsNotConverterException;

class ConverterBuilder
{
    /**
     *
     * @var string
     */
    private $from;
    /**
     *
     * @var string
     */
    private $to;
    /**
     *
     * @var callable
     */
    private $converter;

    private function __construct()
    {
    }

    public static function get()
    {
        return new self();
    }

    public function from($from)
    {
        if (!is_string($from)) {
            throw new \InvalidArgumentException("'From' parameter should be a string containing "
                . "'string', 'array', or a class name");
        }
        $this->from = $from;

        return $this;
    }

    public function to($to)
    {
        if (!is_string($to)) {
            throw new \InvalidArgumentException("'To' parameter should be a string containing "
                . "'string', 'array', or a class name");
        }
        $this->to = $to;

        return $this;
    }

    public function getTo()
    {
        return $this->to;
    }

    public function getFrom()
    {
        return $this->from;
    }

    public function withImplementation($class)
    {
        $this->converter = function () use ($class)
        {
            $object = new $class;
            if (! is_subclass_of($object, Converter::class)) {
                throw new ObjectIsNotConverterException(
                    "Object of type {$class} does not implement " . Converter::class);
            }

            return $object;
        };

        return $this;
    }

    public function getConverter()
    {
        return call_user_func($this->converter);
    }
}
