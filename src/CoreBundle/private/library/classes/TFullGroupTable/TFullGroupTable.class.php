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
use ChameleonSystem\CoreBundle\Util\UrlUtil;

/**
 * class TFullGroupTable extends TGroupTable to add a header, sorting, and searching.
 *
 **/
class TFullGroupTable extends TGroupTable
{
    /**
     * array of TGroupTableFields
     * holds the header info
     * fields should be placed in here in the order of appearance.
     */
    public array $headerCells = [];

    /**
     * see class definition to view available styles.
     *
     * @var TFullGroupTableStyle
     */
    public $style;

    /**
     * string to display for showing the next page (default = 'next page').
     */
    public string $nextPageText = 'next page';

    /**
     * string to display for showing the previous page (default = 'previous page').
     */
    public string $previousPageText = 'previous page';

    /**
     * text to display the current page records, and the total hits.
     * default: "Records $startRecord$ - $endRecord$ of $totalFound$"
     * you can use the following placeholders:
     * $startRecord$, $endRecord$, $totalFound$.
     */
    public string $hitText = 'Records $startRecord$ - $endRecord$ of $totalFound$';

    /**
     * a string that may have the value 'top', 'bottom', 'none', or 'top_and_bottom' (default: 'top_and_bottom').
     */
    public string $pageingLocation = 'top_and_bottom';

    /**
     * if set to true a select box will appear that allows you to reduce the list to one group (default = true);
     * set it to false if you use a external group selector, be sure $useGroupSelector is true!
     */
    public bool $showGroupSelector = true;

    /**
     * group by a given value.
     */
    public bool $useGroupSelector = true;

    /**
     * text to display for showing all groups (default: 'Show All').
     */
    public string $showAllGroupsText = 'Show All';

    /**
     * text to display for the group selector (default: 'group').
     */
    public string $showGroupSelectorText = 'group';

    /**
     * an array of the form 'db_field'=>'search_style'. search style may be: 'none',
     * 'left', 'right', 'both', 'none', or 'full'
     * (default: NULL).
     */
    public ?array $searchFields = null;

    /**
     * text to display for the searchfield (default = 'search').
     */
    public string $searchFieldText = 'search';

    /**
     * text to display for the search button (default = 'search').
     */
    public string $searchButtonText = 'search';

    /**
     * the name of the list.
     * you need to set this if there is more than one list on one page and for caching.
     */
    public string $listName = 'full_group_table';

    /**
     * text to display when no records are found.
     */
    public string $notFoundText = 'no records found';

    /**
     * internal post data.
     */
    public array $_postData = [];

    /**
     * indicates if a searchbox should be shown.
     */
    public bool $showSearchBox = true;

    /**
     * extra content to add into the search box row (default = NULL).
     */
    public ?string $searchBoxContent = null;

    /**
     * extra content to add into below the search box row (default = NULL).
     */
    public ?string $searchBoxRow = null;

    /**
     * has to be 'GET' or 'POST'. default is 'POST'
     * all form data (like search, change page, etc) will be submitted using this method.
     */
    public string $formActionType = 'POST';

    /**
     * array of fieldnames that will be ignored as hidden fields.
     */
    public array $aHiddenFieldIgnoreList = [];

    /**
     * array that holds data for custom searchbox parameters.
     * We need this to store the table object including this parameters in CMS cache.
     */
    public array $customSearchFieldParameter = [];

    /**
     * optional show a rows per page pulldown menu.
     */
    public bool $showRowsPerPageChooser = false;

    /**
     * indicates if the search header is filled.
     */
    public bool $somethingToShow = false;

    /**
     * the generated html header section.
     */
    public string $sHeaderSection = '';

    /**
     * the generated html content section.
     */
    public string $sContentSection = '';

    /**
     * the generated html filter section.
     */
    public string $sFilterSection = '';

    /**
     * the generated html paging section.
     */
    public string $sPagingSection = '';

    /**
     * CSS classes for the table tag.
     */
    protected string $tableCSS = 'table table-sm table-hover TCMSListManagerFullGroupTable';

    public function __construct($postData = [])
    {
        parent::__construct();
        $this->style = new TFullGroupTableStyle();
        $this->_postData = $postData;
    }

    /**
     * initialises the class, set postdata here.
     *
     * @param array $postData
     */
    public function Init($postData = [])
    {
        $this->style = new TFullGroupTableStyle();
        $this->_postData = $postData;
    }

    /**
     * @param array|null $paramArray
     */
    public function AddCustomSearchFieldParameter($paramArray = null)
    {
        if (is_array($paramArray)) {
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
        if (isset($this->customSearchFieldParameter[$param])) {
            $val = $this->customSearchFieldParameter[$param];
        }

        return $val;
    }

    /**
     * add a header cell.
     *
     * @param string|non-empty-array<string, string> $name - database name of the column name may be a string, or an array. if it is an array
     *                                                     it should be of the form 'name'=>'full_name'
     * @param string $align - horizontal alignment to use in the cell
     * @param resource $format - a callback function to use for the column (the function will get 2 parameters,
     *                         the value, and the row. The string returned by the function will be displayed
     *                         in the cell
     * @param int $colSpan - the colSpan parameter of the cell
     * @param bool $allowSort - allow sorting by that column
     * @param mixed $width - force width to X pixels
     * @param int $columnPosition - the array key position where the header will be added (array key starts with 0)
     */
    public function AddHeaderField($name, $align = 'left', $format = null, $colSpan = 1, $allowSort = true, $width = false, $columnPosition = null)
    {
        $oTGroupTableHeaderField = new TGroupTableHeaderField($name, $align, $format, $colSpan, $allowSort, $width);
        if (!is_null($columnPosition) && count($this->headerCells) > 0) {
            $count = 0;
            $aTmpHeaderFields = [];
            reset($this->headerCells);
            foreach ($this->headerCells as $key => $oTmpGroupTable) {
                ++$count;
                if ($key === $columnPosition) {
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
        $aTmpHeader = [];
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
     * @throws ChameleonSystem\CoreBundle\Security\AuthenticityToken\InvalidTokenFormatException
     */
    private function getManagedAttributes()
    {
        $inputFilterUtil = $this->getInputFilterUtil();
        $tableConfigurationId = $inputFilterUtil->getFilteredInput('id');
        $tableEditorConfId = TTools::GetCMSTableId('cms_tbl_conf');
        $authenticityTokenValue = current($this->getAuthenticityTokenManager()
            ->getTokenPlaceholderAsParameter(AuthenticityTokenManagerInterface::TOKEN_FORMAT_ARRAY));
        $attributes = [
            'data-table-managed' => null,
            'data-authenticity-token-id' => AuthenticityTokenManagerInterface::TOKEN_ID,
            'data-authenticity-token-value' => $authenticityTokenValue,
            'data-table-controller' => PATH_CMS_CONTROLLER,
            'data-table-conf-id' => $tableConfigurationId,
            'data-table-editor-conf-id' => $tableEditorConfId,
            'data-table-name' => $this->sTableName,
        ];

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
            if ('top' === $this->pageingLocation || 'top_and_bottom' === $this->pageingLocation) {
                $sTable .= $this->sPagingSection;
            }

            $sTable .= '<div class="full-group-table table-responsive">';
            $sTable .= '<table '.$this->getInlineFromAttributes($this->getManagedAttributes()).'class="'.$this->getTableCSS().'">';
            $sTable .= $this->GetCellWidths();
            $sTable .= $this->sHeaderSection;
            $sTable .= $this->sContentSection;
            $sTable .= '</table>';
            $sTable .= '</div>';

            if ('bottom' === $this->pageingLocation || 'top_and_bottom' === $this->pageingLocation) {
                $sTable .= $this->sPagingSection;
            }
        } else {
            $notfoundRow = '<div class="alert alert-warning mb-0 rounded-0 mt-0">
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
                        reset($this->orderList);

                        $tmpOrderList = $this->orderList;

                        // remove group field from temporary orderList to prevent wrong order counts
                        if (!is_null($this->groupByCell) && !is_null($this->groupByCell->name) && !empty($this->groupByCell->name)) {
                            unset($tmpOrderList[$this->groupByCell->name]);
                        }

                        foreach ($tmpOrderList as $field => $dir) {
                            if (0 != strcmp($cellObj->name, $field)) {
                                ++$orderCount;
                            } else {
                                break;
                            }
                        }

                        if ('ASC' == $tmpOrderList[$cellObj->name]) {
                            $orderImage = '&nbsp;('.$orderCount.')&nbsp;<i class="fas fa-sort-alpha-down" style="font-size: 1.3em;" title="'.TGlobal::OutHTML(ServiceLocator::get('translator')->trans('chameleon_system_core.list.form_sort_order_asc')).'"></i>';
                        } else {
                            $orderImage = '&nbsp;('.$orderCount.')&nbsp;<i class="fas fa-sort-alpha-up" style="font-size: 1.3em;" title="'.TGlobal::OutHTML(ServiceLocator::get('translator')->trans('chameleon_system_core.list.form_sort_order_desc')).'"></i>';
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

        $hitText = str_replace(['$startRecord$', '$endRecord$', '$totalFound$'], [$this->startRecord + 1, $next_startValue, $this->recordCount], $this->hitText);
        $tableNavigation .= '
                    <div id="'.TGlobal::OutHTML($this->listName).'_navi" class="p-2">
        <script>
        function switchPage(startRecord) {
            document.'.$this->listName.'._startRecord.value = startRecord;
            document.'.$this->listName.'.submit();
        }
        </script>
        <div class="d-flex justify-content-between flex-wrap">';
        $tableNavigation .= '<nav>';
        $tableNavigation .= '<ul class="pagination pagination-md TFullGroupTablePagination flex-wrap">';
        $tableNavigation .= '<li class="disabled page-item"><a href="#" class="page-link"><i class="fas fa-list-ul d-none d-lg-inline pr-2"></i>'.$hitText.'</a></li>';

        if ($this->startRecord > 0 && -1 != $this->showRecordCount) {
            $tableNavigation .= '<li class="page-item"><a href="javascript:switchPage(\'0\');" class="page-link"><i class="fas fa-fast-backward" aria-hidden="true"></i></a></li>';
            $tableNavigation .= '<li class="page-item"><a href="javascript:switchPage(\''.$back_startValue.'\');" class="page-link"><i class="fas fa-backward" aria-hidden="true"></i></a></li>';
        } else {
            $tableNavigation .= '<li class="disabled page-item"><a href="#" class="page-link"><i class="fas fa-fast-backward" aria-hidden="true"></i></a></li>';
            $tableNavigation .= '<li class="disabled page-item"><a href="#" class="page-link"><i class="fas fa-backward" aria-hidden="true"></i></a></li>';
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

        for ($i = $pagingStartPage; $i < $pageCount && $i <= ($maxPagingElements + $pagingStartPage); ++$i) {
            $active = '';
            if ($i == $currentPage) {
                $active = 'active';
            }

            $tableNavigation .= '<li class="page-item '.$active.'"><a href="javascript:switchPage(\''.($i * $recordsPerPage).'\');" class="page-link">'.($i + 1).'</a></li>';
        }

        if (($this->startRecord + $this->showRecordCount) < $this->recordCount && -1 != $this->showRecordCount) {
            $tableNavigation .= '<li class="page-item"><a href="javascript:switchPage(\''.$next_startValue.'\');" class="page-link"><i class="fas fa-forward" aria-hidden="true"></i></a></li>';
            $tableNavigation .= '<li class="page-item"><a href="javascript:switchPage(\''.(($pageCount - 1) * $recordsPerPage).'\');" class="page-link"><i class="fas fa-fast-forward" aria-hidden="true"></i></a></li>';
        } else {
            $tableNavigation .= '<li class="page-item disabled"><a href="#" class="page-link"><i class="fas fa-forward" aria-hidden="true"></i></a></li>';
            $tableNavigation .= '<li class="page-item disabled"><a href="#" class="page-link"><i class="fas fa-fast-forward" aria-hidden="true"></i></a></li>';
        }

        $tableNavigation .= '
            </ul>';
        $tableNavigation .= '</nav>';

        if ($this->showRowsPerPageChooser) {
            $tableNavigation .= '<div class="TFullGroupTablePerPageSelect">
            <div class="input-group">
                <div class="input-group-prepend"><span class="input-group-text">'.TGlobal::OutHTML(ServiceLocator::get('translator')->trans('chameleon_system_core.list.form_records_per_page')).'</span></div>
                <select name="_limit" class="form-control" onChange="document.'.$this->listName.'._startRecord.value=0;document.'.$this->listName.'.submit();">
            ';

            $userCount = $this->showRecordCount;
            if (!empty($this->_postData['_limit'])) {
                $userCount = $this->_postData['_limit'];
            }

            $aPageSize = [10, 20, 50, 100, 200, 500];
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
        $filter = '<form name="'.TGlobal::OutHTML($this->listName).'" id="'.TGlobal::OutHTML($this->listName).'" method="'.TGlobal::OutHTML($this->formActionType).'" action="" accept-charset="UTF-8">
      <input type="hidden" name="_user_data" value="">
      <input type="hidden" name="_sort_order" value="">
      <input type="hidden" name="_listName" value="'.TGlobal::OutHTML($this->listName).'">
';
        reset($this->_postData);
        foreach ($this->_postData as $key => $value) {
            if ($key != session_name() && ('_search_word' != $key || !$this->showSearchBox) && '_listName' != $key && '_limit' != $key && '_sort_order' != $key && '_user_data' != $key && !in_array($key, $this->aHiddenFieldIgnoreList)) {
                // also make sure it is not the group key... assuming that exists
                if ((!is_null($this->groupByCell) && $key != $this->groupByCell->name) || is_null($this->groupByCell) || false == $this->showGroupSelector) {
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

        $filterHeader = '<div class="d-flex TFullGroupTable form-inline">';
        $filterContent = '';
        // now add group selector (if activated)
        if (null !== $this->groupByCell && $this->showGroupSelector) {
            $this->somethingToShow = true;

            $sGroupSelectorHTML = '<div class="form-group mr-2">
            <label>'.$this->showGroupSelectorText.'</label>';

            $sGroupSelectorHTML .= "<select class=\"form-control form-control-sm submitOnSelect\" name=\"{$this->groupByCell->name}\" data-select2-option=\"{}\" onChange=\"document.{$this->listName}._startRecord.value=0; document.{$this->listName}.submit();\" ".$this->style->GetGroupSelector().">\n";
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
            </div>';
            $filterContent .= $sGroupSelectorHTML;
        }

        if ($this->showSearchBox) {
            $this->somethingToShow = true;

            $filterContent .= '<div class="form-group mr-2 typeahead-relative">';

            $formatString = '<input id="searchLookup" name="_search_word" class="form-control form-control-sm entry-search-field" placeholder="%s" value="%s" autocomplete="off" data-source-url="%s" data-record-url="%s" data-onclick-function="%s">';
            $filterContent .= sprintf(
                $formatString,
                TGlobal::OutHTML($this->searchFieldText),
                TGlobal::OutHTML($this->_postData['_search_word']),
                TGlobal::OutHTML($this->getRecordAutocompleteUrl()),
                TGlobal::OutHTML($this->getRecordUrl()),
                TGlobal::OutHTML($this->onClick)
            );

            $filterContent .= '</div>
                                <div class="form-group">';

            $formatString = '<input type="button" class="form-control form-control-sm btn btn-sm btn-primary" value="%s">';
            $filterContent .= sprintf($formatString, TGlobal::OutHTML($this->searchButtonText));

            $filterContent .= '</div>';
        }

        // use callback function if one was defined
        if (!is_null($this->searchBoxContent)) {
            $filterContent = $this->searchBoxContent.$filterContent;
        }

        $filterFooter = '';
        if (!is_null($this->searchBoxRow)) {
            $filterFooter .= $this->searchBoxRow;
        }
        $filterFooter .= '</div>';

        if ($this->somethingToShow) {
            $filter .= $filterHeader;
            $filter .= $filterContent;
            $filter .= $filterFooter;
        }

        return $filter;
    }

    private function getRecordAutocompleteUrl(): string
    {
        $pagedef = $this->getTableEditorPagedef('tablemanager');
        $inputFilterUtil = $this->getInputFilterUtil();
        $tableId = $inputFilterUtil->getFilteredInput('id');
        $restrictionField = $inputFilterUtil->getFilteredInput('sRestrictionField');
        $restriction = $inputFilterUtil->getFilteredInput('sRestriction');
        $targetListClass = $inputFilterUtil->getFilteredInput('targetListClass');

        $urlUtil = $this->getUrlUtil();
        $sAjaxURL = $urlUtil->getArrayAsUrl([
            'id' => $tableId,
            'pagedef' => $pagedef,
            '_rmhist' => 'false',
            'sOutputMode' => 'Ajax',
            'module_fnc[contentmodule]' => 'ExecuteAjaxCall',
            '_fnc' => 'getAutocompleteRecords',
            'sRestrictionField' => $restrictionField,
            'sRestriction' => $restriction,
            'recordID' => '',
            'targetListClass' => $targetListClass,
        ], PATH_CMS_CONTROLLER.'?', '&');

        return $sAjaxURL;
    }

    private function getRecordUrl(): string
    {
        $pagedef = $this->getTableEditorPagedef('tableeditor');
        $inputFilterUtil = $this->getInputFilterUtil();
        $tableId = $inputFilterUtil->getFilteredInput('id');
        $urlUtil = $this->getUrlUtil();
        $recordUrl = $urlUtil->getArrayAsUrl([
            'pagedef' => $pagedef,
            'tableid' => $tableId,
            'sRestriction' => '',
            'sRestrictionField' => '',
        ], PATH_CMS_CONTROLLER.'?', '&');

        return $recordUrl;
    }

    private function getTableEditorPagedef($pagedef): string
    {
        $inputFilterUtil = $this->getInputFilterUtil();
        $customTableEditor = $inputFilterUtil->getFilteredInput('sTableEditorPagdef', '');
        if ('' !== $customTableEditor) {
            $pagedef = $customTableEditor;
        }

        return $pagedef;
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

    private function getUrlUtil(): UrlUtil
    {
        return ServiceLocator::get('chameleon_system_core.util.url');
    }
}
