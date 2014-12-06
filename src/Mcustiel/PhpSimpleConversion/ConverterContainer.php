<?php
namespace Mcustiel\PhpSimpleConversion;

use Mcustiel\PhpSimpleConversion\Exception\ConverterDoesNotExistException;
class ConverterContainer
{
    /**
     *
     * @var ConverterContainer
     */
    private static $instance;

    /**
     *
     * @var ConverterBuilder[][]
     */
    private $converters;

    private function __construct()
    {
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    public function addConverter(ConverterBuilder $converter)
    {
        if ($converter->getFrom() == null) {
            throw new \InvalidArgumentException("From is unset");
        }
        if ($converter->getTo() == null) {
            throw new \InvalidArgumentException("To is unset");
        }
        $this->converters[$converter->getFrom()][$converter->getTo()] = $converter;
    }

    public function getConverter($from, $to)
    {
        if (!isset($this->converters[$from][$to])) {
            throw new ConverterDoesNotExistException(
                "Converter from {$from} to {$to} does not exist"
            );
        }

        return $this->converters[$from][$to]->getConverter();
    }
}
