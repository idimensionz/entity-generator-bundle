<?php
/*
 * iDimensionz/doctrine-entity-generator
 * MysqlColumnDefinitionProvider.php
 *  
 * The MIT License (MIT)
 * 
 * Copyright (c) 2018 iDimensionz
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

namespace iDimensionz\EntityGeneratorBundle\Provider;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Connection;
use iDimensionz\EntityGeneratorBundle\Model\ColumnDefinitionModel;

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
        // @todo Change this to use schemaManager->listTableColumns()
//        $this->getConnection()->getSchemaManager()->listTableColumns($tableName, $schemaName);
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
     * @throws \InvalidArgumentException
     */
    protected function mapDatabaseColumnInfoToColumnDefinition(array $item)
    {
        if (!isset($item['COLUMN_NAME']) || !isset($item['COLUMN_TYPE']) || !isset($item['IS_NULLABLE'])) {
            throw new \InvalidArgumentException(
                __METHOD__ . '/Item parameter array must COLUMN_NAME, COLUMN_TYPE and IS_NULLABLE indexes.'
            );
        }

        $length = isset($item['CHARACTER_MAXIMUM_LENGTH']) ? $item['CHARACTER_MAXIMUM_LENGTH'] : null;
        $precision = isset($item['NUMERIC_PRECISION']) ? $item['NUMERIC_PRECISION'] : null;
        $scale = isset($item['NUMERIC_SCALE']) ? $item['NUMERIC_SCALE'] : null;

        $columnDefinitionModel = new ColumnDefinitionModel();
        $columnDefinitionModel->setColumnName($item['COLUMN_NAME']);
        $columnDefinitionModel->setDataType($item['COLUMN_TYPE']);
        $columnDefinitionModel->setIsNullable('YES' === $item['IS_NULLABLE']);
        $columnDefinitionModel->setCharacterMaximumLength($length);
        $columnDefinitionModel->setNumericPrecision($precision);
        $columnDefinitionModel->setNumericScale($scale);

        return $columnDefinitionModel;
    }

    /**
     * @return Connection
     */
    protected function getConnection(): Connection
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
