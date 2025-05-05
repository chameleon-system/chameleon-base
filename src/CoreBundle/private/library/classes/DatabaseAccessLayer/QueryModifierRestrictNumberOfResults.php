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

class QueryModifierRestrictNumberOfResults implements QueryModifierRestrictNumberOfResultsInterface
{
    private $query;
    private $queryWithoutLimit;
    private $existingLimit;

    public function __construct($query)
    {
        $this->query = $query;
        $this->extractExistingLimit();
    }

    public function restrictToMaxNumberOfResults($maxNumberOfResults)
    {
        if (null === $maxNumberOfResults) {
            return $this->query;
        }

        if (false === $this->hasLimit()) {
            return $this->addLimit($this->query, 0, $maxNumberOfResults);
        }

        return $this->modifyExistingLimit($maxNumberOfResults);
    }

    private function hasLimit()
    {
        return null !== $this->existingLimit;
    }

    private function addLimit($query, $start, $maxNumberOfResults)
    {
        return $query.' LIMIT '.(int) $start.', '.(int) $maxNumberOfResults;
    }

    private function modifyExistingLimit($maxNumberOfResults)
    {
        $existingLimit = $this->existingLimit;
        if (null === $existingLimit['length']) {
            $existingLimit['length'] = $maxNumberOfResults;
        } else {
            $existingLimit['length'] = min($existingLimit['length'], $maxNumberOfResults);
        }

        return $this->addLimit($this->queryWithoutLimit, $existingLimit['start'], $existingLimit['length']);
    }

    private function extractExistingLimit()
    {
        $this->queryWithoutLimit = $this->query;
        $this->existingLimit = null;

        $limitPosition = $this->getLimitPosition();
        if (null === $limitPosition) {
            return;
        }
        $this->queryWithoutLimit = mb_substr($this->query, 0, $limitPosition);
        $existingLimit = trim(mb_substr($this->query, $limitPosition + 6));
        $existingLimit = str_replace(["\n", ' '], '', $existingLimit);
        $parts = explode(',', $existingLimit);
        $this->existingLimit = [
            'start' => (int) $parts[0],
            'length' => (2 == count($parts)) ? (int) $parts[1] : null,
        ];
    }

    private function getLimitPosition()
    {
        $normalizedQuery = str_replace(["\n", "\r", "\t"], ' ', $this->query);
        $normalizedQuery = mb_strtoupper($normalizedQuery);
        $limitPos = strrpos($normalizedQuery, ' LIMIT ');
        if (false === $limitPos) {
            return null;
        }
        $limitString = mb_substr($normalizedQuery, $limitPos + 7);

        $limitString = str_replace(' ', '', $limitString);
        // must be x or x,y
        $matcher = '/^[\\- ]*\\d+(,[\\- ]*\\d+){0,1}$/Uu';
        $matches = preg_match($matcher, $limitString);
        if (false === $matches || 0 === $matches) {
            return null;
        }

        return $limitPos;
    }
}
