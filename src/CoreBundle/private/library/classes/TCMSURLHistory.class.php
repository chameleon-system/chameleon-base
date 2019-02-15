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

    public $index = 0;

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
        } else {
            // the history has an internal item count, which needs to be decreased if we don't replace an item.
            $this->index++;
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
        if ($this->index > 0) {
            $url = $this->aHistory[($this->index - 1)]['url'];

            return $url;
        } else {
            return false;
        }
    }

    /**
     * returns and removes the url of the last history element.
     *
     * @return bool|string
     */
    public function PopURL()
    {
        if ($this->index > 0) {
            $url = $this->GetURL();
            unset($this->aHistory[($this->index - 1)]);
            --$this->index;

            return $url;
        } else {
            return false;
        }
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
                // $_item['params']['_rmhist'] = 'true';
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
     * @param $id
     */
    public function Clear($id)
    {
        $this->index = $id;
        $endpoint = count($this->aHistory)-1;
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
        $this->index = 0;
        $this->aHistory = [];
    }

    /**
     * @deprecated since 6.3.0 - use getSimilarHistoryElementIndex() instead.
     *
     * use this function to find the LAST matching history url for a set of
     * parameters.
     *
     * @param array $aParameters
     *
     * @return int|bool (array key or false, if not found)
     */
    public function FindHistoryId($aParameters)
    {
        $foundIndex = $this->getSimilarHistoryElementIndex($aParameters);

        if (null === $foundIndex) {
            return false;
        }

        return $foundIndex;
    }

    /**
     * checks if "param" parameter exists in first history element
     * (session may contain "old" history elements).
     *
     * @return bool
     */
    public function paramsParameterExists()
    {
        if (0 === count($this->aHistory)) {
            return true;
        }

        if (false === isset($this->aHistory[0])) {
            $this->aHistory = array_values($this->aHistory); // rearrange elements
        }

        return array_key_exists('params', $this->aHistory[0]);
    }
}
