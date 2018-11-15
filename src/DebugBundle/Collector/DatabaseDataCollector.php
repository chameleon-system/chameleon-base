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

    private $backtraceEnabled = false;

    private $backtraceLimit = 10;

    public function __construct(ProfilerDatabaseConnection $databaseConnection)
    {
        $this->databaseConnection = $databaseConnection;
    }

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

    public function reset()
    {
        $this->data = [];
    }

    public function getBacktraceEnabled()
    {
        return $this->data['backtraceEnabled'];
    }

    public function getBacktraceLimit()
    {
        return $this->data['backtraceLimit'];
    }

    public function getQueriesByOccurrence()
    {
        return $this->data['queriesByOccurrence'];
    }

    public function getQueryCount()
    {
        return $this->data['queryCount'];
    }

    public function getQueryTime()
    {
        return 1000 * $this->data['queryTime'];
    }

    public function getQueriedTables()
    {
        return $this->data['queriedTables'];
    }

    public function getName()
    {
        return 'chameleon.database';
    }

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

    protected function sortQueriesByNumberOfOccurrence($a, $b)
    {
        if ($a['count'] == $b['count']) {
            return 0;
        }

        return ($a['count'] < $b['count']) ? 1 : -1;
    }

    protected function getQueriesGroupedByHash($queries)
    {
        $queriesByHash = array();
        foreach ($queries as $query) {
            $cleanedQuery = preg_replace("/\s+/", ' ', $query[1]);
            $queriesByHash[md5($cleanedQuery)][] = $query;
        }

        return $queriesByHash;
    }

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
     */
    public function setBacktraceEnabled($backtraceEnabled)
    {
        $this->backtraceEnabled = $backtraceEnabled;
    }

    /**
     * @param bool $backtraceLimit
     */
    public function setBacktraceLimit($backtraceLimit)
    {
        $this->backtraceLimit = $backtraceLimit;
    }
}
