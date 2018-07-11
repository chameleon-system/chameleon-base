<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/*
 * class TFullGroupTableStyle is a subclass of TFullGroupTable and extends TGroupTableStyle.
 * It is used to style the TFullGroupTable class
*/
class TFullGroupTableStyle extends TGroupTableStyle
{
    /**
     * style class name to use for the header line (default = NULL).
     *
     * @var null|string
     */
    public $header = null;

    /**
     * style class name to use for the search line (default = NULL).
     *
     * @var null|string
     */
    public $search = null;

    /**
     * style class name to use for the navigation line (default = NULL).
     *
     * @var null|string
     */
    public $navigation = null;

    /**
     * style class name to use for the not found text line line (default = NULL).
     *
     * @var null|string
     */
    public $notFoundText = null;

    /**
     * style class name to use for the group select field (default = NULL).
     *
     * @var null|string
     */
    public $groupSelector = null;

    /**
     * style class name to use for the filter table (default = NULL).
     *
     * @var null|string
     */
    public $filterTable = null;

    /**
     * CSS style to use in searchfield TD.
     *
     * @var null|string
     */
    public $searchFieldTDstyle = null;

    /**
     * CSS style to use in search button TD.
     *
     * @var null|string
     */
    public $searchButtonTDstyle = null;

    public function TFullGroupTableStyle()
    {
        TGroupTableStyle::TGroupTableStyle();
        $this->header = null;
        $this->search = null;
        $this->navigation = null;
        $this->notFoundText = null;
        $this->groupSelector = null;
        $this->filterTable = null;
        $this->searchFieldTDstyle = null;
        $this->searchButtonTDstyle = null;
    }

    public function GetHeader()
    {
        $output = '';
        if (!is_null($this->header)) {
            $output = " class=\"{$this->header}\"";
        }

        return $output;
    }

    public function GetSearch()
    {
        $output = '';
        if (!is_null($this->search)) {
            $output = " class=\"{$this->search}\"";
        }

        return $output;
    }

    public function GetNavigation()
    {
        $output = '';
        if (!is_null($this->navigation)) {
            $output = " class=\"{$this->navigation}\"";
        }

        return $output;
    }

    public function GetNotFoundText()
    {
        $output = '';
        if (!is_null($this->notFoundText)) {
            $output = " class=\"{$this->notFoundText}\"";
        }

        return $output;
    }

    public function GetGroupSelector()
    {
        $output = '';
        if (!is_null($this->groupSelector)) {
            $output = " class=\"form-control input-sm {$this->groupSelector}\"";
        }

        return $output;
    }

    public function GetFilterTable()
    {
        $output = '';
        if (!is_null($this->filterTable)) {
            $output = " class=\"{$this->filterTable}\"";
        }

        return $output;
    }

    public function GetSearchFieldTD()
    {
        $output = '';
        if (!is_null($this->searchFieldTDstyle)) {
            $output = " class=\"{$this->searchFieldTDstyle}\"";
        }

        return $output;
    }

    public function GetSearchButtonTD()
    {
        $output = '';
        if (!is_null($this->searchButtonTDstyle)) {
            $output = " class=\"{$this->searchButtonTDstyle}\"";
        }

        return $output;
    }
}
