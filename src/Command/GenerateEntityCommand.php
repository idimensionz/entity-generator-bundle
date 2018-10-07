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
            ->addOption(
                'schema-name',
                null,
                InputOption::VALUE_REQUIRED,
                'Schema (database) where the table exists.'
            )
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
        $schemaName = $input->getOption('schema-name');
        if (empty($schemaName)) {
            $databases = $this->getEntityCreatorService()->getAllDatabases();
            $currentDatabase = $this->getEntityCreatorService()->getCurrentDatabaseName();
            $schemaQuestion = new Question("What is the schema (i.e. database) name where the table exists? <info>[{$currentDatabase}]</info> ", $currentDatabase);
            $schemaQuestion->setAutocompleterValues($databases);
            $schemaQuestion->setValidator(
                function ($response) use ($databases) {
                    return !empty($response) && in_array($response, $databases);
                }
            );
            $schemaNameChoice = $questionHelper->ask($input, $output, $schemaQuestion);
            $schemaName = $databases[$schemaNameChoice+1];
        }
        $output->writeln("You chose: <info>{$schemaName}</info>");
        $input->setOption('schema-name', $schemaName);

        $tableName = $input->getOption('table-name');
        if (empty($tableName)) {
            $tableNames = $this->getEntityCreatorService()->getTableNames();
            $tableQuestion = new Question('What is the name of the table to generate an entity for? ', '');
            $tableQuestion->setAutocompleterValues($tableNames);
            $tableQuestion->setValidator(
                function ($inputTableName) use ($tableNames) {
                    if (empty($inputTableName) || (!empty($inputTableName) && !in_array($inputTableName, $tableNames))) {
                        throw new \RuntimeException('table-name must be a valid table name.');
                    }

                    return $inputTableName;
                }
                );
            $tableName = $questionHelper->ask($input, $output, $tableQuestion);
        }
        $output->writeln("You selected table <info>{$tableName}</info>");
        $input->setOption('table-name', $tableName);

        $entityClassName = $input->getOption('entity-class-name');
        if (empty($entityClassName)) {
            $entityQuestion = new Question('What is the FQDN of the entity class to create? ');
            $entityQuestion->setValidator(
                function ($response) {
                    if (empty($response)) {
                        throw new \RuntimeException('entity-class-name is a required value');
                    }

                    return $response;
                });
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
        $schemaName = $input->getOption('schema-name');
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
}
