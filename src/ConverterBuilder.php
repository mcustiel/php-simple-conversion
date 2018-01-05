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

namespace Mcustiel\Conversion;

use Mcustiel\Conversion\Exception\ObjectIsNotConverterException;

/**
 * Builder used to register Converters into the library. It implments a fluent interface to
 * define the needed values to register the converter.
 *
 * @author mcustiel
 */
class ConverterBuilder
{
    /**
     * @var string
     */
    private $from;
    /**
     * @var string
     */
    private $to;
    /**
     * @var callable
     */
    private $converter;

    /**
     * This class can't be instantiated.
     */
    private function __construct()
    {
    }

    /**
     * Creates a ConverterBuilder instance.
     *
     * @return \Mcustiel\Conversion\ConverterBuilder
     */
    public static function get()
    {
        return new self();
    }

    /**
     * Specifies from which type the converter will convert.
     *
     * @param string $from A string specifying the 'from' type. It must be 'string', 'array' or
     *                     the full name of a class.
     *
     * @throws \InvalidArgumentException When given parameter is not a string
     *
     * @return \Mcustiel\Conversion\ConverterBuilder
     */
    public function from($from)
    {
        if (!is_string($from)) {
            throw new \InvalidArgumentException(
                "'From' parameter should be a string containing 'string', 'array', or a class name"
            );
        }
        $this->from = $from;

        return $this;
    }

    /**
     * Specifies to which type the converter will convert to.
     *
     * @param string $to a string specifying the destination type of the conversion
     *
     * @throws \InvalidArgumentException When given parameter is not a string
     *
     * @return \Mcustiel\Conversion\ConverterBuilder
     */
    public function to($to)
    {
        if (!is_string($to)) {
            throw new \InvalidArgumentException(
                "'To' parameter should be a string containing a type name"
            );
        }
        $this->to = $to;

        return $this;
    }

    /**
     * Returns the value of 'to'.
     *
     * @return string
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * Returns the value of 'from'.
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * Specifies the name of the class that implements the Converter for the given to and from parameters.
     *
     * @param string $class The name of the converter class
     *
     * @return \Mcustiel\Conversion\ConverterBuilder
     */
    public function withImplementation($class)
    {
        $this->converter = function () use ($class) {
            $object = $this->getObjectFromClass($class);

            if (!is_subclass_of($object, Converter::class)) {
                throw new ObjectIsNotConverterException(
                    'Object of type ' . get_class($object) . ' does not implement ' . Converter::class
                );
            }

            return $object;
        };

        return $this;
    }

    /**
     * Returns an instance of the converter.
     *
     * @return mixed the return value of the convert method in the converter implementation
     */
    public function getConverter()
    {
        return call_user_func($this->converter);
    }

    private function getObjectFromClass($class)
    {
        if (is_object($class)) {
            return $class;
        }

        return new $class();
    }
}
