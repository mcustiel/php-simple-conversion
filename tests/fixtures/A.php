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
namespace Fixtures;

/**
 * @codeCoverageIgnore
 */
class A
{
    private $id;
    private $jsonString;

    public function __construct($id, $jsonString)
    {
        $this->id = $id;
        $this->jsonString = $jsonString;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function getJsonString()
    {
        return $this->jsonString;
    }

    public function setJsonString($jsonString)
    {
        $this->jsonString = $jsonString;
        return $this;
    }
}
