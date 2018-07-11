<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\Service\ActivePageServiceInterface;

/**
 * a table used to display a resultset
 * example:
 * $oMedia =& TdbCmsMediaList::GetList();
 * $oHTMLList = new THTMLTable();.
 *
 * $oColumn = THTMLTableColumn::GetInstance('id','`cms_media`.`id`','ID',THTMLTableColumn::FIELD_TYPE_NUMBER);
 * $oColumn->iNumberOfDecimals = 3;
 * $oHTMLList->AddColumn($oColumn);
 *
 * $oColumn = THTMLTableColumn::GetInstance('description','`cms_media`.`description`','Name');
 * $oHTMLList->AddColumn($oColumn);
 *
 * $oColumn = THTMLTableColumn::GetInstance('time_stamp','`cms_media`.`time_stamp`','Created',THTMLTableColumn::FIELD_TYPE_DATE);
 * $oColumn->iDateFormatType = TCMSLocal::DATEFORMAT_SHOW_DATE;
 * $oHTMLList->AddColumn($oColumn);
 *
 * $oHTMLList->AddDefaultOrderBy('description','ASC');
 * $oHTMLList->Init($oMedia,5);
 * echo $oHTMLList->Render('standard');

/**/
class THTMLTable
{
    /**
     * number of records per page. use -1 if you want to show all on one page.
     *
     * @var int
     */
    public $iPageSize = null;

    /**
     * auto generated key used to identify the list.
     *
     * @var string
     */
    public $sListIdentKey = null;

    /**
     * set to true if you want to show the global search form.
     *
     * @var bool
     */
    public $bShowGlobalSearchForm = false;

    /**
     * set this to false if you want to hide the individual search forms of the columns.
     *
     * @var bool
     */
    public $bShowColumnSearchFields = true;

    /**
     * set to false if you want to allow sorting by only one column.
     *
     * @var bool
     */
    public $bAllowMultiSort = true;

    /**
     * defines the view to use for the global search form filter. use SetGlobalSearchFormFilterView to set these.
     *
     * @var array
     */
    protected $aGlobalSearchFormView = array('sViewName' => 'global-search-filter', 'sViewType' => 'Core');

    /**
     * the data for the table.
     *
     * @var TCMSRecordList
     */
    protected $oRecordList = null;

    /**
     * the current order by information. Has the form [field]=>direction (always use full db name for "field").
     *
     * @var array
     */
    protected $aOrderByFields = array();

    /**
     * iterator of columns.
     *
     * @var TIterator
     */
    protected $oColumns = null;

    /**
     * current page (starting at page 1).
     *
     * @var int
     */
    protected $iCurrentPage = 1;

    /**
     * search for.
     *
     * @var array
     */
    protected $aSearchData = null;

    /**
     * search term that is used to search through all columns.
     *
     * @var string
     */
    protected $sGlobalSearchTerm = null;

    /**
     * any tables that should trigger a cache clear.
     *
     * @var array
     *
     * @deprecated since 6.2.0 - no longer used.
     */
    protected $aCachTriggerTables = null;

    /**
     * holds a list of actions that can be performed on user selected records on the page. if at least one
     * action is defined, then a column with checkboxed will be added to the list.
     *
     * @var array
     */
    protected $aActions = array();

    /**
     * the method within the item object to use for a css class for a row.
     *
     * @var string
     */
    protected $sRowCSSMethodName = null;

    const URL_PARAM_PAGE = 'ipage';
    const URL_PARAM_SEARCH = 'search';
    const URL_PARAM_SEARCH_GLOBAL = 'gsearch';
    const URL_PARAM_ORDER = 'order';
    const URL_PARAM_CHANGE_PAGE_SIZE = 'iPageSize';

    /**
     * set the method called on each row on the item to fetch a css class for that row.
     *
     * @param string $sMethodName
     */
    public function SetRowCSSMethod($sMethodName)
    {
        $this->sRowCSSMethodName = $sMethodName;
    }

    /**
     * return the css for the row of the passed item.
     *
     * @param TCMSRecord $oItem
     *
     * @return string
     */
    public function GetRowCSSForItem(&$oItem)
    {
        $sCSS = '';
        if (!is_null($this->sRowCSSMethodName)) {
            $sMethod = $this->sRowCSSMethodName;
            if (method_exists($oItem, $sMethod)) {
                $sCSS = $oItem->$sMethod();
            }
        }

        return $sCSS;
    }

    public function __construct()
    {
        $this->oColumns = new TIterator();

        // add header includes (css/js)
        $oController = TGlobal::GetController();
        $oController->AddHTMLHeaderLine('<link href="'.URL_USER_CMS_PUBLIC.'/blackbox/classes/THTMLTable/default.css" rel="stylesheet" type="text/css" />');

        $sLine = '<script src="'.URL_USER_CMS_PUBLIC.'/blackbox/classes/THTMLTable/THTMLTable.js" type="text/javascript"></script>';
        $oController->AddHTMLHeaderLine($sLine);
    }

    /**
     * add an action to the list.
     *
     * @param string $sMethod      - method to call on the modul holding the list
     * @param string $sDisplayName - display name of the method
     */
    public function AddAction($sMethod, $sDisplayName)
    {
        $this->aActions[$sMethod] = $sDisplayName;
    }

    /**
     * define which view to use for the global search form. Note: This will also active the view
     * by setting bShowGlobalSearchForm to true.
     *
     * @param string $sViewName
     * @param string $sViewType - Core, Custom-Core, or Customer
     */
    public function SetGlobalSearchFormFilterView($sViewName, $sViewType = 'Core')
    {
        $this->aGlobalSearchFormView['sViewName'] = $sViewName;
        $this->aGlobalSearchFormView['sViewType'] = $sViewType;
        $this->bShowGlobalSearchForm = true;
    }

    protected function GetViewPath()
    {
        return 'THTMLTable';
    }

    /**
     * return current page.
     *
     * @return int
     */
    public function GetCurrentPage()
    {
        return $this->iCurrentPage;
    }

    /**
     * initialize object using oRecordList.
     *
     * @param TCMSRecordList $oRecordList
     * @param int            $iPageSize
     * @param array          $aCacheTriggerTables - any tables that should trigger a cache clear other than the one passed via oRecordList
     */
    public function Init(&$oRecordList, $iPageSize = 20, $aCacheTriggerTables = array())
    {
        $this->iPageSize = $iPageSize;
        $this->oRecordList = &$oRecordList;

        $this->sListIdentKey = 'THTMLTable'.$this->oRecordList->GetIdentString();

        // recover any data from post/get if found

        $oGlobal = TGlobal::instance();
        if ($oGlobal->UserDataExists($this->sListIdentKey)) {
            $this->SetListStateData($oGlobal->GetUserData($this->sListIdentKey));
        }
        $this->Refresh();
    }

    /**
     * set the lists stat date (page, current sorting info, etc).
     *
     * @param array $aStateData
     */
    protected function SetListStateData($aStateData)
    {
        if (array_key_exists(self::URL_PARAM_PAGE, $aStateData)) {
            $this->iCurrentPage = intval($aStateData[self::URL_PARAM_PAGE]);
        }
        if (array_key_exists(self::URL_PARAM_SEARCH, $aStateData)) {
            $this->aSearchData = $aStateData[self::URL_PARAM_SEARCH];
        }

        if (array_key_exists(self::URL_PARAM_ORDER, $aStateData)) {
            $this->aOrderByFields = $aStateData[self::URL_PARAM_ORDER];
            if (!is_array($this->aOrderByFields)) {
                $this->aOrderByFields = array();
            }
        }

        if (array_key_exists(self::URL_PARAM_CHANGE_PAGE_SIZE, $aStateData)) {
            $this->iPageSize = $aStateData[self::URL_PARAM_CHANGE_PAGE_SIZE];
        }

        if (array_key_exists(self::URL_PARAM_SEARCH_GLOBAL, $aStateData)) {
            $this->sGlobalSearchTerm = $aStateData[self::URL_PARAM_SEARCH_GLOBAL];
        }
    }

    /**
     * add default order by information to the table. note that the user will be able
     * to change these values using get/post.
     *
     * @param string $sFieldAlias
     * @param string $sDirection  - must be ASC or DESC
     */
    public function AddDefaultOrderBy($sFieldAlias, $sDirection)
    {
        if ('ASC' != $sDirection && 'DESC' != $sDirection) {
            trigger_error('ERROR: sDirection must be ASC or DESC in THTMLTable::AddDefaultOrderBy()', E_USER_ERROR);
        }
        $this->aOrderByFields[$sFieldAlias] = $sDirection;
    }

    /**
     * call refresh if you want to apply the current order by / paging etc to the list.
     */
    public function Refresh()
    {
        $iStartRecord = 0;
        if ($this->iPageSize > 0) {
            $iStartRecord = ($this->iCurrentPage - 1) * $this->iPageSize;
        }
        if ($iStartRecord < 0) {
            $iStartRecord = 0;
        }
        // add current search data to columns
        if (is_array($this->aSearchData)) {
            $this->oColumns->GoToStart();
            while ($oColumn = &$this->oColumns->Next()) {
                /** @var $oColumn THTMLTableColumn */
                if (array_key_exists($oColumn->sColumnAlias, $this->aSearchData)) {
                    $oColumn->SetSearchData($this->aSearchData[$oColumn->sColumnAlias]);
                }
            }
            $this->oColumns->GoToStart();
        }

        $aFilter = array();
        while ($oColumn = &$this->oColumns->Next()) {
            /** @var $oColumn THTMLTableColumn */
            $sTmpFilter = $oColumn->GetFilterQueryString();
            if (!empty($sTmpFilter)) {
                $aFilter[] = $sTmpFilter;
            }
        }
        $this->oColumns->GoToStart();

        if (count($aFilter) > 0) {
            $sFilterString = '('.implode(') AND (', $aFilter).')';
            $this->oRecordList->AddFilterString($sFilterString);
        }

        // now ad global search param (if it exists)
        if (!empty($this->sGlobalSearchTerm)) {
            $aFilter = array();
            while ($oColumn = &$this->oColumns->Next()) {
                /** @var $oColumn THTMLTableColumn */
                $sTmpFilter = $oColumn->GetFilterQueryString($this->sGlobalSearchTerm);
                if (!empty($sTmpFilter)) {
                    $aFilter[] = $sTmpFilter;
                }
            }
            $this->oColumns->GoToStart();

            if (count($aFilter) > 0) {
                $sFilterString = '('.implode(') OR (', $aFilter).')';
                $this->oRecordList->AddFilterString($sFilterString);
            }
        }

        $this->oRecordList->SetPagingInfo($iStartRecord, $this->iPageSize);

        if (is_array($this->aOrderByFields) && count($this->aOrderByFields) > 0) {
            $this->oRecordList->ChangeOrderBy($this->aOrderByFields);
        }
    }

    /**
     * add a column to the list.
     *
     * @param THTMLTable $oColumn
     */
    public function AddColumn(&$oColumn)
    {
        $oColumn->oOwningTable = &$this;
        $this->oColumns->AddItem($oColumn);
    }

    /**
     * return the order by URL for the column.
     *
     * @param THTMLTableColumn $oColumn
     *
     * @return string
     */
    public function GetOrderByURL(&$oColumn, $sDirection)
    {
        $aOrderByChange = array();

        $iOrderKey = '';
        if (array_key_exists($oColumn->sColumnAlias, $this->aOrderByFields)) {
            $iOrderKey = $oColumn->sColumnAlias;
        } elseif (array_key_exists($oColumn->sColumnDBName, $this->aOrderByFields)) {
            $iOrderKey = $oColumn->sColumnDBName;
        } else {
            $iOrderKey = $oColumn->sColumnAlias;
        }
        $aOrderByChange[$iOrderKey] = $sDirection;
        $boverwrite = true;
        if ($this->bAllowMultiSort) {
            $boverwrite = false;
        }

        return $this->GetURL(array(self::URL_PARAM_ORDER => $aOrderByChange), $boverwrite);
    }

    /**
     * return the url for a filter column (ie all parameters execpt those that belong to this columns filter.
     *
     * @param THTMLTableColumn $oColumn
     *
     * @return string
     */
    public function GetFilterURL(&$oColumn)
    {
        $aSearchData = array();
        $aSearchData[$oColumn->sColumnAlias] = '';

        return $this->GetURL(array(self::URL_PARAM_SEARCH => $aSearchData));
    }

    /**
     * return the base url for the global search form.
     *
     * @return string
     */
    public function GetGlobalSearchBaseURL()
    {
        return $this->GetURL(array(self::URL_PARAM_CHANGE_PAGE_SIZE => '', self::URL_PARAM_SEARCH_GLOBAL => ''));
    }

    /**
     * return the order by for the give column. returns null if the column is not included in the order by.
     *
     * @param THTMLTableColumn $oColumn
     *
     * @return string
     */
    public function GetCurrentOrderByForColumn(&$oColumn)
    {
        $sOrderBy = null;
        $iOrderKey = '';
        if (array_key_exists($oColumn->sColumnAlias, $this->aOrderByFields)) {
            $iOrderKey = $oColumn->sColumnAlias;
        } elseif (array_key_exists($oColumn->sColumnDBName, $this->aOrderByFields)) {
            $iOrderKey = $oColumn->sColumnDBName;
        } else {
            $iOrderKey = $oColumn->sColumnAlias;
        }

        if (array_key_exists($iOrderKey, $this->aOrderByFields)) {
            $sOrderBy = $this->aOrderByFields[$iOrderKey];
        }

        return $sOrderBy;
    }

    /**
     * returns the order by position for the column. returns false if the column is not ordered
     * positions start at 1.
     *
     * @param THTMLTableColumn $oColumn
     *
     * @return int
     */
    public function GetOrderByPositionForColumn(&$oColumn)
    {
        $iPos = false;
        reset($this->aOrderByFields);
        $i = 1;
        foreach (array_keys($this->aOrderByFields) as $sFieldName) {
            if ($sFieldName == $oColumn->sColumnAlias || $sFieldName == $oColumn->sColumnDBName) {
                $iPos = $i;
                break;
            }
            ++$i;
        }
        reset($this->aOrderByFields);

        return $iPos;
    }

    /**
     * return count on number of columnns ordered.
     *
     * @return int
     */
    public function GetNumberOfOrderedColumns()
    {
        $iCount = 0;
        if (is_array($this->aOrderByFields)) {
            $iCount = count($this->aOrderByFields);
        }

        return $iCount;
    }

    /**
     * return number of pages in list.
     *
     * @return int
     */
    public function GetNumberOfPages()
    {
        return ceil($this->oRecordList->Length() / $this->iPageSize);
    }

    /**
     * get a link to a page. you can pass "first, back, next, last, pagenumber".
     *
     * @param string $sPage
     *
     * @return string
     */
    public function GetPageURL($sPage)
    {
        $iPage = intval($sPage);
        $iLastPage = $this->GetNumberOfPages();
        switch ($sPage) {
            case 'first':
                $iPage = 1;
                break;
            case 'back':
                $iPage = $this->iCurrentPage - 1;
                break;
            case 'next':
                $iPage = $this->iCurrentPage + 1;
                break;
            case 'last':
                $iPage = $iLastPage;
                break;
            default:
                break;
        }
        if ($iPage < 1) {
            $iPage = $iLastPage;
        } elseif ($iPage > $iLastPage) {
            $iPage = 1;
        }

        return $this->GetURL(array(self::URL_PARAM_PAGE => $iPage));
    }

    /**
     * fetch url for list. you can change some parameters via the array.
     *
     * @param array $aChangeParameters
     *
     * @return string
     */
    public function GetURL($aChangeParameters = array(), $bOverwrite = false)
    {
        $aParams = array(self::URL_PARAM_ORDER => $this->aOrderByFields, self::URL_PARAM_PAGE => $this->iCurrentPage, self::URL_PARAM_SEARCH => $this->aSearchData, self::URL_PARAM_CHANGE_PAGE_SIZE => $this->iPageSize, self::URL_PARAM_SEARCH_GLOBAL => $this->sGlobalSearchTerm);

        foreach ($aChangeParameters as $sKey => $sNewParam) {
            if (is_array($sNewParam)) {
                if ($bOverwrite) {
                    $aParams[$sKey] = $sNewParam;
                } else {
                    foreach ($sNewParam as $sSubKey => $sSubParamValue) {
                        if (empty($sSubParamValue) && is_array($aParams[$sKey]) && array_key_exists($sSubKey, $aParams[$sKey])) {
                            unset($aParams[$sKey][$sSubKey]);
                        } elseif (is_array($aParams[$sKey])) {
                            $aParams[$sKey][$sSubKey] = $sSubParamValue;
                        } elseif (!is_array($aParams[$sKey])) {
                            $aParams[$sKey] = array($sSubKey => $sSubParamValue);
                        }
                    }
                }
            } else {
                if (empty($sNewParam) && array_key_exists($sKey, $aParams)) {
                    unset($aParams[$sKey]);
                } else {
                    $aParams[$sKey] = $sNewParam;
                }
            }
        }

        if (TGlobal::IsCMSMode()) {
            $oGlobal = TGlobal::instance();
            $sPagedef = $oGlobal->GetUserData('pagedef');
            $sID = $oGlobal->GetUserData('id');
            $sTableID = $oGlobal->GetUserData('tableid');

            $aParams['pagedef'] = $sPagedef;

            if (!empty($sID)) {
                $aParams['id'] = $sID;
            }
            if (!empty($sTableID)) {
                $aParams['tableid'] = $sTableID;
            }

            $sURL = PATH_CMS_CONTROLLER.'?'.TTools::GetArrayAsURL($aParams);
        } else {
            $sURL = $this->getActivePageService()->getLinkToActivePageRelative(array($this->sListIdentKey => $aParams));
        }

        return $sURL;
    }

    /**
     * return current global search term.
     *
     * @return string
     */
    public function GetGlobalSearchTerm()
    {
        return $this->sGlobalSearchTerm;
    }

    /**
     * generate global search form.
     *
     * @return string
     */
    public function RenderGlobalSearchForm($aCallTimeVars = array())
    {
        return $this->Render($this->aGlobalSearchFormView['sViewName'], $this->aGlobalSearchFormView['sViewType'], $aCallTimeVars);
    }

    /**
     * used to display an article.
     *
     * @param string $sViewName     - the view to use
     * @param string $sViewType     - where the view is located (Core, Custom-Core, Customer)
     * @param array  $aCallTimeVars - place any custom vars that you want to pass through the call here
     *
     * @return string
     */
    public function Render($sViewName = 'standard', $sViewType = 'Core', $aCallTimeVars = array())
    {
        $oView = new TViewParser();

        $oGlobal = TGlobal::instance();
        $oModule = &$oGlobal->GetExecutingModulePointer();
        $sControllingModuleSpotName = '';
        if (null !== $oModule) {
            $sControllingModuleSpotName = $oModule->sModuleSpotName;
        }

        $oView->AddVar('oHTMLTable', $this);
        $oView->AddVar('oRecordList', $this->oRecordList);
        $oView->AddVar('oColumns', $this->oColumns);
        $oView->AddVar('aActions', $this->aActions);
        $oView->AddVar('sControllingModuleSpotName', $sControllingModuleSpotName);

        $oView->AddVar('sListIdentKey', $this->sListIdentKey);
        $oView->AddVar('iCurrentPage', $this->iCurrentPage);
        $oView->AddVar('aSearchData', $this->aSearchData);

        $oView->AddVar('aCallTimeVars', $aCallTimeVars);
        $aOtherParameters = $this->GetAdditionalViewVariables($sViewName, $sViewType);
        $oView->AddVarArray($aOtherParameters);

        return $oView->RenderObjectView($sViewName, $this->GetViewPath(), $sViewType);
    }

    /**
     * use this method to add any variables to the render method that you may
     * require for some view.
     *
     * @param string $sViewName - the view being requested
     * @param string $sViewType - the location of the view (Core, Custom-Core, Customer)
     *
     * @return array
     */
    protected function GetAdditionalViewVariables($sViewName, $sViewType)
    {
        return array();
    }

    /**
     * Add view based clear cache triggers for the Render method here.
     *
     * @param array  $aClearTriggers - clear trigger array (with current contents)
     * @param string $sViewName      - view being requested
     * @param string $sViewType      - location of the view (Core, Custom-Core, Customer)
     *
     * @deprecated since 6.2.0 - no longer used.
     */
    protected function AddClearCacheTriggers(&$aClearTriggers, $sViewName, $sViewType)
    {
    }

    /**
     * used to set the id of a clear cache (ie. related table).
     *
     * @param string $sTableName - the table name
     *
     * @return string
     *
     * @deprecated since 6.2.0 - no longer used.
     */
    protected function GetClearCacheTriggerTableValue($sTableName)
    {
        return '';
    }

    /**
     * returns an array with all table names that are relevant for the render function.
     *
     * @param string $sViewName - the view name being requested (if know by the caller)
     * @param string $sViewType - the view type (core, custom-core, customer) being requested (if know by the caller)
     *
     * @return array
     *
     * @deprecated since 6.2.0 - no longer used.
     */
    protected function GetCacheRelevantTables($sViewName = null, $sViewType = null)
    {
        return [];
    }

    /**
     * @return ActivePageServiceInterface
     */
    private function getActivePageService()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.active_page_service');
    }
}
