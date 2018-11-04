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

use DoctrineTest\InstantiatorTestAsset\PharAsset;
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
    private $entityCreatorService;

    public function setUp()
    {
        parent::setUp();
        $this->entityCreatorService = \Phake::mock(EntityCreatorService::class);
        $this->generateEntityCommand = new GenerateEntityCommandTestStub($this->entityCreatorService);
    }

    public function testConstruct()
    {
        $actualEntityCreatorService = $this->generateEntityCommand->getEntityCreatorService();
        $this->assertSame($this->entityCreatorService, $actualEntityCreatorService);
        $this->assertInstanceOf(\Phake_IMock::class, $actualEntityCreatorService);
    }

    public function testConfigure()
    {
        $this->generateEntityCommand->configure();
        $actualName = $this->generateEntityCommand->getName();
        $this->assertSame('idimensionz:generate:entity', $actualName);
        $actualDescription = $this->generateEntityCommand->getDescription();
        $this->assertSame('Generates the code for an entity class for the specified table.', $actualDescription);

        $actualOption = $this->generateEntityCommand->getDefinition()->getOption('schema-name');
        $this->assertInstanceOf(InputOption::class, $actualOption);
        $this->assertSame('schema-name', $actualOption->getName());
        $this->assertNull($actualOption->getShortcut());
        $this->assertTrue($actualOption->isValueRequired());
        $this->assertSame('Schema (database) where the table exists.', $actualOption->getDescription());

        //                'table-name',
        //                null,
        //                InputOption::VALUE_REQUIRED,
        //                'Generate an entity class for this table.'
        $actualOption = $this->generateEntityCommand->getDefinition()->getOption('schema-name');
        $this->assertInstanceOf(InputOption::class, $actualOption);
        $this->assertSame('schema-name', $actualOption->getName());
        $this->assertNull($actualOption->getShortcut());
        $this->assertTrue($actualOption->isValueRequired());
        $this->assertSame('Schema (database) where the table exists.', $actualOption->getDescription());
    }

    public function testValidateSchemaReturnsFalseWhenParameterIsEmpty()
    {
        $this->generateEntityCommand->setDatabases($this->getDatabases());
        $result = $this->generateEntityCommand->validateSchema('');
        $this->assertFalse($result);
    }

    public function testValidateSchemaReturnsFalseWhenParameterNotEmptyAndNotInDatabases()
    {
        $this->generateEntityCommand->setDatabases($this->getDatabases());
        $result = $this->generateEntityCommand->validateSchema('invalid database');
        $this->assertFalse($result);
    }

    public function testValidateSchemaReturnsTrueWhenParameterNotEmptyAndInDatabases()
    {
        $this->generateEntityCommand->setDatabases($this->getDatabases());
        $result = $this->generateEntityCommand->validateSchema($this->getCurrentDatabase());
        $this->assertTrue($result);
    }

    public function testSetSchemaWhenQuestionSupplied()
    {
        $expectedQuestion = \Phake::mock(Question::class);
        $this->generateEntityCommand->setSchemaQuestion($expectedQuestion);
        $actualQuestion = $this->generateEntityCommand->getSchemaQuestion();

        $this->assertInstanceOf(Question::class, $actualQuestion);
        $this->assertInstanceOf(\Phake_IMock::class, $actualQuestion);
        $this->assertSame($expectedQuestion, $actualQuestion);
    }

    public function testSetSchemaWhenQuestionNotSupplied()
    {
        $databases = $this->getDatabases();
        $this->generateEntityCommand->setDatabases($databases);
        $currentDatabase = $this->getCurrentDatabase();
        $this->generateEntityCommand->setCurrentDatabase($currentDatabase);
        $this->generateEntityCommand->setSchemaQuestion();
        $actualQuestion = $this->generateEntityCommand->getSchemaQuestion();
        $this->assertInstanceOf(Question::class, $actualQuestion);
        $expectedQuestion = 'What is the schema (i.e. database) name where the table exists? ' .
            "<info>[{$currentDatabase}]</info> ";
        $this->assertSame($expectedQuestion, $actualQuestion->getQuestion());
        $this->assertSame($currentDatabase, $actualQuestion->getDefault());
        $this->assertSame($databases, $actualQuestion->getAutocompleterValues());
        $expectedCallable = [$this->generateEntityCommand, 'validateSchema'];
        $this->assertSame($expectedCallable, $actualQuestion->getValidator());
    }

    public function testInteractWhenSchemaNameNotSupplied()
    {
        $questionHelper = \Phake::mock(QuestionHelper::class);
        $helperSet = \Phake::mock(HelperSet::class);
        \Phake::when($helperSet)->get('question')
            ->thenReturn($questionHelper);
        $this->generateEntityCommand->setHelperSet($helperSet);
        $input = \Phake::mock(Input::class);
        \Phake::when($input)->getOption('schema-name')
            ->thenReturn('');
        \Phake::when($input)->getOption('table-name')
            ->thenReturn('some_table');
        \Phake::when($input)->getOption('entity-class-name')
            ->thenReturn('some-entity-class');
        $output = \Phake::mock(Output::class);
        $this->hasEntityCreatorService();
        $this->generateEntityCommand->setEntityCreatorService($this->entityCreatorService);

        $this->generateEntityCommand->interact($input, $output);
        \Phake::verify($this->entityCreatorService, \Phake::times(1))->getAllDatabases();
        \Phake::verify($this->entityCreatorService, \Phake::times(1))->getCurrentDatabaseName();
        \Phake::verify($questionHelper, \Phake::times(1))->ask(\Phake::anyParameters());
    }

    public function testInteractWhenTableNameNotSupplied()
    {
        $this->markTestIncomplete();
    }

    public function testInteractWhenEntityClassNameNotSupplied()
    {
        $this->markTestIncomplete();
    }

    protected function hasEntityCreatorService(): void
    {
        $this->entityCreatorService = \Phake::mock(EntityCreatorService::class);
        \Phake::when($this->entityCreatorService)->getAllDatabases()
            ->thenReturn($this->getDatabases());
        \Phake::when($this->entityCreatorService)->getCurrentDatabaseName()
            ->thenReturn($this->getCurrentDatabase());
    }

    /**
     * @return array
     */
    protected function getDatabases()
    {
        return ['mysql', 'schema_information', 'database1'];
    }

    /**
     * @return string
     */
    protected function getCurrentDatabase()
    {
        return 'database1';
    }
}
