<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\DatabaseAccessLayer\DatabaseAccessLayerCmsMasterPagedefSpotAccess;
use ChameleonSystem\CoreBundle\DatabaseAccessLayer\DatabaseAccessLayerCmsMasterPagedefSpotParameter;

class TCMSMasterPagedefSpot extends TCMSRecord
{
    /**
     * @var array|null
     */
    protected $aParameter;
    /**
     * @var bool
     */
    public $bIsStatic = false;
    /**
     * @var string
     */
    public $sName = '';
    /**
     * @var string
     */
    public $sView = '';
    /**
     * @var string
     */
    public $sModel = '';

    /**
     * @param string $table
     * @param string|null $id
     * @param string|null $iLanguageId
     */
    public function __construct($table = 'cms_master_pagedef_spot', $id = null, $iLanguageId = null)
    {
        parent::__construct($table, $id, $iLanguageId);
    }

    /**
     * {@inheritdoc}
     */
    protected function PostLoadHook()
    {
        parent::PostLoadHook();
        $this->bIsStatic = '1' == $this->sqlData['static'];
        $this->sName = $this->sqlData['name'];
        $this->sView = $this->sqlData['view'];
        $this->sModel = $this->sqlData['model'];
        $this->GetParameters();
    }

    /**
     * get all parameters.
     *
     * @return array
     */
    public function GetParameters()
    {
        if (null !== $this->aParameter) {
            return $this->aParameter;
        }
        $this->aParameter = [];
        $this->aParameter['name'] = $this->sName;
        $this->aParameter['model'] = $this->sModel;
        $this->aParameter['view'] = $this->sView;
        $this->aParameter['static'] = $this->bIsStatic;

        $parameter = $this->getPagedefSpotParameterDataAccess()->getParameterForSpot($this->id);
        if (is_array($parameter)) {
            foreach ($parameter as $parameterRow) {
                $this->AddParameter($parameterRow['name'], $parameterRow['value']);
            }
        }

        if (true === $this->aParameter['static']) {
            return $this->aParameter;
        }

        $restrictions = $this->getPagedefSpotAccessDataAccess()->getAccessForSpot($this->id);
        if (false === is_array($restrictions)) {
            return $this->aParameter;
        }

        foreach ($restrictions as $restriction) {
            if (empty($restriction['model'])) {
                continue;
            }
            if (empty($restriction['views'])) {
                $aViews[] = 'standard';
            } else {
                $aViews = explode("\n", $restriction['views']);
            }
            $this->aParameter['permittedModules'][$restriction['model']] = $aViews;
        }

        return $this->aParameter;
    }

    /**
     * get a specific parameter. if not found, return null.
     *
     * @param string $sName
     *
     * @return string|null
     */
    public function GetParameter($sName)
    {
        return isset($this->aParameter[$sName]) ? $this->aParameter[$sName] : null;
    }

    /**
     * set (overwrite if exists) a parameter with name sName.
     *
     * @param string $sName
     * @param string $sValue
     */
    public function AddParameter($sName, $sValue)
    {
        if ('instanceID' === $sName && empty($sValue)) {
            $sValue = null;
        }
        $this->aParameter[$sName] = $sValue;
        if ('name' === $sName) {
            $this->sName = $sValue;
            $this->sqlData['name'] = $sValue;
        } elseif ('static' === $sName) {
            $this->bIsStatic = ('1' == $sValue) ? true : false;
            $this->sqlData['static'] = $sValue;
        } elseif ('view' === $sName) {
            $this->sView = $sValue;
            $this->sqlData['view'] = $sValue;
        } elseif ('model' === $sName) {
            $this->sModel = $sValue;
            $this->sqlData['model'] = $sValue;
        }
    }

    /**
     * returns if model and view are allowed for spot.
     *
     * @param string $sModel
     * @param string $sView
     *
     * @return bool
     */
    public function CheckAccess($sModel, $sView)
    {
        $quotedId = $this->getDatabaseConnection()->quote($this->id);
        $query = "SELECT `cms_master_pagedef_spot_access`.* 
                  FROM `cms_master_pagedef_spot_access` 
                  WHERE `cms_master_pagedef_spot_access`.`cms_master_pagedef_spot_id` = $quotedId";
        $oSpotRestrictions = TdbCmsMasterPagedefSpotAccessList::GetList($query);
        if (0 === $oSpotRestrictions->Length()) {
            return true;
        }
        $bIsAllowed = false;
        while ($oSpotRestriction = $oSpotRestrictions->Next()) {
            if ($oSpotRestriction->fieldModel !== $sModel) {
                continue;
            }
            if (empty($oSpotRestriction->fieldViews)) {
                $oSpotRestriction->fieldViews = 'standard';
            }
            $aAllowedViews = explode("\n", $oSpotRestriction->fieldViews);
            if (in_array($sView, $aAllowedViews)) {
                $bIsAllowed = true;
            }
        }

        return $bIsAllowed;
    }

    /**
     * @return DatabaseAccessLayerCmsMasterPagedefSpotParameter
     */
    private function getPagedefSpotParameterDataAccess()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.database_access_layer_cms_master_pagedef_spot_parameter');
    }

    /**
     * @return DatabaseAccessLayerCmsMasterPagedefSpotAccess
     */
    private function getPagedefSpotAccessDataAccess()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.database_access_layer_cms_master_pagedef_spot_access');
    }
}
