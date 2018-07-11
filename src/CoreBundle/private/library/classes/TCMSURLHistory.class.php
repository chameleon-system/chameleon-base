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
 * used to manage the navigation in CMS backend.
/**/
class TCMSURLHistory
{
    public $aHistory = array();
    public $index = 0;

    public function __construct()
    {
    }

    /**
     * adds item to the breadcrumb array.
     *
     * @param array  $aParameter - url parameters
     * @param string $name
     */
    public function AddItem($aParameter, $name = '')
    {
        $urlWithFragment = $this->EncodeParameters($aParameter);
        $urlWithoutFragment = $this->EncodeParameters($aParameter, false);

        if ($this->index > 0) {
            /*
             * check if last element has the same url -> update it
             * the check excludes the fragment (= same url; different fragment)
             */
            $lastElement = &$this->aHistory[$this->index - 1];
            $urlLastElementWithoutFragment = $this->EncodeParameters($lastElement['params'], false);

            // compare without fragment
            if ($urlLastElementWithoutFragment == $urlWithoutFragment) {
                $lastElement['name'] = TGlobal::OutHTML($name);
                $lastElement['params'] = $aParameter;
                $lastElement['url'] = $urlWithFragment; // add with fragment
            } else {
                // last element is not the same -> add new
                $this->aHistory[$this->index++] = array(
                    'name' => TGlobal::OutHTML($name),
                    'url' => $urlWithFragment,
                    'params' => $aParameter,
                );
                //$this->index++;
            }
        } else {
            // no item in history -> add it
            $this->aHistory[$this->index] = array(
                'name' => TGlobal::OutHTML($name),
                'url' => $urlWithFragment,
                'params' => $aParameter,
            );
            ++$this->index;
        }
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
                $_item['params']['_rmhist'] = 'true';
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
        $endpoint = count($this->aHistory);
        if ($endpoint > 0) {
            for ($i = ($endpoint - 1); $i >= $id; --$i) {
                unset($this->aHistory[$i]);
            }
        }
    }

    /**
     * use this function to find the LAST matching history url for a set of
     * parameters.
     *
     * @param array $aParameters
     *
     * @return string
     */
    public function FindHistoryId($aParameters)
    {
        $sUrl = $this->EncodeParameters($aParameters);
        $histid = false;
        reset($this->aHistory);
        foreach ($this->aHistory as $key => $item) {
            if ($item['url'] === $sUrl) {
                $histid = $key;
            }
        }
        reset($this->aHistory);

        return $histid;
    }

    /**
     * checks if "param" parameter exists in first history element
     * (session may contain "old" history elements).
     *
     * @return bool
     */
    public function paramsParameterExists()
    {
        if (!empty($this->aHistory)) {
            if (false === isset($this->aHistory[0])) {
                $this->aHistory = array_values($this->aHistory); // rearrange elements
            }

            return array_key_exists('params', $this->aHistory[0]);
        } else {
            return true;
        }
    }
}
