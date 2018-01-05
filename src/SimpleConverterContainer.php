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

use Mcustiel\Conversion\Exception\ConverterDoesNotExistException;

/**
 * Singleton class the holds all the registered converters, and allows to access them.
 *
 * @author mcustiel
 */
class SimpleConverterContainer implements ConverterContainer
{
    /**
     * @var ConverterBuilder|Converter[][]
     */
    private $converters = [];

    /**
     * Registers a converter.
     *
     * @param ConverterBuilder $converterBuilder the builder of the converter to register
     *
     * @throws \InvalidArgumentException if from or to are unset
     */
    public function addConverter(ConverterBuilder $converterBuilder)
    {
        $this->validateBuilder($converterBuilder);
        if (!isset($this->converters[$converterBuilder->getFrom()])) {
            $this->converters[$converterBuilder->getFrom()] = [];
        }
        $this->converters[$converterBuilder->getFrom()][$converterBuilder->getTo()] = $converterBuilder;
    }

    /**
     * Access the implementation of the converter for the given from and to parameters.
     *
     * @param string $from the type from which the converter converts
     * @param string $to   the type to which the converter converts to
     *
     * @throws \Mcustiel\Conversion\Exception\ConverterDoesNotExistException
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

    /**
     * @param converter
     * @param mixed $converter
     */
    private function validateBuilder($converter)
    {
        if (empty($converter->getFrom())) {
            throw new \InvalidArgumentException('From is unset');
        }
        if (empty($converter->getTo())) {
            throw new \InvalidArgumentException('To is unset');
        }
    }
}
