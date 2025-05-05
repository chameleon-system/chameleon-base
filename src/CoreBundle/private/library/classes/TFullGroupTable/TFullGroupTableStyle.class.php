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
     * @var string|null
     */
    public $header;

    /**
     * style class name to use for the search line (default = NULL).
     *
     * @var string|null
     */
    public $search;

    /**
     * style class name to use for the navigation line (default = NULL).
     *
     * @var string|null
     */
    public $navigation;

    /**
     * style class name to use for the not found text line line (default = NULL).
     *
     * @var string|null
     */
    public $notFoundText;

    /**
     * style class name to use for the group select field (default = NULL).
     *
     * @var string|null
     */
    public $groupSelector;

    /**
     * style class name to use for the filter table (default = NULL).
     *
     * @var string|null
     */
    public $filterTable;

    /**
     * CSS style to use in searchfield TD.
     *
     * @var string|null
     */
    public $searchFieldTDstyle;

    /**
     * CSS style to use in search button TD.
     *
     * @var string|null
     */
    public $searchButtonTDstyle;

    public function __construct()
    {
        parent::__construct();
        $this->header = null;
        $this->search = null;
        $this->navigation = null;
        $this->notFoundText = null;
        $this->groupSelector = null;
        $this->filterTable = null;
        $this->searchFieldTDstyle = null;
        $this->searchButtonTDstyle = null;
    }

    /**
     * @deprecated Named constructors are deprecated and will be removed with PHP8. When calling from a parent, please use `parent::__construct` instead.
     * @see self::__construct
     */
    public function TFullGroupTableStyle()
    {
        $this->callConstructorAndLogDeprecation(func_get_args());
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
            $output = " class=\"form-control form-control-sm {$this->groupSelector}\"";
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
