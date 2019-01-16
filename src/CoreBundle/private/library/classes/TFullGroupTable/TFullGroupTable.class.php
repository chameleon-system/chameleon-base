<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\Security\AuthenticityToken\AuthenticityTokenManagerInterface;
use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\CoreBundle\Util\InputFilterUtilInterface;

/**
 * class TFullGroupTable extends TGroupTable to add a header, sorting, and searching.
 *
/**/
class TFullGroupTable extends TGroupTable
{
    /**
     * array of TGroupTableFields
     * holds the header info
     * fields should be placed in here in the order of appearance.
     *
     * @var array
     */
    public $headerCells = array();

    /**
     * see class definition to view available styles.
     *
     * @var TFullGroupTableStyle
     */
    public $style = null;

    /**
     * string to display for showing the next page (default = 'next page').
     *
     * @var string
     */
    public $nextPageText = 'next page';

    /**
     * string to display for showing the previous page (default = 'previous page').
     *
     * @var string
     */
    public $previousPageText = 'previous page';

    /**
     * text to display the current page records, and the total hits.
     * default: "Records $startRecord$ - $endRecord$ of $totalFound$"
     * you can use the following placeholders:
     * $startRecord$, $endRecord$, $totalFound$.
     *
     * @var string
     */
    public $hitText = 'Records $startRecord$ - $endRecord$ of $totalFound$';

    /**
     * a string that may have the value 'top', 'bottom', 'none', or 'top_and_bottom' (default: 'top_and_bottom').
     *
     * @var string
     */
    public $pageingLocation = 'top_and_bottom';

    /**
     * path to the image to display for sorting in ASC order (default: NULL).
     *
     * @var mixed - string or null if no image path set
     */
    public $iconSortASC = null;

    /**
     * path to the image to display for sorting in DESC order (default: NULL).
     *
     * @var mixed - string or null if no image path set
     */
    public $iconSortDESC = null;

    /**
     * if set to true a select box will appear that allows you to reduce the list to one group (default = true);
     * set it to false if you use a external group selector, be sure $useGroupSelector is true!
     *
     * @var bool
     */
    public $showGroupSelector = true;

    /**
     * group by a given value.
     *
     * @var bool
     */
    public $useGroupSelector = true;

    /**
     * text to display for showing all groups (default: 'Show All').
     *
     * @var string
     */
    public $showAllGroupsText = 'Show All';

    /**
     * text to display for the group selector (default: 'group').
     *
     * @var string
     */
    public $showGroupSelectorText = 'group';

    /**
     * an array of the form 'db_field'=>'search_style'. search style may be: 'none',
     * 'left', 'right', 'both', 'none', or 'full'
     * (default: NULL).
     *
     * @var mixed - array or null as default
     */
    public $searchFields = null;

    /**
     * text to display for the searchfield (default = 'search').
     *
     * @var string
     */
    public $searchFieldText = 'search';

    /**
     * text to display for the search button (default = 'search').
     *
     * @var string
     */
    public $searchButtonText = 'search';

    /**
     * the name of the list.
     * you need to set this if there is more than one list on one page and for caching.
     *
     * @var string
     */
    public $listName = 'full_group_table';

    /**
     * text to display when no records are found.
     *
     * @var string
     */
    public $notFoundText = 'no records found';

    /**
     * internal post data.
     *
     * @var array
     */
    public $_postData = array();

    /**
     * indicates if a searchbox should be shown.
     *
     * @var bool
     */
    public $showSearchBox = true;

    /**
     * extra content to add into the search box row (default = NULL).
     *
     * @var mixed - string or null by default
     */
    public $searchBoxContent = null;

    /**
     * extra content to add into below the search box row (default = NULL).
     *
     * @var mixed - string or null by default
     */
    public $searchBoxRow = null;

    /**
     * has to be 'GET' or 'POST'. default is 'POST'
     * all form data (like search, change page, etc) will be submitted using this method.
     *
     * @var string
     */
    public $formActionType = 'POST';

    /**
     * array of fieldnames that will be ignored as hidden fields.
     *
     * @var array
     */
    public $aHiddenFieldIgnoreList = array();

    /**
     * array that holds data for custom searchbox parameters.
     * We need this to store the table object including this parameters in CMS cache.
     *
     * @var array
     */
    public $customSearchFieldParameter = array();

    /**
     * optional show a rows per page pulldown menu.
     *
     * @var bool
     */
    public $showRowsPerPageChooser = false;

    /**
     * indicates if the search header is filled.
     *
     * @var bool
     */
    public $somethingToShow = false;

    /**
     * the generated html header section.
     *
     * @var string
     */
    public $sHeaderSection = '';

    /**
     * the generated html content section.
     *
     * @var string
     */
    public $sContentSection = '';

    /**
     * the generated html filter section.
     *
     * @var string
     */
    public $sFilterSection = '';

    /**
     * the generated html paging section.
     *
     * @var string
     */
    public $sPagingSection = '';

    /**
     * CSS classes for the table tag.
     *
     * @var string
     */
    protected $tableCSS = 'table table-sm table-striped table-bordered table-hover TCMSListManagerFullGroupTable';

    public function TFullGroupTable($postData = array())
    {
        parent::TGroupTable();
        $this->style = new TFullGroupTableStyle();
        $this->_postData = $postData;
    }

    /**
     * initialises the class, set postdata here.
     *
     * @param array $postData
     */
    public function Init($postData = array())
    {
        $this->style = new TFullGroupTableStyle();
        $this->_postData = $postData;
    }

    /**
     * @param array|null $paramArray
     */
    public function AddCustomSearchFieldParameter($paramArray = null)
    {
        if (!is_null($paramArray) && is_array($paramArray)) {
            $this->customSearchFieldParameter = array_merge($this->customSearchFieldParameter, $paramArray);
        }
    }

    /**
     * checks internal search field parameter for existence
     * returns null if it does not exist.
     *
     * @param string|null $param
     */
    public function getCustomSearchFieldParameter($param)
    {
        $val = null;
        if (is_array($this->customSearchFieldParameter) && isset($this->customSearchFieldParameter[$param])) {
            $val = $this->customSearchFieldParameter[$param];
        }

        return $val;
    }

    /**
     * add a header cell.
     *
     * @param string   $name           - database name of the column name may be a string, or an array. if it is an array
     *                                 it should be of the form 'name'=>'full_name'
     * @param string   $align          - horizontal alignment to use in the cell
     * @param resource $format         - a callback function to use for the column (the function will get 2 parameters,
     *                                 the value, and the row. The string returned by the function will be displayed
     *                                 in the cell
     * @param int      $colSpan        - the colSpan parameter of the cell
     * @param bool     $allowSort      - allow sorting by that column
     * @param mixed    $width          - force width to X pixels
     * @param int      $columnPosition - the array key position where the header will be added (array key starts with 0)
     */
    public function AddHeaderField($name, $align = 'left', $format = null, $colSpan = 1, $allowSort = true, $width = false, $columnPosition = null)
    {
        $oTGroupTableHeaderField = new TGroupTableHeaderField($name, $align, $format, $colSpan, $allowSort, $width);
        if (!is_null($columnPosition) && count($this->headerCells) > 0) {
            $count = 0;
            $aTmpHeaderFields = array();
            reset($this->headerCells);
            foreach ($this->headerCells as $key => $oTmpGroupTable) {
                ++$count;
                if ($key == $columnPosition) {
                    $aTmpHeaderFields[$count] = $oTGroupTableHeaderField;
                    ++$count;
                }

                $aTmpHeaderFields[$count] = $oTmpGroupTable;
                ++$count;
            }
            $this->headerCells = $aTmpHeaderFields;
        } else {
            $this->headerCells[] = $oTGroupTableHeaderField;
        }
    }

    /**
     * removes a header column field by name.
     *
     * @param string $name
     */
    public function RemoveHeaderField($name)
    {
        reset($this->headerCells);
        foreach ($this->headerCells as $key => $oHeaderField) {
            /** @var $oHeaderField TGroupTableHeaderField */
            if ($oHeaderField->name == $name) {
                unset($this->headerCells[$key]);
            }
        }

        reset($this->headerCells);
        $aTmpHeader = array();
        foreach ($this->headerCells as $oHeaderField) {
            $aTmpHeader[] = $oHeaderField;
        }
        $this->headerCells = $aTmpHeader;
    }

    public function setTableCSS($classes)
    {
        $this->tableCSS = $classes;
    }

    public function getTableCSS()
    {
        return $this->tableCSS;
    }

    /**
     * Returns data attributes to be included in table element.
     * Used to inject table management instructions for client modules.
     *
     * @return array
     *
     * @throws \ChameleonSystem\CoreBundle\Security\AuthenticityToken\InvalidTokenFormatException
     */
    private function getManagedAttributes()
    {
        $inputFilterUtil = $this->getInputFilterUtil();
        $tableConfigurationId = $inputFilterUtil->getFilteredInput('id');
        $tableEditorConfId = TTools::GetCMSTableId('cms_tbl_conf');
        $authenticityTokenValue = current($this->getAuthenticityTokenManager()
            ->getTokenPlaceholderAsParameter(AuthenticityTokenManagerInterface::TOKEN_FORMAT_ARRAY));
        $attributes = array(
            'data-table-managed' => null,
            'data-authenticity-token-id' => AuthenticityTokenManagerInterface::TOKEN_ID,
            'data-authenticity-token-value' => $authenticityTokenValue,
            'data-table-controller' => PATH_CMS_CONTROLLER,
            'data-table-conf-id' => $tableConfigurationId,
            'data-table-editor-conf-id' => $tableEditorConfId,
            'data-table-name' => $this->sTableName,
        );

        return $attributes;
    }

    /**
     * Forms an inline formatted string from a dictionary of data attributes.
     *
     * @param array $attributes attributes to be formatted inline
     *
     * @return string
     */
    private function getInlineFromAttributes(array $attributes)
    {
        if (0 === count($attributes)) {
            return ' ';
        }
        $inline = '';
        foreach ($attributes as $attrKey => $attrValue) {
            $attrValue = $attributes[$attrKey];
            $inline .= TGlobal::OutHTML("$attrKey ");
            if (null !== $attrValue) {
                $inline .= '="';
                $inline .= TGlobal::OutHTML($attrValue);
                $inline .= '" ';
            }
        }

        return $inline;
    }

    /**
     * returns the table as string.
     *
     * @param bool $returnAsString a dummy parameter, it keeps up with parent function
     *
     * @return string
     */
    public function Display($returnAsString = false)
    {
        // first init the post data..
        $this->_InitPostData();
        // backup the hasCondition flag
        $hasCondition = $this->hasCondition;
        // build full query...
        $fullQuery = $this->_BuildQuery();
        // we want to preserve the original, so temp store it
        $oldQuery = $this->sql;
        $this->sql = $fullQuery;
        // echo $fullQuery;

        // build table content
        $this->sContentSection = parent::Display(true);

        // and restore the old query
        $this->sql = $oldQuery;
        // also restore the hasCondition flag
        $this->hasCondition = $hasCondition;

        // build table header cells
        $this->sHeaderSection = $this->_BuildHeaderCells();

        // build paging section
        $this->sPagingSection = $this->_BuildPagingSection();

        // build filter section
        $this->sFilterSection = $this->_BuildFilterSection();

        $sTable = '';
        $sTable .= $this->sFilterSection;
        // now put the table together and return it
        // now show data only if records have been found...
        if ($this->recordCount > 0) {
            if ('top' == $this->pageingLocation || 'top_and_bottom' == $this->pageingLocation) {
                $sTable .= $this->sPagingSection;
            }

            $sTable .= '<table '.$this->getInlineFromAttributes($this->getManagedAttributes()).'class="'.$this->getTableCSS().'">';
            $sTable .= $this->GetCellWidths();
            $sTable .= $this->sHeaderSection;
            $sTable .= $this->sContentSection;
            $sTable .= '</table>';

            if ('bottom' == $this->pageingLocation || 'top_and_bottom' == $this->pageingLocation) {
                $sTable .= $this->sPagingSection;
            }
        } else {
            $notfoundRow = '<div class="alert alert-info">
            '.$this->notFoundText.'</div>';
            $sTable .= $notfoundRow;
        }

        $sTable .= "</form>\n";

        return $sTable;
    }

    /**
     * sets default values into the post array, if the values have not been set...
     */
    protected function _InitPostData()
    {
        if (!empty($this->_postData['_limit'])) {
            $this->showRecordCount = $this->_postData['_limit'];
        }

        if (!isset($this->_postData['_sort_order']) || empty($this->_postData['_sort_order'])) {
            $this->_postData['_sort_order'] = '';
        }

        if (null !== $this->groupByCell) {
            if (!isset($this->_postData[$this->groupByCell->name]) || empty($this->_postData[$this->groupByCell->name])) {
                $this->_postData[$this->groupByCell->name] = '';
            }
        }
        if (!isset($this->_postData['_search_word']) || empty($this->_postData['_search_word'])) {
            $this->_postData['_search_word'] = '';
        }
        if (!isset($this->_postData['_startRecord']) || empty($this->_postData['_startRecord'])) {
            $this->_postData['_startRecord'] = '0';
        }
    }

    /**
     * extend the standard query to include the sort and search condition.
     *
     * @return string
     */
    protected function _BuildQuery()
    {
        $query = $this->sql; // get the initial sql statement from the parent class
        if ($this->hasCondition) {
            $connector = 'AND';
        } // inital connector
        else {
            $connector = 'WHERE';
        }
        // add search condition. first we take care of the group selector.
        if ($this->useGroupSelector && !is_null($this->groupByCell)) { // the selector is shown, and a group by cell defined
            // if a group has been selected:
            if (!empty($this->_postData[$this->groupByCell->name])) {
                // escape field if needed
                $field = TGroupTable::_EscapeField($this->groupByCell->full_db_name);
                // and add condition
                $query .= ' '.$connector.' '.$field." = '".MySqlLegacySupport::getInstance()->real_escape_string($this->_postData[$this->groupByCell->name])."'";
                if ('WHERE' == $connector) {
                    $connector = 'AND';
                }
            }
        } // end of group selector
        // now add other search terms
        $this->_postData['_search_word'] = trim($this->_postData['_search_word']);
        if (is_array($this->searchFields) && count($this->searchFields) > 0 && !empty($this->_postData['_search_word'])) {
            $searchTermQuery = '(';
            $isFirst = true;
            foreach ($this->searchFields as $field => $dir) {
                // 'left', 'right', 'both', 'none', or 'full'.
                $searchTerm = $this->_postData['_search_word'];
                if ('left' == $dir || 'both' == $dir || 'full' == $dir) {
                    $searchTerm = '%'.$searchTerm;
                }
                if ('right' == $dir || 'both' == $dir || 'full' == $dir) {
                    $searchTerm = $searchTerm.'%';
                }
                if ('full' == $dir) {
                    $searchTerm = str_replace(' ', '%', $searchTerm);
                }
                if ($isFirst) {
                    $isFirst = false;
                } else {
                    $searchTermQuery .= ' OR ';
                }
                $quotedField = $this->_EscapeField($field);
                $searchTermQuery .= $quotedField." LIKE '".MySqlLegacySupport::getInstance()->real_escape_string($searchTerm)."'";
            }
            $searchTermQuery .= ')';

            if (stristr($query, 'GROUP BY')) {
                $query = str_replace('WHERE', 'WHERE 1=1 AND '.$searchTermQuery.' AND ', $query);
            } else {
                $query .= ' '.$connector.' '.$searchTermQuery;
            }

            if ('WHERE' == $connector) {
                $connector = 'AND';
            }
        }
        if ('AND' == $connector) {
            $this->hasCondition = true;
        }

        $query = $this->getFieldTranslationUtil()->getTranslatedQuery($query);

        $this->fullQuery = $query;
        // echo $query;
        return $query;
    }

    /**
     * build the header cells.
     *
     * @return string
     */
    protected function _BuildHeaderCells()
    {
        $tableHeader = '';
        if (is_array($this->headerCells) && count($this->headerCells) > 0) {
            // we have at least one header cell..
            reset($this->headerCells);

            /** @var $cellObj TGroupTableHeaderField */
            foreach ($this->headerCells as $cellObj) {
                $row[$cellObj->name] = $cellObj->full_db_name;
                // check if we allow sorting by that column
                if ($cellObj->allowSort) {
                    // add sort image if this is the sort column
                    if (isset($this->orderList) && is_array($this->orderList) && array_key_exists($cellObj->name, $this->orderList)) {
                        // find orderCount
                        $orderCount = 1;
                        $found = false;
                        reset($this->orderList);

                        $tmpOrderList = $this->orderList;

                        // remove group field from temporary orderList to prevent wrong order counts
                        if (!is_null($this->groupByCell) && !is_null($this->groupByCell->name) && !empty($this->groupByCell->name)) {
                            unset($tmpOrderList[$this->groupByCell->name]);
                        }

                        while ((list($field, $dir) = each($tmpOrderList)) && !$found) {
                            if (0 != strcmp($cellObj->name, $field)) {
                                ++$orderCount;
                            } else {
                                $found = true;
                            }
                        }

                        if ('ASC' == $tmpOrderList[$cellObj->name]) {
                            $orderImage = '&nbsp;('.$orderCount.')&nbsp;<img src="'.$this->iconSortDESC.'" border="0" align="middle" title="'.TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.list.form_sort_order_asc')).'" />';
                        } else {
                            $orderImage = '&nbsp;('.$orderCount.')&nbsp;<img src="'.$this->iconSortASC.'" border="0" align="middle" title="'.TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.list.form_sort_order_desc')).'" />';
                        }

                        $row[$cellObj->name] = '<nobr>'.$row[$cellObj->name].$orderImage;
                    } else {
                        $row[$cellObj->name] = '<nobr>'.$row[$cellObj->name].'</nobr>';
                    }
                    $row[$cellObj->name] = "<span style='cursor:hand; cursor:pointer;' onClick=\"document.{$this->listName}._sort_order.value='{$cellObj->name}';document.{$this->listName}.submit();\">".$row[$cellObj->name].'</span>';
                }

                $tableHeader .= $cellObj->Display($row, $this->style->GetHeader(), null, true);
            }
            $tableHeader = '<thead><tr>'.$tableHeader.'</tr></thead>';
        }

        return $tableHeader;
    }

    protected function GetCellWidths()
    {
        $colDef = '';
        if (is_array($this->headerCells) && count($this->headerCells) > 0) {
            // we have at least on header cell..
            reset($this->headerCells);
            foreach ($this->headerCells as $cellObj) {
                if (false !== $cellObj->width) {
                    $colDef .= '<col width="'.TGlobal::OutHTML($cellObj->width)."\" />\n";
                } else {
                    $colDef .= "<col width=\"*\" />\n";
                }
            }
            $colDef = "<colgroup>{$colDef}</colgroup>";
        }

        return $colDef;
    }

    /**
     * build the back/next navigation string.
     *
     * @return string
     */
    protected function _BuildPagingSection()
    {
        $tableNavigation = '';
        $back_startValue = 0;
        $next_startValue = 0;
        if ($this->showRecordCount < 0) {
            // in this case we always show all records...
            $back_startValue = 0;
            $next_startValue = $this->recordCount;
            $this->startRecord = 0;
        } else {
            if ($this->startRecord <= $this->showRecordCount) {
                $back_startValue = 0;
            } else {
                $back_startValue = $this->startRecord - $this->showRecordCount;
            }
            if ($this->startRecord + $this->showRecordCount >= $this->recordCount) {
                $next_startValue = $this->recordCount;
            } else {
                $next_startValue = $this->startRecord + $this->showRecordCount;
            }
        }

        $hitText = str_replace(array('$startRecord$', '$endRecord$', '$totalFound$'), array(($this->startRecord + 1), $next_startValue, $this->recordCount), $this->hitText);
        $tableNavigation .= "<div id=\"{$this->listName}_navi\">
        <script>
        function switchPage(startRecord) {
            document.".$this->listName.'._startRecord.value = startRecord;
            document.'.$this->listName.'.submit();
        }
        </script>
        <div class="row">';
        $tableNavigation .= '<nav class="col-auto mr-auto">';
        $tableNavigation .= '<ul class="pagination pagination-md TFullGroupTablePagination">';
        $tableNavigation .= '<li class="disabled page-item"><a href="#" class="page-link"><span class="glyphicon glyphicon-list" aria-hidden="true" style="margin-right: 5px;"></span>'.$hitText.'</a></li>';

        if ($this->startRecord > 0 && -1 != $this->showRecordCount) {
            $tableNavigation .= '<li class="page-item"><a href="javascript:switchPage(\'0\');" class="page-link"><span class="glyphicon glyphicon-fast-backward" aria-hidden="true"></span></a></li>';
            $tableNavigation .= '<li class="page-item"><a href="javascript:switchPage(\''.$back_startValue.'\');" class="page-link"><span class="glyphicon glyphicon-backward" aria-hidden="true"></span></a></li>';
        } else {
            $tableNavigation .= '<li class="disabled page-item"><a href="#" class="page-link"><span class="glyphicon glyphicon-fast-backward" aria-hidden="true"></span></a></li>';
            $tableNavigation .= '<li class="disabled page-item"><a href="#" class="page-link"><span class="glyphicon glyphicon-backward" aria-hidden="true"></span></a></li>';
        }

        $recordsPerPage = $this->showRecordCount;
        if (!empty($this->_postData['_limit'])) {
            $recordsPerPage = $this->_postData['_limit'];
        }

        if ($recordsPerPage < 0) {
            $recordsPerPage = 10;
        }

        $pageCount = ceil($this->recordCount / $recordsPerPage);
        if ($this->startRecord < $recordsPerPage) {
            $currentPage = 0;
        } else {
            $currentPage = round($this->startRecord / $recordsPerPage);
        }

        $maxPagingElements = 8;

        if ($currentPage <= ($maxPagingElements / 2)) {
            $pagingStartPage = 0;
        } else {
            $pagingStartPage = $currentPage - ($maxPagingElements / 2);
        }

        for ($i = $pagingStartPage; ($i < $pageCount && $i <= ($maxPagingElements + $pagingStartPage)); ++$i) {
            $active = '';
            if ($i == $currentPage) {
                $active = 'active';
            }

            $tableNavigation .= '<li class="page-item '.$active.'"><a href="javascript:switchPage(\''.($i * $recordsPerPage).'\');" class="page-link">'.($i + 1).'</a></li>';
        }

        if (($this->startRecord + $this->showRecordCount) < $this->recordCount && -1 != $this->showRecordCount) {
            $tableNavigation .= '<li class="page-item"><a href="javascript:switchPage(\''.$next_startValue.'\');" class="page-link"><span class="glyphicon glyphicon-forward" aria-hidden="true"></span></a></li>';
            $tableNavigation .= '<li class="page-item"><a href="javascript:switchPage(\''.(($pageCount - 1) * $recordsPerPage).'\');" class="page-link"><span class="glyphicon glyphicon-fast-forward" aria-hidden="true"></span></a></li>';
        } else {
            $tableNavigation .= '<li class="page-item disabled"><a href="#" class="page-link"><span class="glyphicon glyphicon-forward" aria-hidden="true"></span></a></li>';
            $tableNavigation .= '<li class="page-item disabled"><a href="#" class="page-link"><span class="glyphicon glyphicon-fast-forward" aria-hidden="true"></span></a></li>';
        }

        $tableNavigation .= '
            </ul>';
        $tableNavigation .= '</nav>';

        if ($this->showRowsPerPageChooser) {
            $tableNavigation .= '<div class="col-auto form-group TFullGroupTablePerPageSelect">
            <div class="input-group input-group-sm">
                <div class="input-group-prepend"><span class="input-group-text">'.TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.list.form_records_per_page')).'</span></div>
                <select name="_limit" class="form-control" onChange="document.'.$this->listName.'._startRecord.value=0;document.'.$this->listName.'.submit();">
            ';

            $userCount = $this->showRecordCount;
            if (!empty($this->_postData['_limit'])) {
                $userCount = $this->_postData['_limit'];
            }

            $aPageSize = array(10, 20, 50, 100, 200, 500);
            foreach ($aPageSize as $i) {
                $selected = '';
                if ($userCount == $i) {
                    $selected = ' selected="selected"';
                }

                $tableNavigation .= "<option value=\"{$i}\"{$selected}>{$i}</option>\n";
            }

            $tableNavigation .= '</select>
            </div>
            </div>';
        }

        $tableNavigation .= "</div>
                    </div>\n";

        return $tableNavigation;
    }

    /**
     * build the filter section.
     *
     * @return string
     */
    protected function _BuildFilterSection()
    {
        $filter = "<form name=\"{$this->listName}\" id=\"{$this->listName}\" method=\"{$this->formActionType}\" action=\"\" accept-charset=\"UTF-8\">
      <input type=\"hidden\" name=\"_user_data\" value=\"\">
      <input type=\"hidden\" name=\"_sort_order\" value=\"\">
      <input type=\"hidden\" name=\"_listName\" value=\"{$this->listName}\">\n";
        reset($this->_postData);
        foreach ($this->_postData as $key => $value) {
            if ($key != session_name() && ('_search_word' != $key || !$this->showSearchBox) && '_listName' != $key && '_limit' != $key && '_sort_order' != $key && '_user_data' != $key && !in_array($key, $this->aHiddenFieldIgnoreList)) {
                // also make sure it is not the group key... assuming that exists
                if (((!is_null($this->groupByCell) && $key != $this->groupByCell->name) || (is_null($this->groupByCell)) || false == $this->showGroupSelector)) {
                    if (is_array($value)) {
                        foreach ($value as $subKey => $subValue) {
                            if ('ChangeEditLanguage' != $subValue) {
                                $filter .= '<input type="hidden" name="'.TGlobal::OutHTML($key).'['.TGlobal::OutHTML($subKey).']" value="'.TGlobal::OutHTML($subValue)."\">\n";
                            }
                        }
                    } else {
                        $filter .= '<input type="hidden" name="'.TGlobal::OutHTML($key).'" value="'.TGlobal::OutHTML($value)."\">\n";
                    }
                }
            }
        }

        $filterHeader = "<div class=\"row TFullGroupTable\"><div class='form-inline'>\n";
        $filterContent = '';
        // now add group selector (if activated)
        if (null !== $this->groupByCell && $this->showGroupSelector) {
            $this->somethingToShow = true;

            $sGroupSelectorHTML = '<div class="form-group">
            <label>'.$this->showGroupSelectorText;

            $sGroupSelectorHTML .= "<select name=\"{$this->groupByCell->name}\" onChange=\"document.{$this->listName}._startRecord.value=0; document.{$this->listName}.submit();\" ".$this->style->GetGroupSelector().">\n";
            // add "show all" option to group selector
            $sGroupSelectorHTML .= '<option value=""';
            if (empty($this->_postData[$this->groupByCell->name])) {
                $sGroupSelectorHTML .= ' selected';
            }
            $sGroupSelectorHTML .= ">{$this->showAllGroupsText}</option>\n";
            // get the other groups from the database
            $query = $this->getFieldTranslationUtil()->getTranslatedQuery($this->_GetMainSQL());
            $katList = MySqlLegacySupport::getInstance()->query($query);
            while ($kat = MySqlLegacySupport::getInstance()->fetch_assoc($katList)) {
                if (!empty($kat[$this->groupByCell->name])) {
                    $sGroupSelectorHTML .= '<option value="'.htmlentities($kat[$this->groupByCell->name], ENT_COMPAT, 'UTF-8').'"';
                    if ($kat[$this->groupByCell->name] == $this->_postData[$this->groupByCell->name]) {
                        $sGroupSelectorHTML .= ' selected';
                    }

                    // use Callbackfunction on select box
                    if (null !== $this->groupByCell->selectFormat) {
                        if (function_exists($this->groupByCell->selectFormat)) {
                            $optionTitle = call_user_func($this->groupByCell->selectFormat, $kat[$this->groupByCell->name], $kat);
                        } else {
                            $optionTitle = "[Error: invalid callback function for field ({$this->groupByCell->name})]";
                        }
                    } else {
                        $optionTitle = htmlspecialchars($kat[$this->groupByCell->name]);
                    }

                    $sGroupSelectorHTML .= '>'.htmlspecialchars($optionTitle)."</option>\n";
                }
            }
            $sGroupSelectorHTML .= '</select>
            </label>
            </div>';
            $filterContent .= $sGroupSelectorHTML;
        }

        if ($this->showSearchBox) {
            $this->somethingToShow = true;
            $filterContent .= '<div class="form-group">';
            $filterContent .= '<input type="text" class="form-control form-control-sm" name="_search_word" value="'.TGlobal::OutHTML($this->_postData['_search_word'])."\" onKeyDown=\"if (window.event && window.event.keyCode == 13) document.{$this->listName}._startRecord.value=0\" onChange=\"document.{$this->listName}._startRecord.value=0;document.{$this->listName}.submit();\" placeholder=\"".$this->searchFieldText."\">\n";
            $filterContent .= "<input type=\"button\" class=\"form-control form-control-sm btn-sm btn-primary\" value=\"{$this->searchButtonText}\" onClick=\"document.{$this->listName}._startRecord.value=0;document.{$this->listName}.submit();\" class=\"btn btn-sm btn-primary\">
            </div>";
        }

        // use callback function if one was defined
        if (!is_null($this->searchBoxContent)) {
            $filterContent = $this->searchBoxContent.$filterContent;
        }

        $filterFooter = '';
        if (!is_null($this->searchBoxRow)) {
            $filterFooter .= $this->searchBoxRow;
        }
        $filterFooter .= '</div></div>';

        if ($this->somethingToShow) {
            $filter .= $filterHeader;
            $filter .= $filterContent;
            $filter .= $filterFooter;
        }

        return $filter;
    }

    /**
     * @return AuthenticityTokenManagerInterface
     */
    private function getAuthenticityTokenManager()
    {
        return ServiceLocator::get('chameleon_system_core.security.authenticity_token.authenticity_token_manager');
    }

    /**
     * @return InputFilterUtilInterface
     */
    private function getInputFilterUtil()
    {
        return ServiceLocator::get('chameleon_system_core.util.input_filter');
    }
}
