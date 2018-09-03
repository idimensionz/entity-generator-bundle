<?php

namespace iDimensionz\EntityGeneratorBundle\Command;

use iDimensionz\EntityGeneratorBundle\Service\EntityCreatorService;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

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
