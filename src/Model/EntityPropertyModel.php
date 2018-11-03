<?php
/*
 * iDimensionz/doctrine-entity-generator
 * EntityProperty.php
 *  
 * The MIT License (MIT)
 * 
 * Copyright (c) 2018 iDimensionz
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
*/

namespace iDimensionz\EntityGeneratorBundle\Model;

class EntityPropertyModel
{
    /**
     * @var string  Class property name
     */
    private $name;
    /**
     * @var string
     */
    private $columnName;
    /**
     * @var string
     */
    private $propertyDataType;
    /**
     * @var string
     */
    private $doctrineDataType;
    /**
     * @var int|null
     */
    private $doctrineLength;
    /**
     * @var int|null
     */
    private $doctrinePrecision;
    /**
     * @var int|null
     */
    private $doctrineScale;
    /**
     * @var bool
     */
    private $isDoctrineNullable;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getColumnName(): string
    {
        return $this->columnName;
    }

    /**
     * @param string $columnName
     */
    public function setColumnName(string $columnName): void
    {
        $this->columnName = $columnName;
    }

    /**
     * @return string
     */
    public function getPropertyDataType(): string
    {
        return $this->propertyDataType;
    }

    /**
     * @param string $propertyDataType
     */
    public function setPropertyDataType(string $propertyDataType): void
    {
        $this->propertyDataType = $propertyDataType;
    }

    /**
     * @return string
     */
    public function getDoctrineDataType(): string
    {
        return $this->doctrineDataType;
    }

    /**
     * @param string $doctrineDataType
     */
    public function setDoctrineDataType(string $doctrineDataType): void
    {
        $this->doctrineDataType = $doctrineDataType;
    }

    /**
     * @return int|null
     */
    public function getDoctrineLength(): ?int
    {
        return $this->doctrineLength;
    }

    /**
     * @param int|null $doctrineLength
     */
    public function setDoctrineLength(?int $doctrineLength): void
    {
        $this->doctrineLength = $doctrineLength;
    }

    /**
     * @return int|null
     */
    public function getDoctrinePrecision(): ?int
    {
        return $this->doctrinePrecision;
    }

    /**
     * @param int|null $doctrinePrecision
     */
    public function setDoctrinePrecision(?int $doctrinePrecision): void
    {
        $this->doctrinePrecision = $doctrinePrecision;
    }

    /**
     * @return int|null
     */
    public function getDoctrineScale(): ?int
    {
        return $this->doctrineScale;
    }

    /**
     * @param int|null $doctrineScale
     */
    public function setDoctrineScale(?int $doctrineScale): void
    {
        $this->doctrineScale = $doctrineScale;
    }

    /**
     * @return bool
     */
    public function isDoctrineNullable(): bool
    {
        return $this->isDoctrineNullable;
    }

    /**
     * @param bool $isDoctrineNullable
     */
    public function setIsDoctrineNullable(bool $isDoctrineNullable): void
    {
        $this->isDoctrineNullable = $isDoctrineNullable;
    }
}
