<?php

namespace iDimensionz\Service;

use iDimensionz\Model\ColumnDefinitionModel;
use iDimensionz\Model\EntityPropertyModel;
use iDimensionz\Provider\ColumnDefinitionProviderInterface;

class EntityCreatorService
{
    /**
     * @var ColumnDefinitionProviderInterface
     */
    private $columnDefinitionProvider;
    /**
     * @var array
     */
    private $entityProperties;
    /**
     * @var \Twig_Environment
     */
    private $twig;
    /**
     * @var EntityPropertyModel
     */
    private $entityPropertyModel;

    public function __construct(ColumnDefinitionProviderInterface $columnDefinitionProvider, \Twig_Environment $twig)
    {
        $this->setColumnDefinitionProvider($columnDefinitionProvider);
        $this->setTwig($twig);
    }

    /**
     * @param string $schemaName
     * @param string $tableName
     * @param string $entityClassName
     * @return string   Code for the entity class.
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function convertTableToEntityClass(string $schemaName, string $tableName, string $entityClassName)
    {
        $entityProperties = $this->getEntityPropertiesFromTableColumns($schemaName, $tableName);
        $classCode = $this->getTwig()->render(
            'entityCreator.html.twig',
            [
                'tableName'         => $tableName,
                'entityClassName'   => $entityClassName,
                'entityProperties'   => $entityProperties
            ]
        );

        return $classCode;
    }

    /**
     * @param string $schemaName
     * @param string $tableName
     * @return array
     */
    public function getEntityPropertiesFromTableColumns(string $schemaName, string $tableName): array
    {
        $columnDefinitions = $this->getColumnDefinitionProvider()->getColumnDefinitions($schemaName, $tableName);
        foreach ($columnDefinitions as $columnDefinition) {
            $this->addEntityProperty($this->mapColumnDefinitionToEntityProperty($columnDefinition));
        }

        return $this->entityProperties;
    }

    /**
     * @param ColumnDefinitionModel $columnDefinition
     * @return EntityPropertyModel
     */
    public function mapColumnDefinitionToEntityProperty(ColumnDefinitionModel $columnDefinition): EntityPropertyModel
    {
        $this->entityPropertyModel = new EntityPropertyModel();
        $this->entityPropertyModel->setName(
            $this->convertColumnNameToPropertyName($columnDefinition->getColumnName())
        );
        $this->entityPropertyModel->setColumnName($columnDefinition->getColumnName());
        $this->entityPropertyModel->setIsDoctrineNullable($columnDefinition->isNullable());
        $this->convertColumnDataType(
                $columnDefinition->getDataType(),
                $columnDefinition->getCharacterMaximumLength(),
                $columnDefinition->getNumericPrecision(),
                $columnDefinition->getNumericScale()
            );

        return $this->entityPropertyModel;
    }

    /**
     * @param string $columnName
     * @return string
     */
    public function convertColumnNameToPropertyName(string $columnName)
    {
        // First, convert the entire string to lowercase.
        $columnName = strtolower($columnName);

        if (false !== strpos($columnName, '_')) {
            $columnName = $this->convertUnderscoreToCamelCase($columnName);
        }

        return $columnName;
    }

    /**
     * @param string $string
     * @return string
     */
    public function convertUnderscoreToCamelCase(string $string): string
    {
        // Convert first letter after each underscore to capital letter.
        $string = ucwords($string, '_ ');
        // Remove all underscores.
        $string = str_replace('_', '', $string);
        // Convert first letter to lowercase.
        $camelCase = lcfirst(trim($string));
        // Replace an initial space with an underscore.
        if (' ' === substr($string, 0, 1)) {
            $camelCase = '_' . $camelCase;
        }
        // Remove all other spaces.
        $camelCase = str_replace(' ', '', $camelCase);

        return $camelCase;
    }

    /**
     * @param string   $dataType
     * @param int|null $maximumLength
     * @param int|null $numericPrecision
     * @param int|null $numericScale
     */
    public function convertColumnDataType(string $dataType, ?int $maximumLength, ?int $numericPrecision, ?int $numericScale)
    {
        $doctrineLength = null;
        $doctrinePrecision = null;
        $doctrineScale = null;
        $dataType = strtolower($dataType);

        switch (true) {
            case false !== strpos($dataType, 'tinyint(1)'):
                $propertyDataType = 'bool';
                $doctrineDataType = 'boolean';
                break;
            case false !== strpos($dataType, 'varchar'):
            case false !== strpos($dataType, 'char'):
            case false != strpos($dataType, 'enum'):
                $propertyDataType = 'string';
                $doctrineDataType = 'string';
                break;
            case false !== strpos($dataType, 'text'):
                $propertyDataType = 'string';
                $doctrineDataType = 'text';
                break;
            case false !== strpos($dataType, 'tinyint'):
            case false !== strpos($dataType, 'smallint'):
                $propertyDataType = 'int';
                $doctrineDataType = 'smallint';
                break;
            case false !== strpos($dataType, 'mediumint'):
            case false !== strpos($dataType, 'bigint'):
                $propertyDataType = 'int';
                $doctrineDataType = 'bigint';
                break;
            case false !== strpos($dataType, 'int'):
                $propertyDataType = 'int';
                $doctrineDataType = 'int';
                break;
            case false !== strpos($dataType, 'float'):
            case false !== strpos($dataType, 'decimal'):
                $propertyDataType = 'float';
                $doctrineDataType = 'decimal';
                break;
            case 'datetime' === $dataType:
            case 'date' === $dataType:
                $propertyDataType = '\DateTime';
                $doctrineDataType = 'datetime';
                break;
            default:
                $propertyDataType = 'string';
                $doctrineDataType = 'string';
                break;
        }

        $this->entityPropertyModel->setPropertyDataType($propertyDataType);
        $this->entityPropertyModel->setDoctrineDataType($doctrineDataType);

        if ('string' == $doctrineDataType) {
            $this->entityPropertyModel->setDoctrineLength($maximumLength);
        }

        if ('decimal' == $doctrineDataType) {
            $this->entityPropertyModel->setDoctrinePrecision($numericPrecision);
            $this->entityPropertyModel->setDoctrineScale($numericScale);
        }
    }

    /**
     * @return ColumnDefinitionProviderInterface
     */
    protected function getColumnDefinitionProvider(): ColumnDefinitionProviderInterface
    {
        return $this->columnDefinitionProvider;
    }

    /**
     * @param ColumnDefinitionProviderInterface $columnDefinitionProvider
     */
    public function setColumnDefinitionProvider(ColumnDefinitionProviderInterface $columnDefinitionProvider): void
    {
        $this->columnDefinitionProvider = $columnDefinitionProvider;
    }

    /**
     * @return array
     */
    protected function getEntityProperties(): array
    {
        return $this->entityProperties;
    }

    /**
     * @param array $entityProperties
     */
    public function setEntityProperties(array $entityProperties): void
    {
        $this->entityProperties = $entityProperties;
    }

    /**
     * @param $entityProperty
     */
    public function addEntityProperty($entityProperty)
    {
        $this->entityProperties[] = $entityProperty;
    }

    /**
     * @return \Twig_Environment
     */
    protected function getTwig(): \Twig_Environment
    {
        return $this->twig;
    }

    /**
     * @param \Twig_Environment $twig
     */
    public function setTwig(\Twig_Environment $twig): void
    {
        $this->twig = $twig;
    }
}