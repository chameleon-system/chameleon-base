<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\core\DatabaseAccessLayer\LengthCalculationStrategy;

use Doctrine\DBAL\Connection;

class SqlCountWithSubqueryStrategy implements EntityListLengthCalculationStrategyInterface
{
    /**
     * @var Connection
     */
    private $databaseConnection;

    public function __construct(Connection $databaseConnection)
    {
        $this->databaseConnection = $databaseConnection;
    }

    public function isValidStrategyFor($normalizedQuery)
    {
        return true; // always valid
    }

    public function calculateLength($query, array $queryParameters = [], array $queryParameterTypes = [])
    {
        $sCountQuery = "SELECT COUNT(*) AS matches FROM ({$query}) AS _A_";
        $matchRow = $this->databaseConnection->fetchNumeric($sCountQuery, $queryParameters, $queryParameterTypes);

        return (int) $matchRow[0];
    }
}
