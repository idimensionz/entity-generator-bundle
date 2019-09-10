<?php
/*
 * iDimensionz/{doctrine-entity-generator}
 * EntityCreatorServiceUnitTest.php
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

namespace iDimensionz\EntityGeneratorBundle\Tests\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\DBAL\Schema\MySqlSchemaManager;
use iDimensionz\EntityGeneratorBundle\Model\ColumnDefinitionModel;
use iDimensionz\EntityGeneratorBundle\Model\EntityPropertyModel;
use iDimensionz\EntityGeneratorBundle\Provider\MysqlColumnDefinitionProvider;
use PHPUnit\Framework\TestCase;

class EntityCreatorServiceUnitTest extends TestCase
{
    /**
     * @var EntityCreatorServiceTestStub
     */
    private $entityCreatorService;
    /**
     * @var MysqlColumnDefinitionProvider
     */
    private $columnDefinitionProvider;
    /**
     * @var \Twig_Environment
     */
    private $twig;
    /**
     * @var ArrayCollection|ColumnDefinitionModel[]
     */
    private $columnDefinitions;
    /**
     * @var ColumnDefinitionModel
     */
    private $columnDefinitionModel;
    /**
     * @var AbstractSchemaManager
     */
    private $mockSchemaManager;
    /**
     * @var array
     */
    private $expectedDatabases;
    /**
     * @var array
     */
    private $expectedTableNames;

    public function setUp()
    {
        parent::setUp();
        $this->columnDefinitionModel = $this->hasColumnDefinitionModel();
        $this->columnDefinitions = new ArrayCollection();
        $this->columnDefinitions->add($this->columnDefinitionModel);
        $this->columnDefinitionProvider = \Phake::mock(MysqlColumnDefinitionProvider::class);
        $this->mockSchemaManager = \Phake::mock(MySqlSchemaManager::class);
        \Phake::when($this->columnDefinitionProvider)->getColumnDefinitions(\Phake::anyParameters())
            ->thenReturn($this->columnDefinitions);
        $this->twig = \Phake::mock(\Twig_Environment::class);
        \Phake::when($this->twig)->render(\Phake::anyParameters())
            ->thenReturn('some class code');
        $this->entityCreatorService = new EntityCreatorServiceTestStub($this->columnDefinitionProvider, $this->mockSchemaManager, $this->twig);
    }

    public function tearDown()
    {
        unset($this->columnDefinitionModel);
        unset($this->columnDefinitions);
        unset($this->columnDefinitionProvider);
        unset($this->twig);
        unset($this->entityCreatorService);
        parent::tearDown();
    }

    public function testConstruct()
    {
        $actualProvider = $this->entityCreatorService->getColumnDefinitionProvider();
        $this->assertProvider($actualProvider);
        $actualTwig = $this->entityCreatorService->getTwig();
        $this->assertTwig($actualTwig);
    }

    public function testTwigGetterAndSetter()
    {
        $expectedTwig = \Phake::mock(\Twig_Environment::class);
        $this->entityCreatorService->setTwig($expectedTwig);
        $actualTwig = $this->entityCreatorService->getTwig();
        $this->assertTwig($actualTwig);
        $this->assertSame($expectedTwig, $actualTwig);
    }

    public function testEntityPropertyModelGetterAndSetter()
    {
        $expectedModel = \Phake::mock(EntityPropertyModel::class);
        $this->entityCreatorService->setEntityPropertyModel($expectedModel);
        $actualModel = $this->entityCreatorService->getEntityPropertyModel();
        $this->assertInstanceOf(EntityPropertyModel::class, $actualModel);
        $this->assertInstanceOf(\Phake_IMock::class, $actualModel);
        $this->assertSame($expectedModel, $actualModel);
    }

    public function testColumnDefinitionProviderGetterAndSetter()
    {
        $expectedProvider = \Phake::mock(MysqlColumnDefinitionProvider::class);
        $this->entityCreatorService->setColumnDefinitionProvider($expectedProvider);
        $actualProvider = $this->entityCreatorService->getColumnDefinitionProvider();
        $this->assertProvider($actualProvider);
        $this->assertSame($expectedProvider, $actualProvider);
    }

    public function testEntityPropertiesGetterAndSetter()
    {
        $expectedValue = ['some properties'];
        $this->entityCreatorService->setEntityProperties($expectedValue);
        $actualValue = $this->entityCreatorService->getEntityProperties();
        $this->assertTrue(is_array($actualValue));
        $this->assertSame($expectedValue, $actualValue);
    }

    public function testSchemaManagerGetterAndSetter()
    {
        $this->hasMockSchemaManager();
        $actualSchemaManager = $this->entityCreatorService->getSchemaManager();
        $this->assertSame($this->mockSchemaManager, $actualSchemaManager);
    }

    public function testAddEntityProperty()
    {
        $expectedModel = \Phake::mock(EntityPropertyModel::class);
        $this->entityCreatorService->addEntityProperty($expectedModel);
        $actualModels = $this->entityCreatorService->getEntityProperties();
        $this->assertTrue(is_array($actualModels));
        $this->assertSame([$expectedModel], $actualModels);
    }

    public function testConvertUnderscoreToCamelCase()
    {
        $values = [
            'SOME_RANDOM_string'    => 'someRandomString',
            ' VALID_COLUMN_name'    => '_validColumnName',
            'ONEWORD'               => 'oneword',
            'alreadyCamelCase'      => 'alreadycamelcase',
            ' lower_FIRST'          => '_lowerFirst',
            'normal_column_name'    => 'normalColumnName',
            'unusual_column_'       => 'unusualColumn',
            'lowercase'             => 'lowercase',
        ];
        foreach ($values as $testValue => $expectedValue) {
            $actualValue = $this->entityCreatorService->convertUnderscoreToCamelCase($testValue);
            $this->assertSame($expectedValue, $actualValue);
        }
    }

    public function testConvertColumnNameToPropertyNameWhenColumnContainsUnderscores()
    {
        $values = [
            'SOME_RANDOM_string'    => 'someRandomString',
            ' VALID_COLUMN_name'    => '_validColumnName',
            ' lower_FIRST'          => '_lowerFirst',
            'normal_column_name'    => 'normalColumnName',
            'unusual_column_'       => 'unusualColumn'
        ];
        foreach ($values as $testValue => $expectedValue) {
            $actualValue = $this->entityCreatorService->convertColumnNameToPropertyName($testValue);
            $this->assertSame($expectedValue, $actualValue);
        }
    }

    public function testConvertColumnNameToPropertyNameWhenColumnDoesNotContainUnderscores()
    {
        $values = [
            'ONEWORD'               => 'oneword',
            'alreadyCamelCase'      => 'alreadycamelcase',
            'lowercase'             => 'lowercase',
        ];
        foreach ($values as $testValue => $expectedValue) {
            $actualValue = $this->entityCreatorService->convertUnderscoreToCamelCase($testValue);
            $this->assertSame($expectedValue, $actualValue);
        }
    }

    public function testConvertColumnDataTypeConvertsToBooleanForTinyint1()
    {
        $this->entityCreatorService->setEntityPropertyModel(new EntityPropertyModel());
        $this->entityCreatorService->convertColumnDataType('tinyint(1)');
        $actualModel = $this->entityCreatorService->getEntityPropertyModel();
        $this->assertSame('bool', $actualModel->getPropertyDataType());
        $this->assertSame('boolean', $actualModel->getDoctrineDataType());
        $this->assertNull($actualModel->getDoctrineLength());
        $this->assertNull($actualModel->getDoctrineScale());
        $this->assertNull($actualModel->getDoctrinePrecision());
    }

    public function testConvertColumnDataTypeConvertsCharTypesToString()
    {
        $this->entityCreatorService->setEntityPropertyModel(new EntityPropertyModel());
        $types = [
            ['VARCHAR(255)', 255],
            ['CHAR(23)', 23],
            ['ENUM(\'first\', \'second\', \'third\')', null],
        ];
        foreach ($types as $type) {
            $length = $type[1];
            $type = $type[0];
            $this->entityCreatorService->convertColumnDataType($type, $length);
            $actualModel = $this->entityCreatorService->getEntityPropertyModel();
            $this->assertSame('string', $actualModel->getPropertyDataType());
            $this->assertSame('string', $actualModel->getDoctrineDataType());
            $this->assertSame($length, $actualModel->getDoctrineLength());
            $this->assertNull($actualModel->getDoctrineScale());
            $this->assertNull($actualModel->getDoctrinePrecision());
        }
    }

    public function testConvertColumnDataTypeConvertsTextTypesToString()
    {
        $this->entityCreatorService->setEntityPropertyModel(new EntityPropertyModel());
        $types = ['TEXT', 'LONGTEXT'];
        foreach ($types as $type) {
            $this->entityCreatorService->convertColumnDataType($type);
            $actualModel = $this->entityCreatorService->getEntityPropertyModel();
            $this->assertSame('string', $actualModel->getPropertyDataType());
            $this->assertSame('text', $actualModel->getDoctrineDataType());
            $this->assertNull($actualModel->getDoctrineLength());
            $this->assertNull($actualModel->getDoctrineScale());
            $this->assertNull($actualModel->getDoctrinePrecision());
        }
    }

    public function testConvertColumnDataTypeConvertsTinyAndSmallIntToInt()
    {
        $this->entityCreatorService->setEntityPropertyModel(new EntityPropertyModel());
        $types = ['TINYINT(2)', 'SMALLINT(5)'];
        foreach ($types as $type) {
            $this->entityCreatorService->convertColumnDataType($type);
            $actualModel = $this->entityCreatorService->getEntityPropertyModel();
            $this->assertSame('int', $actualModel->getPropertyDataType());
            $this->assertSame('smallint', $actualModel->getDoctrineDataType());
            $this->assertNull($actualModel->getDoctrineLength());
            $this->assertNull($actualModel->getDoctrineScale());
            $this->assertNull($actualModel->getDoctrinePrecision());
        }
    }

    public function testConvertColumnDataTypeConvertsMediumAndBigIntToInt()
    {
        $this->entityCreatorService->setEntityPropertyModel(new EntityPropertyModel());
        $types = ['MEDIUMINT(9)', 'BIGINT(15)'];
        foreach ($types as $type) {
            $this->entityCreatorService->convertColumnDataType($type);
            $actualModel = $this->entityCreatorService->getEntityPropertyModel();
            $this->assertSame('int', $actualModel->getPropertyDataType());
            $this->assertSame('bigint', $actualModel->getDoctrineDataType());
            $this->assertNull($actualModel->getDoctrineLength());
            $this->assertNull($actualModel->getDoctrineScale());
            $this->assertNull($actualModel->getDoctrinePrecision());
        }
    }

    public function testConvertColumnDataTypeConvertsIntToInt()
    {
        $this->entityCreatorService->setEntityPropertyModel(new EntityPropertyModel());
        $type = 'INT(10)';
        $this->entityCreatorService->convertColumnDataType($type);
        $actualModel = $this->entityCreatorService->getEntityPropertyModel();
        $this->assertSame('int', $actualModel->getPropertyDataType());
        $this->assertSame('integer', $actualModel->getDoctrineDataType());
        $this->assertNull($actualModel->getDoctrineLength());
        $this->assertNull($actualModel->getDoctrineScale());
        $this->assertNull($actualModel->getDoctrinePrecision());
    }

    public function testConvertColumnDataTypeConvertsFloatAndDecimalToFloat()
    {
        $this->entityCreatorService->setEntityPropertyModel(new EntityPropertyModel());
        $this->entityCreatorService->convertColumnDataType('FLOAT', null, null, null);
        $actualModel = $this->entityCreatorService->getEntityPropertyModel();
        $this->assertSame('float', $actualModel->getPropertyDataType());
        $this->assertSame('float', $actualModel->getDoctrineDataType());
        $this->assertNull($actualModel->getDoctrineLength());
        $this->assertNull($actualModel->getDoctrinePrecision());
        $this->assertNull($actualModel->getDoctrineScale());

        $this->entityCreatorService->convertColumnDataType('DECIMAL(9,2)', null, 9, 2);
        $actualModel = $this->entityCreatorService->getEntityPropertyModel();
        $this->assertSame('float', $actualModel->getPropertyDataType());
        $this->assertSame('decimal', $actualModel->getDoctrineDataType());
        $this->assertNull($actualModel->getDoctrineLength());
        $this->assertSame(9, $actualModel->getDoctrinePrecision());
        $this->assertSame(2, $actualModel->getDoctrineScale());
    }

    public function testConvertColumnDataTypeConvertsDateTimeAndDateToDateTime()
    {
        $this->entityCreatorService->setEntityPropertyModel(new EntityPropertyModel());
        $types = ['DATETIME', 'DATE'];
        foreach ($types as $type) {
            $this->entityCreatorService->convertColumnDataType($type);
            $actualModel = $this->entityCreatorService->getEntityPropertyModel();
            $this->assertSame('\DateTime', $actualModel->getPropertyDataType());
            $this->assertSame('datetime', $actualModel->getDoctrineDataType());
            $this->assertNull($actualModel->getDoctrineLength());
            $this->assertNull($actualModel->getDoctrinePrecision());
            $this->assertNull($actualModel->getDoctrineScale());
        }
    }

    public function testConvertColumnDataTypeConvertsUnknownTypeToString()
    {
        $this->entityCreatorService->setEntityPropertyModel(new EntityPropertyModel());
        $type = 'SOME UNKNOWN TYPE';
        $this->entityCreatorService->convertColumnDataType($type);
        $actualModel = $this->entityCreatorService->getEntityPropertyModel();
        $this->assertSame('string', $actualModel->getPropertyDataType());
        $this->assertSame('string', $actualModel->getDoctrineDataType());
        $this->assertNull($actualModel->getDoctrineLength());
        $this->assertNull($actualModel->getDoctrineScale());
        $this->assertNull($actualModel->getDoctrinePrecision());
    }

    public function testGetAllDatabases()
    {
        $this->hasMockSchemaManager();
        $actualDatabases = $this->entityCreatorService->getAllDatabases();
        $this->assertSame($this->expectedDatabases, $actualDatabases);
        \Phake::verify($this->mockSchemaManager, \Phake::times(1))->listDatabases();
    }

    public function testGetCurrentDatabaseName()
    {
        $this->hasMockSchemaManager();
        $actualDatabaseName = $this->entityCreatorService->getCurrentDatabaseName();
        $this->assertSame($this->expectedDatabases[0], $actualDatabaseName);
    }

    public function testGetTableNames()
    {
        $this->hasMockSchemaManager();
        $actualTableNames = $this->entityCreatorService->getTableNames();
        $this->assertSame($this->expectedTableNames, $actualTableNames);
    }

    public function testMapColumnDefinitionToEntityPropertyForDatetimeColumn()
    {
        $columnDefinitionModel = new ColumnDefinitionModel();
        $columnDefinitionModel->setColumnName('created_date');
        $columnDefinitionModel->setDataType('DATETIME');
        $columnDefinitionModel->setIsNullable(true);
        $entityPropertyModel = $this->entityCreatorService->mapColumnDefinitionToEntityProperty($columnDefinitionModel);
        $this->assertInstanceOf(EntityPropertyModel::class, $entityPropertyModel);
        $this->assertSame('createdDate', $entityPropertyModel->getName());
        $this->assertSame('created_date', $entityPropertyModel->getColumnName());
        $this->assertSame('\DateTime', $entityPropertyModel->getPropertyDataType());
        $this->assertSame('datetime', $entityPropertyModel->getDoctrineDataType());
        $this->assertNull($entityPropertyModel->getDoctrinePrecision());
        $this->assertNull($entityPropertyModel->getDoctrineScale());
        $this->assertSame(true, $entityPropertyModel->isDoctrineNullable());
    }

    public function testMapColumnDefinitionToEntityPropertyForVarcharColumn()
    {
        $columnDefinitionModel = new ColumnDefinitionModel();
        $columnDefinitionModel->setColumnName('username');
        $columnDefinitionModel->setDataType('VARCHAR(20)');
        $columnDefinitionModel->setCharacterMaximumLength(20);
        $columnDefinitionModel->setIsNullable(false);
        $entityPropertyModel = $this->entityCreatorService->mapColumnDefinitionToEntityProperty($columnDefinitionModel);
        $this->assertInstanceOf(EntityPropertyModel::class, $entityPropertyModel);
        $this->assertSame('username', $entityPropertyModel->getName());
        $this->assertSame('username', $entityPropertyModel->getColumnName());
        $this->assertSame('string', $entityPropertyModel->getPropertyDataType());
        $this->assertSame('string', $entityPropertyModel->getDoctrineDataType());
        $this->assertSame(20, $entityPropertyModel->getDoctrineLength());
        $this->assertNull($entityPropertyModel->getDoctrinePrecision());
        $this->assertNull($entityPropertyModel->getDoctrineScale());
        $this->assertFalse($entityPropertyModel->isDoctrineNullable());
    }

    public function testMapColumnDefinitionToEntityPropertyForDecimalColumn()
    {
        $columnDefinitionModel = $this->hasColumnDefinitionModel();
        $entityPropertyModel = $this->entityCreatorService->mapColumnDefinitionToEntityProperty($columnDefinitionModel);
        $this->assertInstanceOf(EntityPropertyModel::class, $entityPropertyModel);
        $this->assertSame('_retailPrice', $entityPropertyModel->getName());
        $this->assertSame(' retail_price', $entityPropertyModel->getColumnName());
        $this->assertSame('float', $entityPropertyModel->getPropertyDataType());
        $this->assertSame('decimal', $entityPropertyModel->getDoctrineDataType());
        $this->assertNull($entityPropertyModel->getDoctrineLength());
        $this->assertEquals(4, $entityPropertyModel->getDoctrinePrecision());
        $this->assertEquals(2, $entityPropertyModel->getDoctrineScale());
        $this->assertFalse($entityPropertyModel->isDoctrineNullable());
    }

    public function testGetEntityPropertiesFromTableColumns()
    {
        $actualEntityProperties = $this->entityCreatorService
            ->getEntityPropertiesFromTableColumns('some_schema', 'some_table');
        $this->assertTrue(is_array($actualEntityProperties));
        $this->assertEquals($this->columnDefinitions->count(), count($actualEntityProperties));
        /**
         * @var EntityPropertyModel $actualEntityProperty
         */
        $actualEntityProperty = $actualEntityProperties[0];
        $this->assertInstanceOf(EntityPropertyModel::class, $actualEntityProperty);
        $this->assertSame('_retailPrice', $actualEntityProperty->getName());
        $this->assertSame(' retail_price', $actualEntityProperty->getColumnName());
        $this->assertSame('float', $actualEntityProperty->getPropertyDataType());
        $this->assertSame('decimal', $actualEntityProperty->getDoctrineDataType());
        $this->assertNull($actualEntityProperty->getDoctrineLength());
        $this->assertEquals(4, $actualEntityProperty->getDoctrinePrecision());
        $this->assertEquals(2, $actualEntityProperty->getDoctrineScale());
        $this->assertFalse($actualEntityProperty->isDoctrineNullable());
    }

    public function testConvertTableToEntityClass()
    {
        $actualClassCode = $this->entityCreatorService->convertTableToEntityClass('some_schema', 'some_table', 'SomeEntity');
        $this->assertEquals('some class code', $actualClassCode);
    }

    /**
     * @param $actualTwig
     */
    private function assertTwig($actualTwig): void
    {
        $this->assertInstanceOf(\Twig_Environment::class, $actualTwig);
        $this->assertInstanceOf(\Phake_IMock::class, $actualTwig);
    }

    /**
     * @param $actualProvider
     */
    private function assertProvider($actualProvider): void
    {
        $this->assertInstanceOf(MysqlColumnDefinitionProvider::class, $actualProvider);
        $this->assertInstanceOf(\Phake_IMock::class, $actualProvider);
    }

    /**
     * @return ColumnDefinitionModel
     */
    private function hasColumnDefinitionModel(): ColumnDefinitionModel
    {
        $columnDefinitionModel = new ColumnDefinitionModel();
        $columnDefinitionModel->setColumnName(' retail_price');
        $columnDefinitionModel->setDataType('DECIMAL(4,2)');
        $columnDefinitionModel->setNumericPrecision(4);
        $columnDefinitionModel->setNumericScale(2);
        $columnDefinitionModel->setIsNullable(false);

        return $columnDefinitionModel;
    }

    private function hasMockSchemaManager()
    {
        $this->mockSchemaManager = \Phake::mock(AbstractSchemaManager::class);

        $this->expectedDatabases = ['db1', 'mysql', 'information_schema'];
        \Phake::when($this->mockSchemaManager)->listDatabases()
            ->thenReturn($this->expectedDatabases);

        \Phake::when($this->mockSchemaManager)->getSchemaSearchPaths()
            ->thenReturn([$this->expectedDatabases[0]]);

        $this->expectedTableNames = ['some_table', 'another_table', 'my_table', 'your_table'];
        \Phake::when($this->mockSchemaManager)->listTableNames()
            ->thenReturn($this->expectedTableNames);

        $this->entityCreatorService->setSchemaManager($this->mockSchemaManager);
    }
}
