<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Used to manage the breadcrumb navigation in CMS backend.
 */
class TCMSURLHistory
{
    /**
     * the list of history objects.
     *
     * @var array
     */
    public $aHistory = array();

    public function __get($name)
    {
        if ('index' === $name) {
            @trigger_error('The property TCMSURLHistory::$index is deprecated.', E_USER_DEPRECATED);

            return $this->getHistoryCount();
        }

        $trace = debug_backtrace();
        trigger_error(sprintf('Undefined property via __get(): %s in %s on line %s',
            $name,
            $trace[0]['file'],
            $trace[0]['line']),
            E_USER_NOTICE);

        return null;
    }

    public function __set($name, $val)
    {
        if ('index' === $name) {
            @trigger_error('The property TCMSURLHistory::$index is deprecated.', E_USER_DEPRECATED);
        }

        $trace = debug_backtrace();
        trigger_error(sprintf('Undefined property via __set(): %s in %s on line %s',
            $name,
            $trace[0]['file'],
            $trace[0]['line']),
            E_USER_NOTICE);
    }

    public function __isset($name)
    {
        return 'index' === $name;
    }

    /**
     * adds item to the breadcrumb array.
     *
     * @param array  $aParameter - url parameters
     * @param string $name
     */
    public function AddItem($aParameter, $name = '')
    {
        $foundHistoryElementIndex = $this->getSimilarHistoryElementIndex($aParameter);

        if (null !== $foundHistoryElementIndex) {
            // element found, so remove it and add the new one at the end.
            $this->removeHistoryElementByIndex($foundHistoryElementIndex);
        }

        // add the new item
        $this->aHistory[] = array(
            'name' => $name,
            'url' => $this->EncodeParameters($aParameter),
            'params' => $aParameter,
        );
    }

    private function removeHistoryElementByIndex(int $index): void
    {
        unset($this->aHistory[$index]);
        // reset the index
        $this->aHistory = array_values($this->aHistory);
    }

    public function getSimilarHistoryElementIndex(array $newElementParameters): ?int
    {
        $newElementUrl = $this->EncodeParameters($newElementParameters, false);

        reset($this->aHistory);
        foreach ($this->aHistory as $key => $historyElement) {
            if ($this->EncodeParameters($historyElement['params'], false) === $newElementUrl) {
                return $key;
            }
        }
        reset($this->aHistory);

        return null;
    }

    /**
     * returns the url of the last history element.
     *
     * @return bool|string the url
     */
    public function GetURL()
    {
        $historyCount = $this->getHistoryCount();

        if ($historyCount > 0 && isset($this->aHistory[$historyCount - 1])) {
            return $this->aHistory[$historyCount - 1]['url'];
        } else {
            return false;
        }
    }

    public function getHistoryCount()
    {
        return count($this->aHistory);
    }

    /**
     * returns and removes the url of the last history element.
     *
     * @return bool|string
     */
    public function PopURL()
    {
        $url = $this->GetURL();
        array_pop($this->aHistory);

        return $url;
    }

    /**
     * returns the history as an array
     * if $withRemoveParameter is true, the returned item-urls will contain the "_histid" (for element removal) and "_rmhist = true".
     *
     * @param bool $withRemoveParameter
     *
     * @return array
     */
    public function GetBreadcrumb($withRemoveParameter = false)
    {
        $return = $this->aHistory;

        if ($withRemoveParameter) {
            $return = array();
            foreach ($this->aHistory as $key => $item) {
                $_item = array(
                    'name' => $item['name'],
                    'params' => $item['params'],
                );
                $_item['params']['_histid'] = $key;
                $_item['url'] = $this->EncodeParameters($_item['params']);
                $return[] = $_item;
            }
        }

        return $return;
    }

    /**
     * @param array $aParameters
     * @param bool  $returnWithFragment
     *
     * @return string
     */
    public function EncodeParameters($aParameters, $returnWithFragment = true)
    {
        $sUrl = PATH_CMS_CONTROLLER.'?';
        $fragment = '';

        if (is_array($aParameters)) {
            ksort($aParameters, SORT_STRING);
            if (array_key_exists('fragment', $aParameters)) {
                $fragment = '#'.$aParameters['fragment'];
                unset($aParameters['fragment']);
            }

            $sUrl .= http_build_query($aParameters);
        }

        if ($returnWithFragment) {
            $sUrl .= $fragment;
        }

        return $sUrl;
    }

    /**
     * removes all history entries with higher index than the given one.
     *
     * @param int $id
     */
    public function Clear($id)
    {
        $historyCount = $this->getHistoryCount();

        if (0 === $historyCount) {
            return;
        }

        $endpoint = $historyCount - 1;
        if ($endpoint > 0) {
            for ($i = $endpoint; $i > $id; --$i) {
                unset($this->aHistory[$i]);
            }
        }
    }

    /**
     * resets the history to zero entries.
     */
    public function reset()
    {
        $this->aHistory = [];
    }

    /**
     * checks if "param" parameter exists in first history element
     * (session may contain "old" history elements).
     *
     * @return bool
     */
    public function paramsParameterExists()
    {
        if (0 === $this->getHistoryCount()) {
            return true;
        }

        return array_key_exists('params', $this->aHistory[0]);
    }

    /**
     * Removing history entries concerning this table entry.
     *
     * @param string $tableId
     * @param string $entryId
     */
    public function removeEntries(string $tableId, string $entryId): void
    {
        if ('' === $tableId || '' === $entryId) {
            return;
        }

        $length = count($this->aHistory);
        $changed = false;
        for ($i = 0; $i < $length; ++$i) {
            $hasTableId = false !== strpos($this->aHistory[$i]['url'], 'tableid='.$tableId);
            $hasEntryId = false !== strpos($this->aHistory[$i]['url'], 'id='.$entryId);
            if (true === $hasTableId && true === $hasEntryId) {
                $changed = true;
                unset($this->aHistory[$i]);
            }
        }

        if (true === $changed) {
            // remove now "empty" indices

            $this->aHistory = array_values($this->aHistory);
        }
    }
}
