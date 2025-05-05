<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TCMSGroupedStatisticsGroup
{
    protected $sGroupTitle;
    public $sSubGroupColumn = '';
    protected $aGroupTotals = [];
    protected $aSubGroups = [];
    protected $aDataColumns = [];

    protected $aColumnNames = [];

    private $aDataBlock;
    public const VIEW_SUBTYPE = 'TCMSGroupedStatistics/TCMSGroupedStatisticsGroup';

    public static $iCurrentLevel = 0;

    /*
    * return all column names used in this and any sub groups
    */
    public function GetColumnNames()
    {
        $aNames = $this->aColumnNames;
        if (count($this->aSubGroups) > 0) {
            reset($this->aSubGroups);
            foreach (array_keys($this->aSubGroups) as $sGroupIndex) {
                $aTmpNames = $this->aSubGroups[$sGroupIndex]->GetColumnNames();
                foreach ($aTmpNames as $sName) {
                    if (!in_array($sName, $aNames)) {
                        $aNames[] = $sName;
                    }
                }
            }
            reset($this->aSubGroups);
        }

        return $aNames;
    }

    public function GetTotalFor($sColumnName)
    {
        if (array_key_exists($sColumnName, $this->aGroupTotals)) {
            return $this->aGroupTotals[$sColumnName];
        } else {
            return 0;
        }
    }

    public function GetGroupName()
    {
        return $this->sGroupTitle;
    }

    public function ReturnValueFor($sColumnName)
    {
        if (array_key_exists($sColumnName, $this->aDataColumns)) {
            return $this->aDataColumns[$sColumnName];
        } else {
            return 0;
        }
    }

    public function GetMaxValue()
    {
        return max($this->aGroupTotals);
    }

    /*
    * return the max group depth for all sub groups
    * @return int
    */
    public function GetSubGroupDepth($iDepth = 1)
    {
        $iMaxDepth = 0;
        if (count($this->aSubGroups) > 0) {
            reset($this->aSubGroups);
            foreach (array_keys($this->aSubGroups) as $sGroupIndex) {
                $iMaxDepthTmp = max($this->aSubGroups[$sGroupIndex]->GetSubGroupDepth($iDepth + 1), $iDepth);
                if ($iMaxDepthTmp > $iMaxDepth) {
                    $iMaxDepth = $iMaxDepthTmp;
                }
            }
        }

        return $iMaxDepth;
    }

    /*
    * return the number of rows that this group contains
    */
    public function GetRowCount()
    {
        $iDepth = 1;
        if (count($this->aSubGroups) > 0) {
            reset($this->aSubGroups);
            foreach (array_keys($this->aSubGroups) as $sGroupIndex) {
                $iDepth += $this->aSubGroups[$sGroupIndex]->GetRowCount();
            }
        }

        // else $iDepth += count($this->GetDataBlock());
        return $iDepth;
    }

    public function Render($sViewName, $aColumnNames, $iMaxGroupDepth = null, $bShowDiffColumn = false, $sRowPrefix = '', $sSeparator = ';', $sType = 'Core')
    {
        $oView = new TViewParser();
        /* @var $oView TViewParser */
        $oView->AddVar('oGroup', $this);
        $oView->AddVar('sGroupTitle', $this->sGroupTitle);
        $oView->AddVar('aGroupTotals', $this->aGroupTotals);
        $oView->AddVar('aSubGroups', $this->aSubGroups);
        $oView->AddVar('aDataColumns', $this->aDataColumns);
        $oView->AddVar('aColumnNames', $this->aColumnNames);
        $oView->AddVar('aColumnNames', $aColumnNames);
        $oView->AddVar('iMaxGroupDepth', $iMaxGroupDepth);
        $oView->AddVar('bShowDiffColumn', $bShowDiffColumn);
        $oView->AddVar('sRowPrefix', $sRowPrefix);
        $oView->AddVar('sSeparator', $sSeparator);
        ++self::$iCurrentLevel;

        $sContent = $oView->RenderObjectView($sViewName, self::VIEW_SUBTYPE, $sType);
        --self::$iCurrentLevel;

        return $sContent;
    }

    /**
     * init the object.
     *
     * @param string $sGroupTitle - name of the group
     * @param string $sSubGroupColumn
     */
    public function Init($sGroupTitle, $sSubGroupColumn = '')
    {
        $this->sGroupTitle = $sGroupTitle;
        $this->sSubGroupColumn = $sSubGroupColumn;
    }

    /*
     * add data to the group structure
     * @param array $aSubGroupDef - group name list
     * @param array $aDataCell
    */
    public function AddRow($aSubGroupDef, $aDataCell)
    {
        // update total
        $this->UpdateGroupTotals($aDataCell);
        if (0 == count($aSubGroupDef)) {
            if (!in_array($aDataCell['sColumnName'], $this->aColumnNames)) {
                $this->aColumnNames[] = $aDataCell['sColumnName'];
            }
        // $this->AddDataColumn($aDataCell); // disabled to keep low footprint
        } else {
            while (count($aSubGroupDef) > 0) {
                $sSubGroupName = array_shift($aSubGroupDef);
                if (!array_key_exists($sSubGroupName, $this->aSubGroups)) {
                    $this->aSubGroups[$sSubGroupName] = new self();

                    $aTmpColumnNames = array_keys($aDataCell, $sSubGroupName);
                    if (count($aTmpColumnNames) > 0) {
                        $sSubGroupColumn = $aTmpColumnNames[0];
                    } else {
                        $sSubGroupColumn = '';
                    }
                    $this->aSubGroups[$sSubGroupName]->Init($sSubGroupName, $sSubGroupColumn);
                }
                $this->aSubGroups[$sSubGroupName]->AddRow($aSubGroupDef, $aDataCell);
            }
        }
    }

    /**
     * update the totals for the group.
     *
     * @param array $aDataRow
     */
    protected function UpdateGroupTotals($aDataRow)
    {
        $sColumnName = $aDataRow['sColumnName'];
        $sColumnValue = $aDataRow['dColumnValue'];
        if (!array_key_exists($sColumnName, $this->aGroupTotals)) {
            $this->aGroupTotals[$sColumnName] = 0;
        }
        $this->aGroupTotals[$sColumnName] += $sColumnValue;
    }

    /**
     * add a column of data.
     *
     * @param array $aDataRow
     */
    protected function AddDataColumn($aDataRow)
    {
        $sColumnName = $aDataRow['sColumnName'];
        if (!in_array($sColumnName, $this->aColumnNames)) {
            $this->aColumnNames[] = $sColumnName;
        }
    }

    /**
     * returns the data columsn in the form array(array(field->val,field->val),array()..).
     */
    public function GetDataBlock()
    {
        if (is_null($this->aDataBlock)) {
            $this->aDataBlock = [];
            reset($this->aDataColumns);
            foreach ($this->aDataColumns as $sFieldName => $aRows) {
                foreach ($aRows as $iRowNumber => $dRowValue) {
                    if (!array_key_exists($iRowNumber, $this->aDataBlock)) {
                        $this->aDataBlock[$iRowNumber] = [];
                    }
                    $this->aDataBlock[$iRowNumber][$sFieldName] = $dRowValue;
                }
            }
            reset($this->aDataColumns);
        }

        return $this->aDataBlock;
    }
}
