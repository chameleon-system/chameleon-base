<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\DataAccess;

use ChameleonSystem\CoreBundle\Exception\DataAccessException;

interface DataAccessClassFromTableFieldProviderInterface
{
    /**
     * Fetches field configuration for a table name and a field name.
     *
     * @param string $tableName
     * @param string $fieldName
     *
     * @return string|null
     *
     * @throws DataAccessException
     */
    public function getFieldClassNameFromDictionaryValues($tableName, $fieldName);
}
