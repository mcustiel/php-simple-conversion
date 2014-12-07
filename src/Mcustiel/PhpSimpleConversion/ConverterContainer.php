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
