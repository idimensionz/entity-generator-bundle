<?php
/*
 * iDimensionz/{doctrine-entity-generator}
 * ColumnDefinitionModelUnitTest.php
 *  
 * The MIT License (MIT)
 * 
 * Copyright (c) 2018 Dimensionz
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

namespace iDimensionz\Tests\Model;

use iDimensionz\Model\ColumnDefinitionModel;
use iDimensionz\Tests\UnitTestDataProviderTrait;
use PHPUnit\Framework\TestCase;

class ColumnDefinitionModelUnitTest extends TestCase
{
    use UnitTestDataProviderTrait;

    /**
     * @var ColumnDefinitionModel
     */
    private $columnDefinitionModel;

    public function setUp()
    {
        parent::setUp();
        $this->columnDefinitionModel = new ColumnDefinitionModel();
    }

    public function tearDown()
    {
        unset($this->columnDefinitionModel);
        parent::tearDown();
    }

    /**
     * @dataProvider stringProvider
     * @param        $testValue
     * @param string $expectedValue
     */
    public function testColumnNameGetterAndSetter($testValue, $expectedValue)
    {
        $this->columnDefinitionModel->setColumnName($testValue);
        $actualValue = $this->columnDefinitionModel->getColumnName();
        $this->assertSame($expectedValue, $actualValue);
    }

    /**
     * @dataProvider stringProvider
     * @param        $testValue
     * @param string $expectedValue
     */
    public function testDataTypeGetterAndSetter($testValue, string $expectedValue)
    {
        $this->columnDefinitionModel->setDataType($testValue);
        $actualValue = $this->columnDefinitionModel->getDataType();
        $this->assertSame($expectedValue, $actualValue);
    }

    /**
     * @dataProvider booleanProvider
     * @param      $testValue
     * @param bool $expectedValue
     */
    public function testIsNullableGetterAndSetter($testValue, bool $expectedValue)
    {
        $this->columnDefinitionModel->setIsNullable($testValue);
        $actualValue = $this->columnDefinitionModel->isNullable();
        $this->assertSame($expectedValue, $actualValue);
    }

    /**
     * @dataProvider integerProviderWithNull
     * @param          $testValue
     * @param int|null $expectedValue
     */
    public function testCharacterMaximumLengthGetterAndSetter($testValue, ?int $expectedValue)
    {
        $this->columnDefinitionModel->setCharacterMaximumLength($testValue);
        $actualValue = $this->columnDefinitionModel->getCharacterMaximumLength();
        $this->assertSame($expectedValue, $actualValue);
    }

    /**
     * @dataProvider integerProviderWithNull
     * @param          $testValue
     * @param int|null $expectedValue
     */
    public function testSetNumericScale($testValue, ?int $expectedValue)
    {
        $this->columnDefinitionModel->setNumericScale($testValue);
        $actualValue = $this->columnDefinitionModel->getNumericScale();
        $this->assertSame($expectedValue, $actualValue);
    }

    /**
     * @dataProvider integerProviderWithNull
     * @param          $testValue
     * @param int|null $expectedValue
     */
    public function testSetNumericPrecision($testValue, ?int $expectedValue)
    {
        $this->columnDefinitionModel->setNumericPrecision($testValue);
        $actualValue = $this->columnDefinitionModel->getNumericScale();
        $this->assertSame($expectedValue, $actualValue);
    }
}
