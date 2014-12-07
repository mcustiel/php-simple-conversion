<?php
/**
 * This file is part of php-simple-conversion.
 *
 * php-simple-conversion is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * php-simple-conversion is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with php-simple-conversion.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace Mcustiel\PhpSimpleConversion;

use Mcustiel\PhpSimpleConversion\Exception\ConverterDoesNotExistException;

/**
 * Singleton class the holds all the registered converters, and allows to access them.
 *
 * @author mcustiel
 */
class ConverterContainer
{
    /**
     *
     * @var ConverterContainer
     */
    private static $instance;

    /**
     *
     * @var ConverterBuilder|Converter[][]
     */
    private $converters;

    /**
     * This is singleton, can't be instantiated directly.
     */
    private function __construct()
    {
    }

    /**
     * Get the instance of this class.
     *
     * @return \Mcustiel\PhpSimpleConversion\ConverterContainer
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Registers a converter.
     *
     * @param ConverterBuilder $converter The builder of the converter to register.
     * @throws \InvalidArgumentException If from or to are unset.
     */
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

    /**
     * Access the implementation of the converter for the given from and to parameters.
     *
     * @param string $from The type from which the converter converts.
     * @param string $to   The type to which the converter converts to.
     *
     * @throws Mcustiel\PhpSimpleConversion\Exception\ConverterDoesNotExistException If the
     *                                                                               converter
     *                                                                               implementation
     *                                                                               were not set in
     *                                                                               the builder
     *
     * @return Converter
     */
    public function getConverter($from, $to)
    {
        if (!isset($this->converters[$from][$to])) {
            throw new ConverterDoesNotExistException(
                "Converter from {$from} to {$to} does not exist"
            );
        }
        $converter = $this->converters[$from][$to];
        if ($converter instanceof ConverterBuilder) {
            $this->converters[$from][$to] = $converter->getConverter();
        }

        return $this->converters[$from][$to];
    }
}
