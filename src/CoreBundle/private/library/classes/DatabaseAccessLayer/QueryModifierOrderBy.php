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

class QueryModifierOrderBy implements QueryModifierOrderByInterface
{
    /**
     * {@inheritdoc}
     */
    public function getQueryWithOrderBy($query, array $orderBy)
    {
        $sQuery = $this->getQueryWithoutOrderBy($query);
        if (0 === count($orderBy)) {
            return $sQuery;
        }

        $limitString = '';
        $limitPos = $this->findLimit($sQuery);
        if (false !== $limitPos) {
            $limitString = substr($sQuery, $limitPos);
            $sQuery = substr($sQuery, 0, $limitPos);
        }

        // if the query has a limit, we need to place the order by before that
        return $sQuery.' ORDER BY '.$this->getOrderByString($orderBy).$limitString;
    }

    private function findLimit($query)
    {
        $tmpQuery = strtoupper($query);
        $tmpQuery = str_replace("\n", ' ', $tmpQuery);
        $iLimitPos = strrpos($tmpQuery, ' LIMIT ');

        return $iLimitPos;
    }

    /**
     * {@inheritdoc}
     */
    public function getQueryWithoutOrderBy($sourceQuery)
    {
        $queryWithoutOrderBy = $sourceQuery;
        $sTmpSql = strtoupper($sourceQuery);
        // remove order by
        $iStrPos = strrpos($sTmpSql, ' ORDER BY ');
        if (false !== $iStrPos) {
            // check to make sure the query ends with order by...
            $iLength = strlen($sTmpSql);
            $iCountOpenBrackets = 0;
            for ($iPos = $iStrPos; $iPos < $iLength; ++$iPos) {
                if (')' === $sTmpSql[$iPos]) {
                    --$iCountOpenBrackets;
                } elseif ('(' === $sTmpSql[$iPos]) {
                    ++$iCountOpenBrackets;
                }
                if ($iCountOpenBrackets < 0) {
                    break;
                }
            }
            $iLimitPos = strpos($sTmpSql, ' LIMIT ', $iStrPos);
            if ($iCountOpenBrackets >= 0) {
                $queryWithoutOrderBy = substr($sourceQuery, 0, $iStrPos);
                if (false !== $iLimitPos) {
                    $queryWithoutOrderBy .= substr($sourceQuery, $iLimitPos);
                }
            } else {
                $queryWithoutOrderBy = substr($sourceQuery, 0, $iStrPos).substr($sourceQuery, $iPos, $iLength - $iPos);
            }
        }

        return $queryWithoutOrderBy;
    }

    private function getOrderByString(array $orderBy)
    {
        $orderByParts = [];
        foreach ($orderBy as $field => $direction) {
            $field = trim($field);
            $direction = \mb_strtoupper(\trim($direction));
            if ('DESC' !== $direction) {
                $direction = 'ASC';
            }
            $orderByParts[] = "{$field} {$direction}";
        }

        return implode(', ', $orderByParts);
    }
}
