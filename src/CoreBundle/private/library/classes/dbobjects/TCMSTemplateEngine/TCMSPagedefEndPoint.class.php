<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\Service\RequestInfoServiceInterface;
use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\CoreBundle\Util\InputFilterUtilInterface;

class TCMSPagedefEndPoint extends TCMSPage
{
    /**
     * name of the Template (shown to the user).
     *
     * @var string
     */
    public $templateName;

    /**
     * a short text describing the layout to the user.
     *
     * @var string
     */
    public $templateDescription;

    /**
     * name of the layout to use (comes from the master pagedef).
     * example: mylayout (not mylayout.layout.php).
     *
     * @var string
     */
    public $layoutTemplate;

    /**
     * array of all dynamic modules.
     *
     * format:
     * 'spotname'=>oSpot
     *
     * @var TCMSMasterPagedefSpot[]
     */
    public $aModuleList;

    /**
     * array of all dynamic modules.
     * will be set by SetTemplate and holds the modules of the old template
     * and aModuleList holds the NEW module list.
     *
     * format:
     * 'spotname'=>oSpot
     *
     * @var TCMSMasterPagedefSpot[]
     */
    protected $aOldDynamicModules;

    /**
     * list of the static modules - they always come from the master pagedef
     * 'spotname'=>oSpot.
     *
     * @var array
     */
    public $aStaticModuleList;

    /**
     * id of the master pagedef.
     *
     * @var string
     */
    public $iMasterPageDefId;

    /**
     * the masterpagedef object.
     *
     * @var TCMSMasterPagedef
     */
    protected $oMasterPagedef;

    /**
     * if object is found in cache, then we return it from cache, else we create
     * a new instance, save it to cache, and return it.
     *
     * @param int $iPageId
     *
     * @return TCMSPagedef
     */
    public static function GetCachedInstance($iPageId)
    {
        static $aPageCache = array();
        if (!array_key_exists($iPageId, $aPageCache)) {
            $aPageCache[$iPageId] = null;
            $oTCMSPagedef = new TCMSPagedef();
            $oTCMSPagedef->Load($iPageId);
            $aPageCache[$iPageId] = $oTCMSPagedef;
        }

        return $aPageCache[$iPageId];
    }

    /**
     * {@inheritdoc}
     */
    protected function PostLoadHook()
    {
        parent::PostLoadHook();
        if (array_key_exists('cms_master_pagedef_id', $this->sqlData) && !empty($this->sqlData['cms_master_pagedef_id'])) {
            $this->iMasterPageDefId = $this->sqlData['cms_master_pagedef_id'];
        } else {
            $this->iMasterPageDefId = null;
        }

        $this->LoadPageDefVars(); // load all pagevars (including master pagedata)
        $this->removeEmptyDynamicSpots();
    }

    /**
     * create a new CUSTOMER pagedef (save is also executed).
     *
     * @param string $masterPagedefId - if of the layout definition
     */
    public function ChangeMasterPagedef($masterPagedefId)
    {
        // backup existing modules...
        $aOldDynamicModules = $this->aModuleList;
        $this->aOldDynamicModules = $aOldDynamicModules;

        $this->iMasterPageDefId = $masterPagedefId;
        $this->LoadPageDefVars();
        $this->removeEmptyDynamicSpots();

        // if we have existing dynamic modules, then we should update them
        if (is_array($aOldDynamicModules)) {
            foreach ($aOldDynamicModules as $sSpotName => $spot) {
                $module = $spot->GetParameter('model');
                $view = $spot->GetParameter('view');
                $instanceID = $spot->GetParameter('instanceID');
                $this->UpdateModule($sSpotName, $module, $view, $instanceID);
            }
        }
        $this->Save();
    }

    /**
     * updates a module within the pagedef. Make sure to call save after calling
     * this function if you want to commit the changes to the page definition.
     *
     * @param string $spotName   - name of the spot in the module list
     * @param string $model      - the class to be used as the module
     * @param string $view       - name of the view to use
     * @param int    $instanceID - module instance id (optional)
     */
    public function UpdateModule($spotName, $model, $view, $instanceID = null)
    {
        if (false === array_key_exists($spotName, $this->aModuleList)) {
            return;
        }
        $module = $this->aModuleList[$spotName];
        $module->AddParameter('model', $model);
        $module->AddParameter('view', $view);
        $module->AddParameter('instanceID', $instanceID);
    }

    /**
     * commit the current pagedef state to the pagedef record.
     */
    public function Save()
    {
        $oTableEditor = new TCMSTableEditorManager();

        if ($this->iMasterPageDefId != $this->sqlData['cms_master_pagedef_id']) { // only save the pagedef if it changed, to prevent a workflow log
            $iTableID = TTools::GetCMSTableId('cms_tpl_page');
            $oTableEditor->Init($iTableID, $this->id);
            $oTableEditor->SaveField('cms_master_pagedef_id', $this->iMasterPageDefId);
        }

        // add new pagevars
        if (count($this->aModuleList) > 0) {
            reset($this->aModuleList);

            $iTableID = TTools::GetCMSTableId('cms_tpl_page_cms_master_pagedef_spot');
            $moduleFnc = $this->getInputFilterUtil()->getFilteredInput('module_fnc', array());

            ksort($this->aModuleList);
            $databaseConnection = $this->getDatabaseConnection();
            foreach ($this->aModuleList as $sSpotName => $spot) {
                if (true === $spot->bIsStatic) {
                    continue;
                }
                $model = $spot->GetParameter('model');

                $isRealModuleSet = ('MTEmpty' !== $model);
                $isClearInstanceCall = isset($moduleFnc[$sSpotName]) && ('ClearInstance' === $moduleFnc[$sSpotName]);

                // save changes only on real modules not MTEmpty modules or if the spot was cleared
                if (false === $isRealModuleSet && false === $isClearInstanceCall) {
                    continue;
                }

                $instanceId = $spot->GetParameter('instanceID');
                if (null === $instanceId) {
                    $instanceId = '';
                }

                $aPostData = array(
                    'cms_tpl_page_id' => $this->id,
                    'cms_master_pagedef_spot_id' => $spot->id,
                    'cms_tpl_module_instance_id' => $instanceId,
                    'view' => $spot->GetParameter('view'),
                    'model' => $model,
                );

                // check if pagedef spot exists in source template configuration and update it
                if (null === $this->aOldDynamicModules || false === isset($this->aOldDynamicModules[$sSpotName])) {
                    $sSpotID = $spot->id;
                } else {
                    $sSpotID = $this->aOldDynamicModules[$sSpotName]->id;
                }

                $query = 'SELECT `id`
                          FROM `cms_tpl_page_cms_master_pagedef_spot`
                          WHERE `cms_tpl_page_id` = :id
                          AND `cms_master_pagedef_spot_id` = :spotId';

                $recordID = $databaseConnection->fetchColumn($query, array(
                    'id' => $this->id,
                    'spotId' => $sSpotID,
                ));
                if (false !== $recordID) { // instance exists... force an update
                    $aPostData['id'] = $recordID;
                }

                // save/update the module data in page spot
                $oTableEditor->Init($iTableID, $recordID);
                $oTableEditor->AllowEditByAll(true);
                $oTableEditor->ForceHiddenFieldWriteOnSave(true);
                $oTableEditor->Save($aPostData);
            }
        }
        TCacheManager::PerformeTableChange('cms_tpl_page_cms_master_pagedef_spot');
        TCacheManager::PerformeTableChange('cms_tpl_page', $this->id);
    }

    /**
     * returns static and dynamic modules merged as one array.
     *
     * @return array - key = spotname, value = TCMSMasterPagedefSpot
     */
    public function GetModuleList()
    {
        $moduleList = $this->aModuleList;
        if (is_array($this->aStaticModuleList)) {
            $moduleList = array_merge($moduleList, $this->aStaticModuleList);
        }

        // now convert list to use the old spotname=>array('param'=>'val',...) format
        $aModuleData = array();
        foreach ($moduleList as $sSpotName => $spot) {
            $aModuleData[$sSpotName] = $spot->GetParameters();
        }

        return $aModuleData;
    }

    /**
     * load the pagevars specific to this page... only runs if a masterpagedef is defined.
     *
     * @return bool
     */
    protected function LoadPageDefVars()
    {
        if (null === $this->iMasterPageDefId) {
            return false;
        }
        $this->LoadMasterPageDefVars();
        // now update dynamic module vars (ie instance id)
        $query = "SELECT `cms_tpl_page_cms_master_pagedef_spot`.*,
                         `cms_master_pagedef_spot`.`name` AS spotname
                  FROM `cms_tpl_page_cms_master_pagedef_spot`
                  INNER JOIN `cms_master_pagedef_spot` ON `cms_tpl_page_cms_master_pagedef_spot`.`cms_master_pagedef_spot_id` = `cms_master_pagedef_spot`.`id`
                  INNER JOIN `cms_master_pagedef` ON `cms_master_pagedef`.`id` = `cms_master_pagedef_spot`.`cms_master_pagedef_id`
                  WHERE `cms_tpl_page_cms_master_pagedef_spot`.`cms_tpl_page_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->id)."'
                  AND `cms_master_pagedef`.`id` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->iMasterPageDefId)."'
                  AND `cms_tpl_page_cms_master_pagedef_spot`.`cms_tpl_module_instance_id` != ''
                  AND `cms_tpl_page_cms_master_pagedef_spot`.`cms_tpl_module_instance_id` != '0'
             ";
        $oCmsTplPageCmsMasterPagedefSpotList = TdbCmsTplPageCmsMasterPagedefSpotList::GetList($query);
        while ($oCmsTplPageCmsMasterPagedefSpot = $oCmsTplPageCmsMasterPagedefSpotList->Next()) {
            $this->UpdateModule($oCmsTplPageCmsMasterPagedefSpot->sqlData['spotname'], $oCmsTplPageCmsMasterPagedefSpot->fieldModel, $oCmsTplPageCmsMasterPagedefSpot->fieldView, $oCmsTplPageCmsMasterPagedefSpot->fieldCmsTplModuleInstanceId);
        }

        return true;
    }

    /**
     * load the pagedef data from the master pagedef.
     */
    public function LoadMasterPageDefVars()
    {
        $this->oMasterPagedef = TdbCmsMasterPagedef::GetNewInstance();
        if (false === $this->oMasterPagedef->Load($this->iMasterPageDefId)) {
            return;
        }
        $this->templateName = $this->oMasterPagedef->sqlData['name'];
        $this->templateDescription = $this->oMasterPagedef->sqlData['description'];
        $this->layoutTemplate = $this->oMasterPagedef->sLayout;
        $this->oMasterPagedef->SetPageId($this->id);
        $this->aStaticModuleList = $this->oMasterPagedef->GetStaticSpots();
        $this->aModuleList = $this->oMasterPagedef->GetDynamicSpots();
    }

    /**
     * returns the layout filename without extension
     * example: "mylayout" (not "mylayout.layout.php").
     *
     * @return string
     */
    public function GetLayoutFile()
    {
        $this->LoadMasterPageDefVars();

        return $this->oMasterPagedef->sLayout;
    }

    protected function removeEmptyDynamicSpots()
    {
        // drop empty spots if we are in frontend mode
        $isTemplateEnginePagedef = ('templateengine' === $this->getInputFilterUtil()->getFilteredInput('pagedef', '')); //e.g. changing layouts

        if (true === \is_array($this->aModuleList) && false === $isTemplateEnginePagedef && false === $this->getRequestInfoService()->isCmsTemplateEngineEditMode()) {
            reset($this->aModuleList);
            foreach ($this->aModuleList as $spotName => $spot) {
                if ('MTEmpty' === $spot->sModel) {
                    unset($this->aModuleList[$spotName]);
                }
            }
        }
    }

    /**
     * @return InputFilterUtilInterface
     */
    private function getInputFilterUtil()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.util.input_filter');
    }

    private function getRequestInfoService(): RequestInfoServiceInterface
    {
        return ServiceLocator::get('chameleon_system_core.request_info_service');
    }
}
