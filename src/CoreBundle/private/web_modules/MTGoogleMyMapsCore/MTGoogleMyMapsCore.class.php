<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\Service\LanguageServiceInterface;

/**
 * @deprecated since 6.2.0 - no longer used (uses an old Maps API which is no longer supported).
 */
class MTGoogleMyMapsCore extends TUserCustomModelBase
{
    protected $oTableRow;

    protected $bAllowHTMLDivWrapping = true;

    public function Init()
    {
        parent::Init();
        $this->LoadTableRow();
    }

    public function &Execute()
    {
        $this->data = parent::Execute();
        $this->data['instanceID'] = $this->instanceID;
        $this->GetMapParameters();

        return $this->data;
    }

    public function GetMapParameters()
    {
        $aURLParameters = array();
        if (!empty($this->oTableRow->sqlData['maps_url'])) {
            $aURLParameters = TTools::GetURLArguments($this->oTableRow->sqlData['maps_url']);

            // get latitude and longitude
            // &ll=28.479876,34.495493
            if (!empty($aURLParameters['ll'])) {
                $aCoordinates = explode(',', $aURLParameters['ll']);
                $this->data['latitude'] = $aCoordinates[0];
                $this->data['longitude'] = $aCoordinates[1];
            }

            if (!empty($aURLParameters['z'])) {
                $zoom = str_replace('_', '', $aURLParameters['z']);
                $this->data['zoomFactor'] = $zoom;
            }
        }

        if (!empty($this->oTableRow->sqlData['map_type'])) {
            $mapType = $this->oTableRow->sqlData['map_type'];
            if ('map' == $mapType) {
                $this->data['mapType'] = 'G_NORMAL_MAP';
            } elseif ('sat' == $mapType) {
                $this->data['mapType'] = 'G_SATELLITE_MAP';
            } elseif ('hybrid' == $mapType) {
                $this->data['mapType'] = 'G_HYBRID_TYPE';
            }
        }
    }

    protected function LoadTableRow()
    {
        $this->oTableRow = new TCMSRecord();
        $this->oTableRow->table = 'module_google_map';
        $this->oTableRow->SetLanguage($this->getLanguageService()->getActiveLanguageId());
        $this->oTableRow->LoadFromField('cms_tpl_module_instance_id', $this->instanceID);
        $this->data['oTableRow'] = &$this->oTableRow;
    }

    /**
     * if this function returns true, then the result of the execute function will be cached.
     *
     * @return bool
     */
    public function _AllowCache()
    {
        return true;
    }

    /**
     * if the content that is to be cached comes from the database (as ist most often the case)
     * then this function should return an array of assoc arrays that point to the
     * tables and records that are associated with the content. one table entry has
     * two fields:
     *   - table - the name of the table
     *   - id    - the record in question. if this is empty, then any record change in that
     *             table will result in a cache clear.
     *
     * @return array
     */
    public function _GetCacheTableInfos()
    {
        $tableInfo = array(array('table' => 'module_google_map', 'id' => $this->_oTableRow->id));

        return $tableInfo;
    }

    /**
     * @return LanguageServiceInterface
     */
    private function getLanguageService()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.language_service');
    }
}
