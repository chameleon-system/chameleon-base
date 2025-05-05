<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Field\Provider;

use ChameleonSystem\CoreBundle\DataAccess\DataAccessClassFromTableFieldProviderInterface;

class ClassFromTableFieldProvider implements ClassFromTableFieldProviderInterface
{
    /**
     * @var DataAccessClassFromTableFieldProviderInterface
     */
    private $dataAccessClassFromTableFieldProvider;

    public function __construct(DataAccessClassFromTableFieldProviderInterface $dataAccessClassFromTableFieldProvider)
    {
        $this->dataAccessClassFromTableFieldProvider = $dataAccessClassFromTableFieldProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldClassNameFromTableField($tableField)
    {
        if ('id' === $tableField) {
            return null;
        }
        $tableFieldDictionary = $this->getDictionaryFromTableField($tableField);
        $fieldName = $tableFieldDictionary['fieldName'];
        if ('id' === $fieldName || 'cmsident' === $fieldName) {
            return null;
        }

        if (null === $tableFieldDictionary) {
            throw new \InvalidArgumentException(sprintf("Expected syntactically valid table field identifier, holding escaped table and field name. Got '%s'.", $tableField));
        }

        $fieldClassName = $this->dataAccessClassFromTableFieldProvider->getFieldClassNameFromDictionaryValues($tableFieldDictionary['tableName'], $tableFieldDictionary['fieldName']);

        if (null === $fieldClassName || '' === $fieldClassName) {
            throw new \InvalidArgumentException(sprintf("Expected existing table and field configuration for supplied table '%s' and field '%s'. The table and/or field may not exist or are lacking a responsible field class.", $tableFieldDictionary['tableName'], $fieldName));
        }

        return $fieldClassName;
    }

    /**
     * {@inheritdoc}
     */
    public function getDictionaryFromTableField($fieldIdentifier)
    {
        $fieldIdentifier = str_replace('`', '', $fieldIdentifier);

        /** @var string[]|false $tableConfIdSplit */
        $tableConfIdSplit = explode('.', $fieldIdentifier);

        if (false === $tableConfIdSplit || 2 !== count($tableConfIdSplit)) {
            return null;
        }

        return [
            'tableName' => $tableConfIdSplit[0],
            'fieldName' => $tableConfIdSplit[1],
        ];
    }
}
