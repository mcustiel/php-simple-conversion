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
