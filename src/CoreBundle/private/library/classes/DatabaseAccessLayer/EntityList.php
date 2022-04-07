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

use ChameleonSystem\core\DatabaseAccessLayer\LengthCalculationStrategy\EntityListLengthCalculationStrategyInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Statement;

/**
 * @template T
 * @implements EntityListInterface<T>
 */
class EntityList implements EntityListInterface
{
    /**
     * @var Connection
     */
    private $databaseConnection;

    /**
     * @var T[]
     */
    private $entityList = array();

    /**
     * @var int
     */
    private $entityIndex = 0;

    /**
     * @var string
     */
    private $query;

    /**
     * @var Statement|null
     */
    private $databaseEntityListStatement = null;

    /**
     * @var int
     */
    private $entityCount = null;
    /**
     * @var int
     */
    private $entityCountEstimate = null;
    /**
     * @var int
     */
    private $currentPage = 0;
    /**
     * @var EntityListPager
     */
    private $pager;

    /**
     * @var int|null
     */
    private $maxNumberOfResults = null;

    /**
     * @var array
     */
    private $queryParameters = null;
    /**
     * @var array
     */
    private $queryParameterTypes = null;

    /**
     * @param string     $query
     * @param array      $queryParameters     - same as the parameters parameter of the Connection
     * @param array      $queryParameterTypes - same as the parameters types of the Connection
     * @param Connection $databaseConnection
     */
    public function __construct(Connection $databaseConnection, $query, array $queryParameters = null, array $queryParameterTypes = null)
    {
        $this->query = $query;
        $this->databaseConnection = $databaseConnection;
        $this->queryParameters = $queryParameters;
        $this->queryParameterTypes = $queryParameterTypes;
    }

    /**
     * @return Statement
     */
    protected function getDatabaseEntityListStatement()
    {
        if (null === $this->databaseEntityListStatement) {
            $this->databaseEntityListStatement = $this->getDatabaseConnection()->executeQuery(
                $this->getExecutableQuery($this->query),
                $this->getQueryParameters(),
                $this->getQueryParametersTypes()
            );
        }

        return $this->databaseEntityListStatement;
    }

    /**
     * @return Connection
     */
    protected function getDatabaseConnection()
    {
        return $this->databaseConnection;
    }

    /**
     * @return T|false
     */
    public function current()
    {
        if ($this->entityIndex < 0) {
            return false;
        }
        if (isset($this->entityList[$this->entityIndex])) {
            return $this->entityList[$this->entityIndex];
        }

        $this->entityList[$this->entityIndex] = $this->getDatabaseEntityListStatement()->fetch(\PDO::FETCH_ASSOC);

        return $this->entityList[$this->entityIndex];
    }

    /**
     * Move forward to next element.
     * {@inheritDoc}
     */
    public function next()
    {
        if (isset($this->entityList[$this->entityIndex]) && false === $this->entityList[$this->entityIndex]) {
            return;
        }
        ++$this->entityIndex;
        $this->current(); // make sure the item is fetched and stored
    }

    /**
     * {@inheritDoc}
     */
    public function previous()
    {
        if ($this->entityIndex < 0) {
            return;
        }
        --$this->entityIndex;
    }

    /**
     * {@inheritDoc}
     */
    public function key()
    {
        return $this->entityIndex;
    }

    /**
     * {@inheritDoc}
     */
    public function valid()
    {
        $item = $this->current();

        return false !== $item;
    }

    /**
     * {@inheritDoc}
     */
    public function rewind()
    {
        $this->entityIndex = 0;
    }

    /**
     * returns an exact count of the number of records matching the query.
     * {@inheritDoc}
     */
    public function count()
    {
        if (null !== $this->entityCount) {
            return $this->correctCountUsingMaxNumberOfResultsAllowed($this->entityCount);
        }

        $normalizedQuery = $this->getNormalizeQuery($this->query);
        $normalizedQuery = $this->removeOrderByFromQuery($normalizedQuery);
        $strategyList = array(
            'ChameleonSystem\core\DatabaseAccessLayer\LengthCalculationStrategy\SqlCountStrategy',
            'ChameleonSystem\core\DatabaseAccessLayer\LengthCalculationStrategy\SqlCountWithSubqueryStrategy',
        );
        $queryWithoutOrderBy = $this->removeOrderByFromQuery($this->query);
        foreach ($strategyList as $strategyName) {
            /** @var $strategy EntityListLengthCalculationStrategyInterface */
            $strategy = new $strategyName($this->getDatabaseConnection());
            if (true === $strategy->isValidStrategyFor($normalizedQuery)) {
                $this->entityCount = $strategy->calculateLength($queryWithoutOrderBy, $this->getQueryParameters(), $this->getQueryParametersTypes());
                break;
            }
        }

        if (null === $this->entityCount) {
            $this->entityCount = $this->getDatabaseEntityListStatement()->rowCount();
        }

        return $this->correctCountUsingMaxNumberOfResultsAllowed($this->entityCount);
    }

    /**
     * @param int $count
     * @return int
     */
    private function correctCountUsingMaxNumberOfResultsAllowed($count)
    {
        if (null === $this->maxNumberOfResults) {
            return $count;
        }

        return min($count, $this->maxNumberOfResults);
    }

    /**
     * estimates the number of records found. If a count is already known, it will return that instead.
     * {@inheritDoc}
     */
    public function estimateCount()
    {
        if (null !== $this->entityCount) {
            return $this->correctCountUsingMaxNumberOfResultsAllowed($this->entityCount);
        }

        if (null !== $this->entityCountEstimate) {
            return $this->correctCountUsingMaxNumberOfResultsAllowed($this->entityCountEstimate);
        }

        $query = 'EXPLAIN '.$this->query;
        $estimateRow = $this->getDatabaseConnection()->fetchAssoc($query);
        $this->entityCountEstimate = (int) $estimateRow['rows'];

        return $this->correctCountUsingMaxNumberOfResultsAllowed($this->entityCountEstimate);
    }

    /**
     * @param string $query
     * @return string
     */
    private function getNormalizeQuery($query)
    {
        return str_replace(array("\n", "\n\r", "\t"), ' ', mb_strtoupper($query));
    }

    /**
     * @param string $query
     * @return string
     */
    private function removeOrderByFromQuery($query)
    {
        $queryModifier = $this->getQueryModifierOrderByService();

        return $queryModifier->getQueryWithoutOrderBy($query);
    }

    /**
     * @return QueryModifierOrderByInterface
     */
    protected function getQueryModifierOrderByService()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.query_modifier.order_by');
    }

    /**
     * {@inheritDoc}
     */
    public function setPageSize($pageSize)
    {
        $this->pager = $this->getEntityListPager($pageSize);
        $this->resetList();

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setCurrentPage($currentPage)
    {
        $this->currentPage = $currentPage;
        $this->resetList();

        return $this;
    }

    /**
     * @return void
     */
    private function resetList()
    {
        $this->databaseEntityListStatement = null;
        $this->entityList = array();
        $this->entityIndex = 0;
    }

    /**
     * @return void
     */
    private function resetListCounts()
    {
        $this->entityCount = null;
        $this->entityCountEstimate = null;
    }

    /**
     * @param string $query
     * @return string
     */
    private function getExecutableQuery($query)
    {
        if (null !== $this->pager) {
            $query = $this->pager->getQueryForPage($query, $this->currentPage);
        }

        if (null !== $this->maxNumberOfResults) {
            $query = $this->addMaxNumberOfResultsRestrictionToQuery($query);
        }

        return $query;
    }

    /**
     * @param int $position
     * @return void
     */
    public function seek($position)
    {
        // backwards seek
        if (isset($this->entityList[$position])) {
            $this->entityIndex = $position;

            return;
        }

        // forward seek
        while ($this->valid() && $this->entityIndex <= $position) {
            $this->next();
        }
    }

    /**
     * @return int
     */
    public function getCurrentPosition()
    {
        return $this->entityIndex;
    }

    /**
     * {@inheritDoc}
     */
    public function end()
    {
        if ($this->entityIndex < 0) {
            $this->entityIndex = 0;
        }
        while ($this->valid()) {
            $this->next();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function setQuery($query)
    {
        $this->query = $query;
        $this->resetList();
        $this->resetListCounts();
    }

    /**
     * limit results to - pass null to remove the restriction.
     *
     * @param int $maxNumberOfResults
     * @return void
     */
    public function setMaxAllowedResults($maxNumberOfResults)
    {
        $this->maxNumberOfResults = $maxNumberOfResults;
        $this->resetList();
        $this->resetListCounts();
    }

    /**
     * @param string $query
     * @return string
     */
    private function addMaxNumberOfResultsRestrictionToQuery($query)
    {
        $queryModifier = new QueryModifierRestrictNumberOfResults($query);

        return $queryModifier->restrictToMaxNumberOfResults($this->maxNumberOfResults);
    }

    /**
     * @return EntityListPagerInterface
     */
    protected function getEntityListPager($pageSize)
    {
        return new EntityListPager($pageSize);
    }

    /**
     * @return string
     */
    protected function getQuery()
    {
        return $this->query;
    }

    /**
     * @return array
     */
    protected function getQueryParameters()
    {
        return (null === $this->queryParameters) ? array() : $this->queryParameters;
    }

    /**
     * @return array
     */
    protected function getQueryParametersTypes()
    {
        return (null === $this->queryParameterTypes) ? array() : $this->queryParameterTypes;
    }

    /**
     * @return int
     */
    public function getNumberOfResultsOnPage()
    {
        return $this->getDatabaseEntityListStatement()->rowCount();
    }
}
