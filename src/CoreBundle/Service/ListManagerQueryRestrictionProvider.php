<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Service;

class ListManagerQueryRestrictionProvider implements ListManagerQueryRestrictionProviderInterface
{
    public function getSqlRestrictionForManagedListTable(?string $tableName): ?string
    {
        return null;
    }

    public function appendSqlRestriction(string $query, string $restriction): string
    {
        return $query;
    }
}
