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
 * holds all relevant information for a column within the table.
 * /**/
class THTMLTableColumn
{
    public const SELF_FIELD_DEF = 'THTMLTableColumn';
    public const FIELD_TYPE_TEXT = 'THTMLTableColumn';
    public const FIELD_TYPE_NUMBER = 'THTMLTableColumnNumber';
    public const FIELD_TYPE_DATE = 'THTMLTableColumnDate';
    public const FIELD_TYPE_LOOKUP = 'THTMLTableColumnLookup';

    /**
     * the column db alias in the query.
     *
     * @var string
     */
    public $sColumnAlias = '';

    /**
     * column db name (example: `my_table`.`my_field`).
     *
     * @var string
     */
    public $sColumnDBName = '';

    /**
     * column title.
     *
     * @var string
     */
    public $sTitle = '';

    /**
     * pointer to the owning table.
     *
     * @var THTMLTable
     */
    public $oOwningTable;

    /**
     * set to false if you want to prevent the user from filtering this field.
     *
     * @var bool
     */
    public $bAllowFilter = true;

    /**
     * set to false if you do not want to allow the user to search the contents of this field.
     *
     * @var bool
     */
    public $bAllowSort = true;

    /**
     * if a callback is to be used for formating instead of the default formating method.
     *
     * @var string
     */
    protected $sFormatCallbackFunction;

    /**
     * the current order by direction for the column. set to null if the column is not ordered.
     *
     * @var string
     */
    protected $sOrderByDirection;

    /**
     * any search data for the column.
     *
     * @var string
     */
    protected $searchFilter = '';

    /**
     * set search data.
     *
     * @param string $searchData
     */
    public function SetSearchData($searchData)
    {
        $this->searchFilter = $searchData;
    }

    /**
     * return sql restriction for the acting filter.
     *
     * @param string $sSearchFilter - the filter to use. SET THIS ONLY IF YOU WANT TO OVERWRITE THE CURRENT searchFilter!
     *
     * @return string
     */
    public function GetFilterQueryString($sSearchFilter = null)
    {
        if (is_null($sSearchFilter) && $this->bAllowFilter) {
            $sSearchFilter = $this->searchFilter;
        }
        $sFilter = '';
        if (!empty($sSearchFilter)) {
            $sFilter = $this->sColumnDBName." LIKE '%".MySqlLegacySupport::getInstance()->real_escape_string($sSearchFilter)."%'";
        }

        return $sFilter;
    }

    /**
     * Return field object.
     *
     * @param string $sColumnAlias - db alias
     * @param string $sColumnDBName - full db name (`table`.`field`) of the field
     * @param string $sTitle - field title
     * @param string $sType - field type (must be of the form: Class,SubType,Type)
     * @param string $sCallback - callback to use for formating
     *
     * @return THTMLTableColumn
     */
    public static function GetInstance($sColumnAlias, $sColumnDBName, $sTitle, $sType = self::FIELD_TYPE_TEXT, $sCallback = null)
    {
        $oInstance = self::GetClassForType($sType);
        /** @var $oInstance THTMLTableColumn */
        if (is_null($oInstance)) {
            trigger_error('Error: Type ['.$sType.'] has no class in THTMLTable/THTMLTableColumn::GetInstance()', E_USER_ERROR);
        }
        $oInstance->sColumnAlias = $sColumnAlias;
        $oInstance->sColumnDBName = $sColumnDBName;
        $oInstance->sTitle = $sTitle;
        if (!is_null($sCallback)) {
            $oInstance->SetFormatCallback($sCallback);
        }

        return $oInstance;
    }

    /**
     * returns a css class used for the column.
     *
     * @return string
     */
    public function GetColumnFormatCSSClass()
    {
        return 'THTMLTableColumnText';
    }

    /**
     * return order by string (null, ASC, or DESC).
     */
    public function GetOrderByDirection()
    {
        return $this->sOrderByDirection;
    }

    /**
     * render a field value.
     *
     * @param TCMSRecord $oTableRow - the complete record object
     *
     * @return string
     */
    public function GetFieldValue($oTableRow)
    {
        $sValue = '';
        if (array_key_exists($this->sColumnAlias, $oTableRow->sqlData)) {
            $sValue = $oTableRow->sqlData[$this->sColumnAlias];
        }
        $sFormatedValue = '';
        if (!is_null($this->sFormatCallbackFunction)) {
            $sFunction = $this->sFormatCallbackFunction;
            if (method_exists($oTableRow, $this->sFormatCallbackFunction)) {
                $sFormatedValue = $oTableRow->$sFunction($sValue, $this->sColumnAlias);
            } elseif (function_exists($this->sFormatCallbackFunction)) {
                $sFormatedValue = $sFunction($sValue, $oTableRow, $this->sColumnAlias);
            } else {
                trigger_error('Error: Callback ['.$sFunction.'] does not exist in THTMLTable/THTMLTableColumn::GetFieldValue()', E_USER_ERROR);
            }
        } else {
            $sFormatedValue = $this->FormatValue($sValue, $oTableRow);
        }

        return $sFormatedValue;
    }

    /**
     * method used to format the given value. overwrite this method for every column type you write.
     *
     * @param string $sValue
     * @param TCMSRecord $oTableRow
     *
     * @return string
     */
    protected function FormatValue($sValue, $oTableRow)
    {
        return TGlobal::OutHTML($sValue);
    }

    /**
     * set the callback function used to format the contents.
     *
     * @param string $sCallback
     */
    protected function SetFormatCallback($sCallback)
    {
        $this->sFormatCallbackFunction = $sCallback;
    }

    /**
     * create a class for a given type.
     *
     * @param string $sType (must be CLASSNAME)
     *
     * @return THTMLTableColumn
     */
    protected static function GetClassForType($sType)
    {
        $oInstance = null;
        $sClassName = null;

        // old format (Class, Subtype, Type) lookup
        if (strstr($sType, ',')) {
            $aParts = explode(',', $sType);
            if (3 == count($aParts)) {
                $sClassName = trim($aParts[0]);
            }
        } else {
            $sClassName = $sType;
        }

        if (is_null($sClassName)) {
            trigger_error('ERROR: sType has invalid format (must be of the form CLASSNAME,SUBTYPE,TYPE)', E_USER_ERROR);
        } else {
            $oInstance = new $sClassName();
        }

        return $oInstance;
    }

    /**
     * set the current order by direction for the column. note: you do not need to set this - THTMLTable will take care of that for you.
     *
     * @param string $sDirection
     */
    public function SetOrderByDirection($sDirection)
    {
        $this->sOrderByDirection = $sDirection;
    }

    protected function GetViewPath()
    {
        return 'THTMLTable/THTMLTableColumn';
    }

    /**
     * used to display the column.
     *
     * @param string $sViewName - the view to use
     * @param string $sViewType - where the view is located (Core, Custom-Core, Customer)
     * @param array $aCallTimeVars - place any custom vars that you want to pass through the call here
     *
     * @return string
     */
    public function Render($sViewName = 'standard', $sViewType = 'Core', $aCallTimeVars = [])
    {
        $oView = new TViewParser();
        $oView->AddVar('oColumn', $this);
        $oView->AddVar('oOwningTable', $this->oOwningTable);
        $oView->AddVar('searchFilter', $this->searchFilter);
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
        $aViewVariables = [];

        return $aViewVariables;
    }
}
