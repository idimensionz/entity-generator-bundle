<?php
/*
 * iDimensionz/{doctrine-entity-generator}
 * EntityPropertyModelUnitTest.php
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

use iDimensionz\Model\EntityPropertyModel;
use iDimensionz\Tests\UnitTestDataProviderTrait;
use PHPUnit\Framework\TestCase;

class EntityPropertyModelUnitTest extends TestCase
{
    use UnitTestDataProviderTrait;

    /**
     * @var EntityPropertyModel
     */
    private $entityPropertyModel;

    public function setUp()
    {
        parent::setUp();
        $this->entityPropertyModel = new EntityPropertyModel();
    }

    public function tearDown()
    {
        unset($this->entityPropertyModel);
        parent::tearDown();
    }

    /**
     * @dataProvider stringProvider
     * @param        $testValue
     * @param string $expectedValue
     */
    public function testColumnNameGetterAndSetter($testValue, string $expectedValue)
    {
        $this->entityPropertyModel->setColumnName($testValue);
        $actualValue = $this->entityPropertyModel->getColumnName();
        $this->assertSame($expectedValue, $actualValue);
    }

    /**
     * @dataProvider stringProvider
     * @param        $testValue
     * @param string $expectedValue
     */
    public function testGetName($testValue, string $expectedValue)
    {
        $this->entityPropertyModel->setName($testValue);
        $actualValue = $this->entityPropertyModel->getName();
        $this->assertSame($expectedValue, $actualValue);
    }

    /**
     * @dataProvider integerProviderWithNull
     * @param          $testValue
     * @param int|null $expectedValue
     */
    public function testGetDoctrineScale($testValue, ?int $expectedValue)
    {
        $this->entityPropertyModel->setDoctrineScale($testValue);
        $actualValue = $this->entityPropertyModel->getDoctrineScale();
        $this->assertSame($expectedValue, $testValue);
    }

    /**
     * @dataProvider integerProviderWithNull
     */
    public function testGetDoctrinePrecision($testValue, ?int $expectedValue)
    {
        $this->entityPropertyModel->setDoctrinePrecision($testValue);
        $actualValue = $this->entityPropertyModel->getDoctrinePrecision();
        $this->assertSame($expectedValue, $actualValue);
    }

    /**
     * @dataProvider stringProvider
     * @param        $testValue
     * @param string $expectedValue
     */
    public function testGetDoctrineDataType($testValue, string $expectedValue)
    {
        $this->entityPropertyModel->setDoctrineDataType($testValue);
        $actualValue = $this->entityPropertyModel->getDoctrineDataType();
        $this->assertSame($expectedValue, $actualValue);
    }

    /**
     * @dataProvider booleanProvider
     */
    public function testIsDoctrineNullable($testValue, $expectedValue)
    {
        $this->entityPropertyModel->setIsDoctrineNullable($testValue);
        $actualValue = $this->entityPropertyModel->isDoctrineNullable();
        $this->assertSame($expectedValue, $actualValue);
    }

    /**
     * @dataProvider integerProviderWithNull
     * @param             $testValue
     * @param null|int $expectedValue
     */
    public function testGetDoctrineLength($testValue, ?int $expectedValue)
    {
        $this->entityPropertyModel->setDoctrineLength($testValue);
        $actualValue = $this->entityPropertyModel->getDoctrineLength();
        $this->assertSame($expectedValue, $actualValue);
    }

    /**
     * @dataProvider stringProvider
     * @param        $testValue
     * @param string $expectedValue
     */
    public function testGetPropertyDataType($testValue, string $expectedValue)
    {
        $this->entityPropertyModel->setPropertyDataType($testValue);
        $actualValue = $this->entityPropertyModel->getPropertyDataType();
        $this->assertSame($expectedValue, $actualValue);
    }
}
