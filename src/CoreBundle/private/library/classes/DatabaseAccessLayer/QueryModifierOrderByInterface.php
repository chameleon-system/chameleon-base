<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\core\DatabaseAccessLayer;

interface QueryModifierOrderByInterface
{
    /**
     * @param string $query
     * @param array $orderBy - must be of the form `table`.`field` => ASC/DESC or fieldalias=>ASC/DESC - fields MUST be quoted!
     *
     * @return string
     */
    public function getQueryWithOrderBy($query, array $orderBy);

    /**
     * @param string $sourceQuery
     *
     * @return string
     */
    public function getQueryWithoutOrderBy($sourceQuery);
}
