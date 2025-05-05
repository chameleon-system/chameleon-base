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
 * used to generate grouped statistics.
 * /**/
class TCMSGroupedStatistics
{
    private $aBlocks = [];
    public $bShowDiffColumn = false;
    private $sBlockName;

    public const VIEW_SUBTYPE = 'TCMSGroupedStatistics';

    /*
     * add a new block to the list
    */
    public function AddBlock($sBlockName, $sQuery, $aSubGroups = [])
    {
        $tRes = MySqlLegacySupport::getInstance()->query($sQuery);
        $sMySqlError = MySqlLegacySupport::getInstance()->error();
        if (!empty($sMySqlError)) {
            trigger_error('SQL Error: '.$sMySqlError, E_USER_WARNING);
        }
        if (!array_key_exists($sBlockName, $this->aBlocks)) {
            $this->aBlocks[$sBlockName] = new TCMSGroupedStatisticsGroup();
            $this->aBlocks[$sBlockName]->Init($sBlockName);
        }
        while ($aDataRow = MySqlLegacySupport::getInstance()->fetch_assoc($tRes)) {
            $aRealNames = [];
            if (is_array($aSubGroups)) {
                foreach ($aSubGroups as $sGroupName) {
                    if (strlen($aDataRow[$sGroupName]) > 0) {
                        $aRealNames[] = $aDataRow[$sGroupName];
                    } else {
                        $aRealNames[] = 'Keine Zuweisung';
                    }
                }
            }
            $this->aBlocks[$sBlockName]->AddRow($aRealNames, $aDataRow);
        }
    }

    public function Render($sViewName, $sType = 'Core')
    {
        $oView = new TViewParser();
        /* @var $oView TViewParser */
        $oView->AddVar('oStats', $this);
        $oView->AddVar('aBlocks', $this->aBlocks);
        $oView->AddVar('sBlockName', $this->sBlockName);
        $aNameColumns = $this->GetNameColumns();
        $oView->AddVar('aNameColumns', $aNameColumns);
        $oView->AddVar('bShowDiffColumn', $this->bShowDiffColumn);

        $sSeparator = ';';
        $oView->AddVar('sSeparator', $sSeparator);

        $iMaxGroupCount = $this->GetMaxGroupColumnCount();
        $oView->AddVar('iMaxGroupCount', $iMaxGroupCount);

        return $oView->RenderObjectView($sViewName, self::VIEW_SUBTYPE, $sType);
    }

    protected function GetNameColumns()
    {
        $aNameColumns = [];
        foreach (array_keys($this->aBlocks) as $iBlockIndex) {
            $aTmpNames = $this->aBlocks[$iBlockIndex]->GetColumnNames();
            foreach ($aTmpNames as $sName) {
                if (!in_array($sName, $aNameColumns)) {
                    $aNameColumns[] = $sName;
                }
            }
        }
        reset($this->aBlocks);
        asort($aNameColumns);

        return $aNameColumns;
    }

    protected function GetMaxGroupColumnCount()
    {
        $iMaxCount = 0;
        foreach (array_keys($this->aBlocks) as $iBlockIndex) {
            $iMaxCount = max($this->aBlocks[$iBlockIndex]->GetSubGroupDepth() + 1, $iMaxCount);
        }
        reset($this->aBlocks);

        return $iMaxCount;
    }
}
