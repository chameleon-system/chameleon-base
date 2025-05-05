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
use Doctrine\DBAL\ForwardCompatibility\DriverResultStatement;
use Doctrine\DBAL\ForwardCompatibility\DriverStatement;

/**
 * @template T
 *
 * @implements EntityListInterface<T>
 */
class EntityList implements EntityListInterface
{
    private Connection $databaseConnection;

    private array $entityList = [];

    private int $entityIndex = 0;
    private string $query;

    /**
     * @var Statement|null
     */
    private $databaseEntityListStatement;

    private ?int $entityCount = null;
    private ?int $entityCountEstimate = null;
    private int $currentPage = 0;
    private ?EntityListPagerInterface $pager = null;

    private ?int $maxNumberOfResults = null;

    private array $queryParameters;
    private array $queryParameterTypes = [];

    /**
     * @param array $queryParameters - same as the parameters parameter of the Connection
     * @param array $queryParameterTypes - same as the parameters types of the Connection
     */
    public function __construct(Connection $databaseConnection, string $query, array $queryParameters = [], array $queryParameterTypes = [])
    {
        $this->query = $query;
        $this->databaseConnection = $databaseConnection;
        $this->queryParameters = $queryParameters;
        $this->queryParameterTypes = $queryParameterTypes;
    }

    /**
     * @return DriverStatement|DriverResultStatement
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

    protected function getDatabaseConnection(): Connection
    {
        return $this->databaseConnection;
    }

    /**
     * @return T|false
     */
    public function current(): mixed
    {
        if ($this->entityIndex < 0) {
            return false;
        }
        if (isset($this->entityList[$this->entityIndex])) {
            return $this->entityList[$this->entityIndex];
        }

        $this->entityList[$this->entityIndex] = $this->getDatabaseEntityListStatement()->fetchAssociative();

        return $this->entityList[$this->entityIndex];
    }

    /**
     * Move forward to next element.
     */
    public function next(): void
    {
        if (isset($this->entityList[$this->entityIndex]) && false === $this->entityList[$this->entityIndex]) {
            return;
        }
        ++$this->entityIndex;
        $this->current(); // make sure the item is fetched and stored
    }

    public function previous(): void
    {
        if ($this->entityIndex < 0) {
            return;
        }
        --$this->entityIndex;
    }

    public function key(): int
    {
        return $this->entityIndex;
    }

    public function valid(): bool
    {
        $item = $this->current();

        return false !== $item;
    }

    public function rewind(): void
    {
        $this->entityIndex = 0;
    }

    /**
     * returns an exact count of the number of records matching the query.
     */
    public function count(): int
    {
        if (null !== $this->entityCount) {
            return $this->correctCountUsingMaxNumberOfResultsAllowed($this->entityCount);
        }

        $normalizedQuery = $this->getNormalizeQuery($this->query);
        $normalizedQuery = $this->removeOrderByFromQuery($normalizedQuery);
        $strategyList = [
            'ChameleonSystem\core\DatabaseAccessLayer\LengthCalculationStrategy\SqlCountStrategy',
            'ChameleonSystem\core\DatabaseAccessLayer\LengthCalculationStrategy\SqlCountWithSubqueryStrategy',
        ];
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

    private function correctCountUsingMaxNumberOfResultsAllowed(int $count): int
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
    public function estimateCount(): int
    {
        if (null !== $this->entityCount) {
            return $this->correctCountUsingMaxNumberOfResultsAllowed($this->entityCount);
        }

        if (null !== $this->entityCountEstimate) {
            return $this->correctCountUsingMaxNumberOfResultsAllowed($this->entityCountEstimate);
        }

        $query = 'EXPLAIN '.$this->query;
        $estimateRow = $this->getDatabaseConnection()->fetchAssociative($query);
        $this->entityCountEstimate = (int) $estimateRow['rows'];

        return $this->correctCountUsingMaxNumberOfResultsAllowed($this->entityCountEstimate);
    }

    private function getNormalizeQuery(string $query): string
    {
        return str_replace(["\n", "\n\r", "\t"], ' ', mb_strtoupper($query));
    }

    private function removeOrderByFromQuery(string $query): string
    {
        $queryModifier = $this->getQueryModifierOrderByService();

        return $queryModifier->getQueryWithoutOrderBy($query);
    }

    protected function getQueryModifierOrderByService(): QueryModifierOrderByInterface
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.query_modifier.order_by');
    }

    /**
     * {@inheritDoc}
     */
    public function setPageSize(int $pageSize): self
    {
        $this->pager = $this->getEntityListPager($pageSize);
        $this->resetList();

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setCurrentPage(int $currentPage): self
    {
        $this->currentPage = $currentPage;
        $this->resetList();

        return $this;
    }

    private function resetList(): void
    {
        $this->databaseEntityListStatement = null;
        $this->entityList = [];
        $this->entityIndex = 0;
    }

    private function resetListCounts(): void
    {
        $this->entityCount = null;
        $this->entityCountEstimate = null;
    }

    private function getExecutableQuery(string $query): string
    {
        if (null !== $this->pager) {
            $query = $this->pager->getQueryForPage($query, $this->currentPage);
        }

        if (null !== $this->maxNumberOfResults) {
            $query = $this->addMaxNumberOfResultsRestrictionToQuery($query);
        }

        return $query;
    }

    public function seek(int $position): void
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

    public function getCurrentPosition(): int
    {
        return $this->entityIndex;
    }

    /**
     * {@inheritDoc}
     */
    public function end(): void
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
    public function setQuery(string $query): void
    {
        $this->query = $query;
        $this->resetList();
        $this->resetListCounts();
    }

    /**
     * limit results to - pass null to remove the restriction.
     */
    public function setMaxAllowedResults(?int $maxNumberOfResults): void
    {
        $this->maxNumberOfResults = $maxNumberOfResults;
        $this->resetList();
        $this->resetListCounts();
    }

    private function addMaxNumberOfResultsRestrictionToQuery(string $query): string
    {
        $queryModifier = new QueryModifierRestrictNumberOfResults($query);

        return $queryModifier->restrictToMaxNumberOfResults($this->maxNumberOfResults);
    }

    protected function getEntityListPager(int $pageSize): EntityListPagerInterface
    {
        return new EntityListPager($pageSize);
    }

    protected function getQuery(): string
    {
        return $this->query;
    }

    protected function getQueryParameters(): array
    {
        return $this->queryParameters;
    }

    protected function getQueryParametersTypes(): array
    {
        return $this->queryParameterTypes;
    }

    public function getNumberOfResultsOnPage(): int
    {
        $result = $this->getDatabaseEntityListStatement();
        if (null === $result) {
            return 0;
        }

        return $result->rowCount();
    }
}
