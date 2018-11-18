<?php
/*
 * iDimensionz/entity-generator-bundle
 * GenerateEntityCommandUnitTest.php
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

namespace iDimensionz\EntityGeneratorBundle\Tests\Command;

use iDimensionz\EntityGeneratorBundle\Service\EntityCreatorService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\Input;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\Console\Question\Question;

class GenerateEntityCommandUnitTest extends TestCase
{
    /**
     * @var GenerateEntityCommandTestStub
     */
    private $generateEntityCommand;
    /**
     * @var EntityCreatorService|\Phake_IMock
     */
    private $mockEntityCreatorService;
    /**
     * @var array
     */
    private $databases;
    /**
     * @var array
     */
    private $tableNames;
    /**
     * @var QuestionHelper|\Phake_IMock
     */
    private $questionHelper;
    /**
     * @var HelperSet
     */
    private $helperSet;
    /**
     * @var string
     */
    private $entityClassContent;

    protected function setUp()
    {
        parent::setUp();
        $this->hasEntityCreatorService();
        $this->generateEntityCommand = new GenerateEntityCommandTestStub($this->mockEntityCreatorService);
    }

    protected function tearDown()
    {
        unset($this->entityClassContent);
        unset($this->helperSet);
        unset($this->questionHelper);
        unset($this->tableNames);
        unset($this->databases);
        unset($this->mockEntityCreatorService);
        unset($this->generateEntityCommand);
        parent::tearDown();

    }

    public function testConstruct()
    {
        $actualEntityCreatorService = $this->generateEntityCommand->getEntityCreatorService();
        $this->assertSame($this->mockEntityCreatorService, $actualEntityCreatorService);
        $this->assertInstanceOf(\Phake_IMock::class, $actualEntityCreatorService);
    }

    public function testConfigure()
    {
        $this->generateEntityCommand->configure();
        $actualName = $this->generateEntityCommand->getName();
        $this->assertSame('idimensionz:generate:entity', $actualName);
        $actualDescription = $this->generateEntityCommand->getDescription();
        $this->assertSame('Generates the code for an entity class for the specified table.', $actualDescription);

        $actualOption = $this->generateEntityCommand->getDefinition()->getOption('table-name');
        $this->assertInstanceOf(InputOption::class, $actualOption);
        $this->assertSame('table-name', $actualOption->getName());
        $this->assertNull($actualOption->getShortcut());
        $this->assertTrue($actualOption->isValueRequired());
        $this->assertSame('Generate an entity class for this table.', $actualOption->getDescription());
    }

    /**
     * @expectedException \Exception
     */
    public function testValidateTableNamesThrowsExceptionWhenTableNameEmpty()
    {
        $this->generateEntityCommand->setTableNames($this->getTableNames());
        $tableName = '';
        $this->generateEntityCommand->validateTableName($tableName);
    }

    /**
     * @expectedException \Exception
     */
    public function testValidateTableNamesThrowsExceptionWhenTableNameNotValid()
    {
        $this->generateEntityCommand->setTableNames($this->getTableNames());
        $tableName = 'not a valid table name';
        $this->generateEntityCommand->validateTableName($tableName);
    }

    public function testValidateTableNamesReturnsParameterWhenValid()
    {
        $this->generateEntityCommand->setTableNames($this->getTableNames());
        $tableName = $this->tableNames[0];
        $actualTableName = $this->generateEntityCommand->validateTableName($tableName);
        $this->assertSame($tableName, $actualTableName);
    }

    /**
     * @expectedException \Exception
     */
    public function testValidateEntityClassNameThrowsExceptionWhenParameterEmpty()
    {
        $this->generateEntityCommand->validateEntityClassName('');
    }

    /**
     * @expectedException \Exception
     */
    public function testValidateEntityClassNameThrowsExceptionWhenClassExists()
    {
        $this->generateEntityCommand->validateEntityClassName(self::class);
    }

    public function testValidateEntityClassNameReturnsClassNameWhenValid()
    {
        $expectedClassName = 'someNewEntityClass';
        $actualClassName = $this->generateEntityCommand->validateEntityClassName($expectedClassName);
        $this->assertSame($expectedClassName, $actualClassName);
    }

    public function testEntityCreatorServiceGetterAndSetter()
    {
        $entityCreatorService = \Phake::mock(EntityCreatorService::class);
        $this->generateEntityCommand->setEntityCreatorService($entityCreatorService);
        $actualService = $this->generateEntityCommand->getEntityCreatorService();
        $this->assertInstanceOf(EntityCreatorService::class, $actualService);
        $this->assertInstanceOf(\Phake_IMock::class, $actualService);
        $this->assertSame($entityCreatorService, $actualService);
    }

    public function testTableQuestionGetterAndSetterWhenQuestionSupplied()
    {
        $mockQuestion = \Phake::mock(Question::class);
        $this->generateEntityCommand->setTableQuestion($mockQuestion);
        $actualQuestion = $this->generateEntityCommand->getTableQuestion();
        $this->assertInstanceOf(Question::class, $actualQuestion);
        $this->assertInstanceOf(\Phake_IMock::class, $actualQuestion);
        $this->assertSame($mockQuestion, $actualQuestion);
    }

    public function testTableQuestionGetterAndSetterWhenQuestionNotSupplied()
    {
        $this->generateEntityCommand->setTableNames($this->getTableNames());
        $this->generateEntityCommand->setTableQuestion();
        $actualQuestion = $this->generateEntityCommand->getTableQuestion();
        $this->assertDefaultTableQuestion($actualQuestion);
    }

    public function testGetTableQuestionWhenQuestionNotSet()
    {
        $this->generateEntityCommand->setTableNames($this->getTableNames());
        $actualQuestion = $this->generateEntityCommand->getTableQuestion();
        $this->assertDefaultTableQuestion($actualQuestion);
    }

    public function testEntityClassNameGetterAndSetterWhenQuestionSupplied()
    {
        $mockQuestion = \Phake::mock(Question::class);
        $this->generateEntityCommand->setEntityClassNameQuestion($mockQuestion);
        $actualQuestion = $this->generateEntityCommand->getEntityClassNameQuestion();
        $this->assertInstanceOf(Question::class, $actualQuestion);
        $this->assertInstanceOf(\Phake_IMock::class, $actualQuestion);
        $this->assertSame($mockQuestion, $actualQuestion);
    }

    public function testEntityClassNameGetterAndSetterWhenQuestionNotSupplied()
    {
        $this->generateEntityCommand->setEntityClassNameQuestion();
        $actualQuestion = $this->generateEntityCommand->getEntityClassNameQuestion();
        $this->assertDefaultEntityClassNameQuestion($actualQuestion);
    }

    public function testGetEntityClassNameWhenQuestionNotSet()
    {
        $actualQuestion = $this->generateEntityCommand->getEntityClassNameQuestion();
        $this->assertDefaultEntityClassNameQuestion($actualQuestion);
    }

    public function testInteractWhenTableNameNotSupplied()
    {
        $this->hasQuestionHelper();
        $this->generateEntityCommand->setHelperSet($this->helperSet);
        $input = $this->hasInput('');
        $output = \Phake::mock(Output::class);
        $this->hasEntityCreatorService();
        $this->generateEntityCommand->setEntityCreatorService($this->mockEntityCreatorService);

        $this->generateEntityCommand->interact($input, $output);
        \Phake::verify($this->mockEntityCreatorService, \Phake::times(1))->getTableNames();
        $this->assertEquals($this->tableNames, $this->generateEntityCommand->getTableNames());
        \Phake::verify($this->questionHelper, \Phake::times(1))->ask(\Phake::anyParameters());
    }

    public function testInteractWhenEntityClassNameNotSupplied()
    {
        $this->hasQuestionHelper();
        $this->generateEntityCommand->setHelperSet($this->helperSet);
        $input = $this->hasInput('some_table', '');
        $output = \Phake::mock(Output::class);
        $this->hasEntityCreatorService();
        $this->generateEntityCommand->setEntityCreatorService($this->mockEntityCreatorService);

        $this->generateEntityCommand->interact($input, $output);
        \Phake::verify($this->questionHelper, \Phake::times(1))->ask(\Phake::anyParameters());
    }

    public function testExecute()
    {
        $this->hasEntityCreatorService();
        $this->generateEntityCommand->setEntityCreatorService($this->mockEntityCreatorService);
        $input = $this->hasInput();
        $output = \Phake::mock(Output::class);
        $this->generateEntityCommand->execute($input, $output);
        \Phake::verify($this->mockEntityCreatorService, \Phake::times(1))->getCurrentDatabaseName();
        \Phake::verify($this->mockEntityCreatorService, \Phake::times(1))->convertTableToEntityClass(
            $this->getCurrentDatabase(),
            'some_table',
            'entityClassName'
        );
        \Phake::verify($output, \Phake::times(1))->writeln($this->entityClassContent);
    }

    protected function hasEntityCreatorService(): void
    {
        $this->mockEntityCreatorService = \Phake::mock(EntityCreatorService::class);
        \Phake::when($this->mockEntityCreatorService)->getAllDatabases()
            ->thenReturn($this->getDatabases());
        \Phake::when($this->mockEntityCreatorService)->getCurrentDatabaseName()
            ->thenReturn($this->getCurrentDatabase());
        \Phake::when($this->mockEntityCreatorService)->getTableNames()
            ->thenReturn($this->getTableNames());
        $this->entityClassContent = '
            class someEntityClass {
                private $someProperty;
                
                protected function getSomeProperty()
                {
                    return $someProperty;
                }
                
                public function setSomeProperty($someProperty)
                {
                    $this->someProperty = $someProperty;
                }
            }
        ';
        \Phake::when($this->mockEntityCreatorService)->convertTableToEntityClass(\Phake::anyParameters())
            ->thenReturn($this->entityClassContent);
    }

    /**
     * @return array
     */
    protected function getDatabases()
    {
        $this->databases = ['mysql', 'information_schema', 'database1'];
        return $this->databases;
    }

    /**
     * @return string
     */
    protected function getCurrentDatabase()
    {
        return $this->databases[2];
    }

    protected function getTableNames()
    {
        $this->tableNames = ['table1', 'table2', 'table3'];
        return $this->tableNames;
    }

    protected function hasQuestionHelper(): void
    {
        $this->questionHelper = \Phake::mock(QuestionHelper::class);
        $this->helperSet = \Phake::mock(HelperSet::class);
        \Phake::when($this->helperSet)->get('question')
            ->thenReturn($this->questionHelper);
    }

    /**
     * @param $tableName
     * @param $entityClassName
     * @return Input|\Phake_IMock
     */
    protected function hasInput(
        $tableName = 'some_table',
        $entityClassName = 'entityClassName'
    ): Input {
        $input = \Phake::mock(Input::class);
        \Phake::when($input)->getOption('table-name')
            ->thenReturn($tableName);
        \Phake::when($input)->getOption('entity-class-name')
            ->thenReturn($entityClassName);

        return $input;
    }

    /**
     * @param $actualQuestion
     */
    private function assertDefaultEntityClassNameQuestion($actualQuestion): void
    {
        $this->assertInstanceOf(Question::class, $actualQuestion);
        $this->assertNotInstanceOf(\Phake_IMock::class, $actualQuestion);
        $this->assertSame('What is the FQDN of the entity class to create? ', $actualQuestion->getQuestion());
        $this->assertSame([$this->generateEntityCommand, 'validateEntityClassName'], $actualQuestion->getValidator());
    }

    /**
     * @param $actualQuestion
     */
    private function assertDefaultTableQuestion($actualQuestion): void
    {
        $this->assertInstanceOf(Question::class, $actualQuestion);
        $this->assertNotInstanceOf(\Phake_IMock::class, $actualQuestion);
        $this->assertSame('What is the name of the table to generate an entity for? ', $actualQuestion->getQuestion());
        $this->assertSame('', $actualQuestion->getDefault());
        $this->assertSame($this->getTableNames(), $actualQuestion->getAutocompleterValues());
        $this->assertSame([$this->generateEntityCommand, 'validateTableName'], $actualQuestion->getValidator());
    }
}
