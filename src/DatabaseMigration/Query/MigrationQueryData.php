<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\DatabaseMigration\Query;

use ChameleonSystem\CoreBundle\DataModel\CommentDataModel;
use Doctrine\Common\Collections\Expr\Comparison;

/**
 * MigrationQueryData is a parameter holder class for database migration queries. It provides a fluent interface with
 * which the different parts of a query can be set.
 */
class MigrationQueryData
{
    /**
     * @var string
     */
    private $tableName;
    /**
     * @var string
     */
    private $language;
    /**
     * @var array
     */
    private $fields = [];
    /**
     * @var Comparison[]
     */
    private $whereEquals = [];
    /**
     * @var Comparison[]
     */
    private $whereExpressions = [];
    /**
     * @var array
     */
    private $fieldTypes = [];
    /**
     * @var array
     */
    private $whereExpressionsFieldTypes = [];
    /**
     * @var array<string, CommentDataModel>
     */
    private array $comments = [];

    /**
     * @param string $tableName
     * @param string $language
     */
    public function __construct($tableName, $language)
    {
        $this->tableName = $tableName;
        $this->language = $language;
    }

    /**
     * @param string $language
     *
     * @return MigrationQueryData
     */
    public function setLanguage($language)
    {
        $this->language = mb_strtolower($language);

        return $this;
    }

    /**
     * @return MigrationQueryData
     */
    public function setFields(array $fields)
    {
        $this->fields = $fields;

        return $this;
    }

    /**
     * @param Comparison[] $whereExpressions
     *
     * @return MigrationQueryData
     */
    public function setWhereExpressions(array $whereExpressions)
    {
        $this->whereExpressions = $whereExpressions;

        return $this;
    }

    /**
     * @return MigrationQueryData
     */
    public function setFieldTypes(array $fieldTypes)
    {
        $this->fieldTypes = $fieldTypes;

        return $this;
    }

    /**
     * @return MigrationQueryData
     */
    public function setWhereEquals(array $whereEquals)
    {
        $this->whereEquals = $whereEquals;

        return $this;
    }

    /**
     * @param array<string, CommentDataModel> $comments
     */
    public function setComments(array $comments): self
    {
        $this->comments = $comments;

        return $this;
    }

    /**
     * @return MigrationQueryData
     */
    public function setWhereExpressionsFieldTypes(array $whereExpressionsFieldTypes)
    {
        $this->whereExpressionsFieldTypes = $whereExpressionsFieldTypes;

        return $this;
    }

    /**
     * @return string
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @return Comparison[]
     */
    public function getWhereExpressions()
    {
        return $this->whereExpressions;
    }

    /**
     * @return array
     */
    public function getFieldTypes()
    {
        return $this->fieldTypes;
    }

    /**
     * @return array
     */
    public function getWhereEquals()
    {
        return $this->whereEquals;
    }

    /**
     * @return array
     */
    public function getWhereExpressionsFieldTypes()
    {
        return $this->whereExpressionsFieldTypes;
    }

    public function getComments(): array
    {
        return $this->comments;
    }
}
