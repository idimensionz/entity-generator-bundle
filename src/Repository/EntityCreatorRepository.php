<?php

namespace iDimensionz\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Statement;

class EntityCreatorRepository
{
    /**
     * @var Connection
     */
    private $databaseConnection;
    /**
     * @var string  Name of the table to parse into an entity.
     */
    private $entityTable;

    public function __construct(Connection $databaseConnection)
    {
        $this->setDatabaseConnection($databaseConnection);
    }

    /**
     * @param string $schemaName
     * @param string $tableName
     * @return array
     */
    public function getColumnDefinitions( string $schemaName, string $tableName)
    {
        $sql = "SELECT COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE, CHARACTER_MAXIMUM_LENGTH, NUMERIC_PRECISION, NUMERIC_SCALE
            FROM INFORMATION_SCHEMA.COLUMNS
           WHERE TABLE_SCHEMA = '$schemaName'
             AND TABLE_NAME = '$tableName'";
        $databaseConnection = $this->getDatabaseConnection();
        $columnDefinitions = $databaseConnection->fetchAll($sql);

        return $columnDefinitions;
    }

    /**
     * @return Connection
     */
    public function getDatabaseConnection(): Connection
    {
        return $this->databaseConnection;
    }

    /**
     * @param Connection $databaseConnection
     */
    public function setDatabaseConnection(Connection $databaseConnection): void
    {
        $this->databaseConnection = $databaseConnection;
    }

    /**
     * @return string
     */
    protected function getEntityTable(): string
    {
        return $this->entityTable;
    }

    /**
     * @param string $entityTable
     */
    public function setEntityTable(string $entityTable): void
    {
        $this->entityTable = $entityTable;
    }
}
