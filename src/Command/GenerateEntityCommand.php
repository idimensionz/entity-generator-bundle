<?php

namespace iDimensionz\EntityGeneratorBundle\Command;

use iDimensionz\EntityGeneratorBundle\Service\EntityCreatorService;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class GenerateEntityCommand extends ContainerAwareCommand
{
    /**
     * @var EntityCreatorService
     */
    private $entityCreatorService;
    /**
     * @var Question
     */
    private $tableQuestion;
    /**
     * @var array
     */
    private $tableNames;
    /**
     * @var Question
     */
    private $entityClassNameQuestion;

    /**
     * GenerateEntityCommand constructor.
     * @param EntityCreatorService $entityCreatorService
     * @param null|string          $name
     */
    public function __construct(EntityCreatorService $entityCreatorService, ?string $name = null)
    {
        parent::__construct($name);
        $this->setEntityCreatorService($entityCreatorService);
    }

    protected function configure()
    {
        $this
            ->setName('idimensionz:generate:entity')
            ->setDescription('Generates the code for an entity class for the specified table.')
//            ->addOption(
//                'schema-name',
//                null,
//                InputOption::VALUE_REQUIRED,
//                'Schema (database) where the table exists.'
//            )
            ->addOption(
                'table-name',
                null,
                InputOption::VALUE_REQUIRED,
                'Generate an entity class for this table.'
            )
            ->addOption(
                'entity-class-name',
                null,
                InputOption::VALUE_REQUIRED,
                'Name of the entity class to create.'
            )
        ;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        parent::interact($input, $output);
        /**
         * @var QuestionHelper $questionHelper
         */
        $questionHelper = $this->getHelper('question');

        $tableName = $input->getOption('table-name');
        if (empty($tableName)) {
            $tableNames = $this->getEntityCreatorService()->getTableNames();
            $this->setTableNames($tableNames);
            $tableQuestion = $this->getTableQuestion();
            $tableName = $questionHelper->ask($input, $output, $tableQuestion);
        }
        $output->writeln("You selected table <info>{$tableName}</info>");
        $input->setOption('table-name', $tableName);

        $entityClassName = $input->getOption('entity-class-name');
        if (empty($entityClassName)) {
            $entityQuestion = $this->getEntityClassNameQuestion();
            $entityClassName = $questionHelper->ask($input, $output, $entityQuestion);
        }
        $input->setOption('entity-class-name', $entityClassName);
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return int|null|void
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $schemaName = $this->getEntityCreatorService()->getCurrentDatabaseName();
        $tableName = $input->getOption('table-name');
        $entityName = $input->getOption('entity-class-name');
        $entityClassCode = $this->getEntityCreatorService()->convertTableToEntityClass(
            $schemaName,
            $tableName,
            $entityName
        );
        $output->writeln($entityClassCode);
    }

    /**
     * @param string $inputTableName
     * @return bool
     * @throws \Exception
     */
    public function validateTableName(string $inputTableName)
    {
        $tableNames = $this->getTableNames();
        if (empty($inputTableName) || (!empty($inputTableName) && !in_array($inputTableName, $tableNames))) {
            throw new \Exception('Table name must be one of ' . implode(', ', $tableNames));
        }

        return $inputTableName;
    }

    /**
     * @param string $entityClassName
     * @return string
     * @throws \Exception
     */
    public function validateEntityClassName(string $entityClassName)
    {
        if (empty($entityClassName) || class_exists($entityClassName)) {
            throw new \Exception('entity-class-name is a required value and must not already exist');
        }

        return $entityClassName;
    }

    /**
     * @return EntityCreatorService
     */
    protected function getEntityCreatorService(): EntityCreatorService
    {
        return $this->entityCreatorService;
    }

    /**
     * @param EntityCreatorService $entityCreatorService
     */
    public function setEntityCreatorService(EntityCreatorService $entityCreatorService): void
    {
        $this->entityCreatorService = $entityCreatorService;
    }

    /**
     * @param null|Question $tableQuestion
     */
    public function setTableQuestion(?Question $tableQuestion = null)
    {
        if (is_null($tableQuestion)) {
            $tableNames = $this->getTableNames();
            $this->tableQuestion = new Question('What is the name of the table to generate an entity for? ', '');
            $this->tableQuestion->setAutocompleterValues($tableNames);
            $validator = [$this, 'validateTableName'];
            $this->tableQuestion->setValidator($validator);
        } else {
            $this->tableQuestion = $tableQuestion;
        }
    }

    /**
     * @return Question
     */
    protected function getTableQuestion()
    {
        if (!$this->tableQuestion instanceof Question) {
            $this->setTableQuestion();
        }

        return $this->tableQuestion;
    }

    /**
     * @param Question|null $question
     */
    public function setEntityClassNameQuestion(Question $question = null)
    {
        if (is_null($question)) {
            $this->entityClassNameQuestion = new Question('What is the FQDN of the entity class to create? ');
            $validator = [$this, 'validateEntityClassName'];
            $this->entityClassNameQuestion->setValidator($validator);
        } else {
            $this->entityClassNameQuestion = $question;
        }
    }

    /**
     * @return Question
     */
    protected function getEntityClassNameQuestion(): Question
    {
        if (!$this->entityClassNameQuestion instanceof Question) {
            $this->setEntityClassNameQuestion();
        }

        return $this->entityClassNameQuestion;
    }

    /**
     * @return array
     */
    public function getTableNames(): array
    {
        return $this->tableNames;
    }

    /**
     * @param array $tableNames
     */
    public function setTableNames(array $tableNames): void
    {
        $this->tableNames = $tableNames;
    }
}
