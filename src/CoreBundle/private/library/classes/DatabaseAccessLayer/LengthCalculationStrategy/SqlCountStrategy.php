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

class SqlCountStrategy implements EntityListLengthCalculationStrategyInterface
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
        return
            false === $this->queryContainsGroupBy($normalizedQuery)
            && false === $this->queryHasLimit($normalizedQuery)
            && false === $this->queryUsesUnion($normalizedQuery)
            && false === $this->queryUsesDistinct($normalizedQuery)
        ;
    }

    public function calculateLength($query, array $queryParameters = [], array $queryParameterTypes = [])
    {
        $queryWithoutFields = substr($query, $this->getFromPosition($query));
        $sCountQuery = "SELECT COUNT(*) AS matches {$queryWithoutFields}";
        $matchRow = $this->databaseConnection->fetchNumeric($sCountQuery, $queryParameters, $queryParameterTypes);

        return (int) $matchRow[0];
    }

    private function queryContainsGroupBy($query)
    {
        return false !== stripos($query, ' GROUP BY ');
    }

    private function queryHasLimit($query)
    {
        return false !== stripos($query, ' LIMIT ');
    }

    private function queryUsesUnion($query)
    {
        return false !== stripos($query, ' UNION ');
    }

    private function queryUsesDistinct($normalizedQuery)
    {
        return false !== stripos($normalizedQuery, ' DISTINCT ');
    }

    private function getFromPosition($query)
    {
        $normalizedQuery = str_replace(["\n", "\n\r", "\t"], ' ', $query);

        return stripos($normalizedQuery, ' FROM ');
    }
}
