<?php
/*
 * iDimensionz/{doctrine-entity-generator}
 * MysqlColumnDefinitionProvider.php
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

namespace iDimensionz\Provider;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Connection;
use iDimensionz\Model\ColumnDefinitionModel;

class MysqlColumnDefinitionProvider implements ColumnDefinitionProviderInterface
{
    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->setConnection($connection);
    }

    /**
     * @param string $schemaName
     * @param string $tableName
     * @return ArrayCollection|ColumnDefinitionModel[]
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getColumnDefinitions(string $schemaName, string $tableName): ArrayCollection
    {
        $sql = "SELECT COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE, CHARACTER_MAXIMUM_LENGTH, NUMERIC_PRECISION, NUMERIC_SCALE
            FROM INFORMATION_SCHEMA.COLUMNS
           WHERE TABLE_SCHEMA = ?
             AND TABLE_NAME = ?";
        $databaseConnection = $this->getConnection();
        $statement = $databaseConnection->executeQuery($sql, [$schemaName, $tableName]);
        $databaseColumnInfo = $statement->fetchAll();

        $columnDefinitions = new ArrayCollection();
        foreach ($databaseColumnInfo as $item) {
            $columnDefinition = $this->mapDatabaseColumnInfoToColumnDefinition($item);
            $columnDefinitions->add($columnDefinition);
        }

        return $columnDefinitions;
    }

    /**
     * @param array $item
     * @return ColumnDefinitionModel
     */
    private function mapDatabaseColumnInfoToColumnDefinition(array $item)
    {
        $columnDefinitionModel = new ColumnDefinitionModel();
        $columnDefinitionModel->setColumnName($item['COLUMN_NAME']);
        $columnDefinitionModel->setDataType($item['COLUMN_TYPE']);
        $columnDefinitionModel->setIsNullable('YES' === $item['IS_NULLABLE']);
        $columnDefinitionModel->setCharacterMaximumLength($item['CHARACTER_MAXIMUM_LENGTH']);
        $columnDefinitionModel->setNumericPrecision($item['NUMERIC_PRECISION']);
        $columnDefinitionModel->setNumericScale($item['NUMERIC_SCALE']);

        return $columnDefinitionModel;
    }
    /**
     * @return Connection
     */
    public function getConnection(): Connection
    {
        return $this->connection;
    }

    /**
     * @param Connection $connection
     */
    public function setConnection(Connection $connection): void
    {
        $this->connection = $connection;
    }
}
