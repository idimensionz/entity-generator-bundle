<?php

namespace iDimensionz\Service;

use iDimensionz\Repository\EntityCreatorRepository;

class EntityCreatorService
{
    /**
     * @var EntityCreatorRepository
     */
    private $repository;
    /**
     * @var array
     */
    private $entityProperties;
    /**
     * @var \Twig_Environment
     */
    private $twig;

    public function __construct(EntityCreatorRepository $repository, \Twig_Environment $twig)
    {
        $this->setRepository($repository);
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
                'entityProperies'   => $entityProperties
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
        $columnDefinitions = $this->getRepository()->getColumnDefinitions($schemaName, $tableName);
        foreach ($columnDefinitions as $columnDefinition) {
            $this->addEntityProperty($this->mapColumnDefinitionToEntityProperty($columnDefinition));
        }

        return $this->entityProperties;
    }

    /**
     * @param array $columnDefinition
     * @return array
     */
    public function mapColumnDefinitionToEntityProperty(array $columnDefinition): array
    {
        $entityProperty = [];
        $entityProperty['name'] = $this->convertColumnNameToPropertyName($columnDefinition['COLUMN_NAME']);
        list(
            $entityProperty['phpDataType'],
            $entityProperty['doctrineType'],
            $entityProperty['doctrineLength'],
            $entityProperty['doctrinePrecision'],
            $entityProperty['doctrineScale']
        ) =
            $this->convertColumnDataType(
                $columnDefinition['COLUMN_TYPE'],
                $columnDefinition['CHARACTER_MAXIMUM_LENGTH'],
                $columnDefinition['NUMERIC_PRECISION'],
                $columnDefinition['NUMERIC_SCALE']
            );
        $entityProperty['columnName'] = $columnDefinition['COLUMN_NAME'];
        $entityProperty['doctrineNullable'] = ('YES' === $columnDefinition['IS_NULLABLE'] ? 'true' : 'false');

        return $entityProperty;
    }

    /**
     * @param string $columnName
     * @return string
     */
    public function convertColumnNameToPropertyName(string $columnName)
    {
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
        // First, convert the entire string to lowercase.
        $string = strtolower($string);
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

    public function convertColumnDataType($dataType, $maximumLength, $numericPrecision, $numericScale)
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
                $propertyDataType = 'string';
                $doctrineDataType = 'string';
                break;
            case false !== strpos($dataType, 'text'):
                $propertyDataType = 'string';
                $doctrineDataType = 'text';
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
            case 'datetime':
            case 'date':
                $propertyDataType = '\DateTime';
                $doctrineDataType = 'datetime';
                break;
            case 'float':
            case 'decimal':
                $propertyDataType = 'float';
                $doctrineDataType = 'decimal';
                break;
            default:
                $propertyDataType = 'string';
                $doctrineDataType = 'string';
                break;
        }

        if ('string' == $doctrineDataType) {
            $doctrineLength = $maximumLength;
        }

        if ('decimal' == $doctrineDataType) {
            $doctrinePrecision = $numericPrecision;
            $doctrineScale = $numericScale;
        }

        return [$propertyDataType, $doctrineDataType, $doctrineLength, $doctrinePrecision, $doctrineScale];
    }

    /**
     * @return EntityCreatorRepository
     */
    protected function getRepository(): EntityCreatorRepository
    {
        return $this->repository;
    }

    /**
     * @param EntityCreatorRepository $repository
     */
    public function setRepository(EntityCreatorRepository $repository): void
    {
        $this->repository = $repository;
    }

    /**
     * @return array
     */
    public function getEntityProperties(): array
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
    public function getTwig(): \Twig_Environment
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