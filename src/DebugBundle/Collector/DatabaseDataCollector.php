<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\DebugBundle\Collector;

use ChameleonSystem\DebugBundle\Connection\ProfilerDatabaseConnection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

class DatabaseDataCollector extends DataCollector
{
    /**
     * @var \ChameleonSystem\DebugBundle\Connection\ProfilerDatabaseConnection
     */
    private $databaseConnection;

    /**
     * @var bool
     */
    private $backtraceEnabled = false;

    /**
     * @var bool|int
     */
    private $backtraceLimit = 10;

    public function __construct(ProfilerDatabaseConnection $databaseConnection)
    {
        $this->databaseConnection = $databaseConnection;
    }

    /**
     * @return void
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $queries = $this->databaseConnection->getQueries();
        $queryTime = $this->databaseConnection->getQueryTimer();

        $this->data = array(
            'queryCount' => count($queries),
            'queryTime' => $queryTime,
            'queriesByOccurrence' => $this->getQueriesListedByNumberOfOccurrence($queries),
            'backtraceEnabled' => $this->backtraceEnabled,
            'backtraceLimit' => $this->backtraceLimit,
            'queriedTables' => $this->buildQueriedTables($queries),
        );
    }

    /**
     * @return void
     */
    public function reset()
    {
        $this->data = [];
    }

    /**
     * @return bool
     */
    public function getBacktraceEnabled()
    {
        return $this->data['backtraceEnabled'];
    }

    /**
     * @return int
     */
    public function getBacktraceLimit()
    {
        return $this->data['backtraceLimit'];
    }

    /**
     * @return mixed
     */
    public function getQueriesByOccurrence()
    {
        return $this->data['queriesByOccurrence'];
    }

    /**
     * @return int
     */
    public function getQueryCount()
    {
        return $this->data['queryCount'];
    }

    /**
     * @return numeric
     */
    public function getQueryTime()
    {
        return 1000 * $this->data['queryTime'];
    }

    /**
     * @return string
     */
    public function getQueriedTables()
    {
        return $this->data['queriedTables'];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'chameleon.database';
    }

    /**
     * @param array $queries
     *
     * @return array<string, mixed>[]
     */
    protected function getQueriesListedByNumberOfOccurrence($queries)
    {
        $queriesByOccurrence = array();
        $queriesByHash = $this->getQueriesGroupedByHash($queries);

        foreach ($queriesByHash as $hash => $queriesForHash) {
            $callers = array();
            $backtraces = array();
            $time = 0;
            foreach ($queriesForHash as $queryForHash) {
                $callers[] = $queryForHash[0];
                $backtraces[] = $queryForHash[2];
                $time += $queryForHash[3];
            }
            $queriesByOccurrence[$hash] = array(
                'count' => count($queriesForHash),
                'query' => $queriesForHash[0][1],
                'caller' => $callers,
                'backtrace' => $backtraces,
                'time' => 1000 * $time,
            );
        }

        usort($queriesByOccurrence, array(&$this, 'sortQueriesByNumberOfOccurrence'));

        return $queriesByOccurrence;
    }

    /**
     * @param array{count: int} $a
     * @param array{count: int} $b
     * @return int
     */
    protected function sortQueriesByNumberOfOccurrence($a, $b)
    {
        if ($a['count'] == $b['count']) {
            return 0;
        }

        return ($a['count'] < $b['count']) ? 1 : -1;
    }

    /**
     * @return array<string, mixed[]>
     * @param string[][] $queries
     */
    protected function getQueriesGroupedByHash($queries)
    {
        $queriesByHash = array();
        foreach ($queries as $query) {
            $cleanedQuery = preg_replace("/\s+/", ' ', $query[1]);
            $queriesByHash[md5($cleanedQuery)][] = $query;
        }

        return $queriesByHash;
    }

    /**
     * @param array $queries
     *
     * @return array<string, int>
     */
    protected function buildQueriedTables($queries)
    {
        $tables = array();
        foreach ($queries as $query) {
            $matches = array();
            if (preg_match('/(FROM|JOIN)\s+`(.+?)`/', $query[1], $matches)) {
                for ($i = 2; $i < count($matches); ++$i) {
                    $match = $matches[$i];
                    if (array_key_exists($match, $tables)) {
                        $tables[$match] = $tables[$match] + 1;
                    } else {
                        $tables[$match] = 1;
                    }
                }
            }
        }
        arsort($tables);

        return $tables;
    }

    /**
     * @param bool $backtraceEnabled
     *
     * @return void
     */
    public function setBacktraceEnabled($backtraceEnabled)
    {
        $this->backtraceEnabled = $backtraceEnabled;
    }

    /**
     * @param bool $backtraceLimit
     *
     * @return void
     */
    public function setBacktraceLimit($backtraceLimit)
    {
        $this->backtraceLimit = $backtraceLimit;
    }
}
