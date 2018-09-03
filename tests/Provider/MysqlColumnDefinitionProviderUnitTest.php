<?php
/*
 * iDimensionz/{doctrine-entity-generator}
 * MysqlColumnDefinitionProviderUnitTest.php
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

namespace iDimensionz\EntityGeneratorBundle\Tests\Provider;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\ResultStatement;
use iDimensionz\EntityGeneratorBundle\Model\ColumnDefinitionModel;
use PHPUnit\Framework\TestCase;

class MysqlColumnDefinitionProviderUnitTest extends TestCase
{
    /**
     * @var MysqlColumnDefinitionProviderTestStub
     */
    private $mysqlColumnDefinitionProvider;
    /**
     * @var Connection
     */
    private $mockConnection;
    /**
     * @var ResultStatement
     */
    private $mockResultStatement;
    private $databaseColumnInfo;

    public function setUp()
    {
        parent::setUp();
        $this->databaseColumnInfo =
            [
                'COLUMN_TYPE'               => 'something',
                'COLUMN_NAME'               => 'something else',
                'IS_NULLABLE'               => 'YES',
                'CHARACTER_MAXIMUM_LENGTH'  => 255,
                'NUMERIC_SCALE'             => 8,
                'NUMERIC_PRECISION'         => 2
            ];

        $this->mockResultStatement = \Phake::mock(ResultStatement::class);
        \Phake::when($this->mockResultStatement)->fetchAll()
            ->thenReturn([$this->databaseColumnInfo]);
        $this->mockConnection = \Phake::mock(Connection::class);
        \Phake::when($this->mockConnection)->executeQuery(\Phake::anyParameters())
            ->thenReturn($this->mockResultStatement);
        $this->mysqlColumnDefinitionProvider = new MysqlColumnDefinitionProviderTestStub($this->mockConnection);
    }

    public function testConstruct()
    {
        $actualConnection = $this->mysqlColumnDefinitionProvider->getConnection();
        $this->assertInstanceOf(Connection::class, $actualConnection);
        $this->assertInstanceOf(\Phake_IMock::class, $actualConnection);
    }

    public function testConnectionGetterAndSetter()
    {
        $this->mockConnection = \Phake::mock(Connection::class);
        $this->mysqlColumnDefinitionProvider->setConnection($this->mockConnection);
        $actualConnection = $this->mysqlColumnDefinitionProvider->getConnection();
        $this->assertInstanceOf(Connection::class, $actualConnection);
        $this->assertInstanceOf(\Phake_IMock::class, $actualConnection);
        $this->assertSame($this->mockConnection, $actualConnection);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testMapDatabaseColumnDefinitionsThrowsInvalidArgumentExceptionWhenColumnNameMissing()
    {
        $item['COLUMN_TYPE'] = 'something';
        $item['IS_NULLABLE'] = 'YES';
        $this->mysqlColumnDefinitionProvider->mapDatabaseColumnInfoToColumnDefinition($item);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testMapDatabaseColumnDefinitionsThrowsInvalidArgumentExceptionWhenColumnTypeMissing()
    {
        $item['COLUMN_NAME'] = 'something';
        $item['IS_NULLABLE'] = 'YES';
        $this->mysqlColumnDefinitionProvider->mapDatabaseColumnInfoToColumnDefinition($item);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testMapDatabaseColumnDefinitionsThrowsInvalidArgumentExceptionWhenIsNullabeMissing()
    {
        $item['COLUMN_TYPE'] = 'something';
        $item['COLUMN_NAME'] = 'something else';
        $this->mysqlColumnDefinitionProvider->mapDatabaseColumnInfoToColumnDefinition($item);
    }

    public function testMapDatabaseColumnDefinitionsReturnsModelWithNullValuesWhenOptionalIndexesNotSet()
    {
        $item = [
            'COLUMN_TYPE' => 'something',
            'COLUMN_NAME' => 'something else',
            'IS_NULLABLE' => 'YES'
        ];
        $actualModel = $this->mysqlColumnDefinitionProvider->mapDatabaseColumnInfoToColumnDefinition($item);
        $this->assertInstanceOf(ColumnDefinitionModel::class, $actualModel);
        $this->assertSame($item['COLUMN_TYPE'], $actualModel->getDataType());
        $this->assertSame($item['COLUMN_NAME'], $actualModel->getColumnName());
        $this->assertSame(true, $actualModel->isNullable());
        $this->assertNull($actualModel->getCharacterMaximumLength());
        $this->assertNull($actualModel->getNumericScale());
        $this->assertNull($actualModel->getNumericPrecision());
    }

    public function testMapDatabaseColumnDefinitionsReturnsModelWithValuesWhenOptionalIndexesSet()
    {
        $item = [
            'COLUMN_TYPE'               => 'something',
            'COLUMN_NAME'               => 'something else',
            'IS_NULLABLE'               => 'YES',
            'CHARACTER_MAXIMUM_LENGTH'  => 255,
            'NUMERIC_SCALE'             => 8,
            'NUMERIC_PRECISION'         => 2
        ];
        $actualModel = $this->mysqlColumnDefinitionProvider->mapDatabaseColumnInfoToColumnDefinition($item);
        $this->assertInstanceOf(ColumnDefinitionModel::class, $actualModel);
        $this->assertSame($item['COLUMN_TYPE'], $actualModel->getDataType());
        $this->assertSame($item['COLUMN_NAME'], $actualModel->getColumnName());
        $this->assertSame(true, $actualModel->isNullable());
        $this->assertSame($item['CHARACTER_MAXIMUM_LENGTH'], $actualModel->getCharacterMaximumLength());
        $this->assertSame($item['NUMERIC_PRECISION'], $actualModel->getNumericPrecision());
        $this->assertSame($item['NUMERIC_SCALE'], $actualModel->getNumericScale());
    }

    public function testGetColumnDefinitions()
    {
        $schemaName = 'my_schema';
        $tableName = 'my_table';
        $expectedSql = 'SELECT COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE, CHARACTER_MAXIMUM_LENGTH, NUMERIC_PRECISION, NUMERIC_SCALE
            FROM INFORMATION_SCHEMA.COLUMNS
           WHERE TABLE_SCHEMA = ?
             AND TABLE_NAME = ?';
        /**
         * @var ArrayCollection $actualValue
         */
        $actualValue = $this->mysqlColumnDefinitionProvider->getColumnDefinitions($schemaName, $tableName);
        \Phake::verify($this->mockConnection->executeQuery($expectedSql, [$schemaName, $tableName]));
        $this->assertInstanceOf(ArrayCollection::class, $actualValue);
        $this->assertEquals(1, $actualValue->count());
        $actualModel = $actualValue->current();
        $this->assertInstanceOf(ColumnDefinitionModel::class, $actualModel);
    }
}
