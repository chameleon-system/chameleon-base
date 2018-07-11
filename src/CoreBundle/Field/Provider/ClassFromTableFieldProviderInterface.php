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

use ChameleonSystem\CoreBundle\Exception\DataAccessException;

/**
 * Retrieves class information from table field data.
 */
interface ClassFromTableFieldProviderInterface
{
    /**
     * Resolves the name of a responsible TCMSField* class from a supplied table and field name.
     *
     * @param string $tableField Field/Table identifier (in the form of "`table_name`.`field_name`").
     *
     * @return string|null Field type class name
     *
     * @throws DataAccessException
     * @throws \InvalidArgumentException
     */
    public function getFieldClassNameFromTableField($tableField);

    /**
     * Parses a database field name specification into its base table and field separately.
     * Example input data: '`shop_article`.`cmsident`' produces [tableName=>$tableName, fieldName=>fieldName].
     *
     * @param string $fieldIdentifier
     *
     * @return array|null
     */
    public function getDictionaryFromTableField($fieldIdentifier);
}
