<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\DatabaseMigrationBundle\Bridge\Chameleon\Query;

use ChameleonSystem\CoreBundle\Service\DeletedFieldsServiceInterface;
use ChameleonSystem\DatabaseMigration\Constant\QueryConstants;
use ChameleonSystem\DatabaseMigration\Query\MigrationQueryData;
use ChameleonSystem\DatabaseMigration\Query\QueryInterface;
use ChameleonSystem\DatabaseMigrationBundle\Bridge\Chameleon\DataAccess\AbstractQueryDataAccessInterface;
use Doctrine\Common\Collections\Expr\Comparison;
use Doctrine\Common\Collections\Expr\CompositeExpression;
use Doctrine\DBAL\Connection;

/**
 * AbstractQuery provides a generic implementation that generates and executes SQL queries from MigrationQueryData objects.
 * It needs to be subclassed for every SQL query type.
 */
abstract class AbstractQuery implements QueryInterface
{
    /**
     * @var Connection
     */
    private $databaseConnection;
    /**
     * @var AbstractQueryDataAccessInterface
     */
    private $dataAccess;

    public function __construct(
        Connection $databaseConnection,
        AbstractQueryDataAccessInterface $dataAccess,
        protected readonly DeletedFieldsServiceInterface $deletedFieldsService,
    ) {
        $this->databaseConnection = $databaseConnection;
        $this->dataAccess = $dataAccess;
    }

    /**
     * {@inheritdoc}
     */
    final public function execute(MigrationQueryData $migrationQueryData)
    {
        $this->assertPrerequisites($migrationQueryData);
        list($query, $queryParams) = $this->getQuery($migrationQueryData);
        $statement = $this->databaseConnection->prepare($query);
        $statement->executeQuery($queryParams);

        return [$query, $queryParams];
    }

    /**
     * Assures that prerequisites for the given $migrationQueryData objects are met. Subclasses may extend this method
     * to enforce further restrictions.
     *
     * @return void
     *
     * @throws \InvalidArgumentException if prerequisites aren't met
     */
    protected function assertPrerequisites(MigrationQueryData $migrationQueryData)
    {
        if (('' === trim($migrationQueryData->getTableName())) || '' === trim($migrationQueryData->getLanguage())) {
            throw new \InvalidArgumentException('Table name and language need to be set.');
        }
    }

    /**
     * {@inheritdoc}
     */
    final public function getQuery(MigrationQueryData $migrationQueryData)
    {
        $this->filterDeletedFields($migrationQueryData);
        $query = $this->getBaseQuery($this->databaseConnection->quoteIdentifier($migrationQueryData->getTableName()));
        $queryParams = [];

        $baseLanguage = $this->dataAccess->getBaseLanguageIso();
        $translatedFields = $this->getTranslatedFields($migrationQueryData->getTableName(), $migrationQueryData->getLanguage(), $baseLanguage);

        list($subQuery, $subQueryParams) = $this->getSetQueryPart($migrationQueryData, $translatedFields);
        $query .= $subQuery;
        $queryParams = array_merge($queryParams, $subQueryParams);

        list($subQuery, $subQueryParams) = $this->getWhereQueryPart($migrationQueryData, $translatedFields);
        $query .= $subQuery;
        $queryParams = array_merge($queryParams, $subQueryParams);

        return [$query, $queryParams];
    }

    protected function filterDeletedFields(MigrationQueryData $migrationQueryData): void
    {
        $deletedFields = $this->deletedFieldsService->getTableDeletedFields($migrationQueryData->getTableName());
        $filteredFields = array_diff_key($migrationQueryData->getFields(), array_fill_keys($deletedFields, true));
        $migrationQueryData->setFields($filteredFields);
    }

    /**
     * Returns the first part of the query, defining its purpose (e.g. "INSERT INTO $quotedTableName").
     *
     * @param string $quotedTableName
     *
     * @return string
     */
    abstract protected function getBaseQuery($quotedTableName);

    /**
     * @param string $tableName
     * @param string $targetLanguage
     * @param string $baseLanguage
     *
     * @return array
     */
    private function getTranslatedFields($tableName, $targetLanguage, $baseLanguage)
    {
        if ($targetLanguage === $baseLanguage) {
            return [];
        } else {
            return $this->dataAccess->getTranslatedFieldsForTable($tableName);
        }
    }

    /**
     * Returns the SET query part of a query. This method may be overwritten in a subclass, e.g. for disabling adding set fields.
     *
     * @param array $translatedFields A simple list of alle fields that are translated in the current table
     *
     * @return array An array consisting of the query part (string) and and parameters (array)
     */
    protected function getSetQueryPart(MigrationQueryData $migrationQueryData, array $translatedFields)
    {
        $query = '';
        $queryParams = [];

        if (count($migrationQueryData->getFields()) > 0) {
            $query .= ' SET ';
            $isFirst = true;

            foreach ($migrationQueryData->getFields() as $fieldName => $value) {
                if (false === $isFirst) {
                    $query .= ', ';
                }
                $finalFieldName = $this->getFinalFieldName($fieldName, $migrationQueryData->getLanguage(), $translatedFields);
                $valueFieldTypes = $migrationQueryData->getFieldTypes();
                $fieldType = isset($valueFieldTypes[$fieldName]) ? $valueFieldTypes[$fieldName] : null;
                list($subQuery, $subQueryParams) = $this->getSingleSetQueryPart($fieldName, Comparison::EQ, $value, $finalFieldName, $fieldType);
                $query .= $subQuery;
                $queryParams = array_merge($queryParams, $subQueryParams);

                $isFirst = false;
            }
        }

        return [$query, $queryParams];
    }

    /**
     * Returns the field name in the database - currently the field with appended suffix if it is translatable.
     *
     * @param string $fieldName
     * @param string $language
     *
     * @return string
     */
    private function getFinalFieldName($fieldName, $language, array $translatedFields)
    {
        if (in_array($fieldName, $translatedFields)) {
            return $fieldName.'__'.$language;
        } else {
            return $fieldName;
        }
    }

    /**
     * Returns a single query part of the form "$fieldName $operator $value". This method can be used both for SET and
     * for WHERE clauses. Depending on the type of the given field, the value may be a placeholder ("?"), in which case
     * the real parameter is added to the returned query parameter array.
     *
     * @param string $fieldName
     * @param string $operator
     * @param string|string[] $value
     * @param string $finalFieldName
     * @param string $fieldType
     *
     * @return array An array consisting of the query part (string) and and parameters (array)
     */
    protected function getSingleSetQueryPart($fieldName, $operator, $value, $finalFieldName, $fieldType)
    {
        $query = '';
        $queryParams = [];
        if (null !== $fieldType) {
            switch ($fieldType) {
                case QueryConstants::FIELDTYPE_ARRAY:
                    $value = $this->getQuotedArrayValue($value);
                    break;
                case QueryConstants::FIELDTYPE_LITERAL:
                    break;
                case QueryConstants::FIELDTYPE_COLUMN:
                    $value = $this->databaseConnection->quoteIdentifier($value);
                    break;
                default:
                    throw new \InvalidArgumentException("Invalid field type for field '$fieldName': ".$fieldType);
            }
        } elseif (is_array($value)) {
            $value = $this->getQuotedArrayValue($value);
        } else {
            $queryParams[] = $value;
            $value = '?';
        }
        $query .= $this->getKeyValueString($this->databaseConnection->quoteIdentifier($finalFieldName), $operator, $value);

        return [$query, $queryParams];
    }

    /**
     * @param string[] $value
     *
     * @return string
     */
    private function getQuotedArrayValue(array $value)
    {
        return sprintf('(%s)', implode(', ', array_map([$this->databaseConnection, 'quote'], $value)));
    }

    /**
     * @param string $fieldName
     * @param string $operator
     * @param string $value
     *
     * @return string
     */
    private function getKeyValueString($fieldName, $operator, $value)
    {
        return $fieldName.' '.$operator.' '.$value;
    }

    /**
     * Returns the WHERE query part. This method may be overwritten in a subclass, e.g. for disabling adding where conditions.
     *
     * @return array An array consisting of the query part (string) and and parameters (array)
     */
    protected function getWhereQueryPart(MigrationQueryData $migrationQueryData, array $translatedFields)
    {
        $whereExpressions = array_merge($this->getEqualsConditionsFromArray($migrationQueryData->getWhereEquals()), $migrationQueryData->getWhereExpressions());
        if (0 === count($whereExpressions)) {
            return ['', []];
        }
        list($conditionString, $queryParams) = $this->doGetConditions($migrationQueryData->getLanguage(), $translatedFields, $whereExpressions, $migrationQueryData->getWhereExpressionsFieldTypes());

        return [' WHERE '.$conditionString, $queryParams];
    }

    /**
     * Returns an array of Comparison objects with the Comparison::EQ operator for the passed associative array.
     *
     * @return Comparison[]
     */
    protected function getEqualsConditionsFromArray(array $array)
    {
        $conditionList = [];
        foreach ($array as $field => $value) {
            $conditionList[] = new Comparison($field, Comparison::EQ, $value);
        }

        return $conditionList;
    }

    /**
     * @param string $targetLanguage
     * @param string $type
     *
     * @return array An array consisting of the query part (string) and and parameters (array)
     */
    private function doGetConditions($targetLanguage, array $translatedFields, array $conditions, array $conditionFieldTypes, $type = CompositeExpression::TYPE_AND)
    {
        $conditionString = '';
        $queryParams = [];
        foreach ($conditions as $condition) {
            if (!empty($conditionString)) {
                $conditionString .= ' '.$type.' ';
            }
            if ($condition instanceof CompositeExpression) {
                list($subConditionString, $subQueryParams) = $this->doGetConditions($targetLanguage, $translatedFields, $condition->getExpressionList(), $conditionFieldTypes, $condition->getType());
                $conditionString .= '('.$subConditionString;
                $queryParams = array_merge($queryParams, $subQueryParams);
                $conditionString .= ')';
            } elseif ($condition instanceof Comparison) {
                $field = $condition->getField();
                $finalFieldName = $this->getFinalFieldName($field, $targetLanguage, $translatedFields);

                $value = $condition->getValue()->getValue();
                $fieldType = isset($conditionFieldTypes[$field]) ? $conditionFieldTypes[$field] : null;

                list($subQuery, $subQueryParams) = $this->getSingleSetQueryPart($field, $condition->getOperator(), $value, $finalFieldName, $fieldType);
                $conditionString .= $subQuery;
                $queryParams = array_merge($queryParams, $subQueryParams);
            } else {
                throw new \InvalidArgumentException('Invalid condition class "'.get_class($condition).'". Expected one of [\Doctrine\Common\Collections\Expr\CompositeExpression, \Doctrine\Common\Collections\Expr\Comparison]');
            }
        }

        return [$conditionString, $queryParams];
    }
}
