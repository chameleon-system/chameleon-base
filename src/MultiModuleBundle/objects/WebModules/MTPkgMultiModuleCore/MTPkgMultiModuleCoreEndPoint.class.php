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

class MTPkgMultiModuleCoreEndPoint extends TUserCustomModelBase
{
    /**
     * const to configure module set for static use.
     */
    const NAME_STATIC_MODULE_SET_NAME_PARAMETER = 'systemName';

    /**
     * holds the module config - will be loaded if needed.
     *
     * @var TdbPkgMultiModuleModuleConfig|null
     */
    protected $oConfig = null;

    /**
     * holds the array of TdbCmsTplModuleInstance objects
     * null by default.
     *
     * @var TdbCmsTplModuleInstance[]|null
     */
    protected $aModuleInstances = null;

    /**
     * holds the record list of all module configurations
     * null by default.
     *
     * @var TdbCmsTplModule|null
     */
    protected $oModuleList = null;

    /**
     * holds the array of TdbPkgMultiModuleSetItem objects
     * null by default.
     *
     * @var array|null
     */
    protected $aSetItems = null;

    /**
     * lookup array of all virtual spots
     * key = instance ID
     * value = spotName like: spotNameOfMultiModule__10.
     *
     * @var array
     */
    protected $aModuleInstanceSpots = array();

    /**
     * holds the array of all moduel objects.
     *
     * key = module instance id
     * value = ModuleObject
     *
     * @var array
     */
    protected $aModuleObjects = array();

    public function &Execute()
    {
        $this->data = parent::Execute();

        $this->LoadInstances();
        $this->data['aModuleInstanceSpots'] = $this->aModuleInstanceSpots;
        $this->data['sModuleContentId'] = $this->GetModuleContentIdentifier();
        $this->data['oConfig'] = $this->GetConfig();
        $this->data['aModuleInstances'] = $this->aModuleInstances;
        $this->data['aSetItems'] = $this->aSetItems;

        return $this->data;
    }

    /**
     * Defines interface to allow them to be called from web.
     */
    protected function DefineInterface()
    {
        parent::DefineInterface();
        $this->methodCallAllowed[] = 'RenderModuleAjax';
    }

    /**
     * call a method of this module. validates request.
     *
     * @param array  $aMethodParameter - parameters to pass to the method
     * @param string $sMethodName      - name of the function
     *
     * @return mixed
     */
    public function &_CallMethod($sMethodName, $aMethodParameter = array())
    {
        $sFunctionResult = null;
        $this->LoadInstances();
        if (true === \method_exists($this, $sMethodName)) {
            $sFunctionResult = parent::_CallMethod($sMethodName, $aMethodParameter);

            return $sFunctionResult;
        }

        if (true === \is_array($this->aModuleInstances)) {
            foreach ($this->aModuleInstances as $oModuleInstance) {
                $oModule = $this->GetModuleObjectByInstance($oModuleInstance);
                if (false === \method_exists($oModule, $sMethodName)) {
                    continue;
                }
                $oModule->instanceID = $oModuleInstance->id;
                $sFunctionResult = $oModule->_CallMethod($sMethodName, $aMethodParameter);
            }

            return $sFunctionResult;
        }

        return $sFunctionResult;
    }

    /**
     * Returns html tag identifier for to replace in ajax calls.
     *
     * @return string
     */
    public function GetModuleContentIdentifier()
    {
        return 'pkgMultiModule_Modul_'.$this->sModuleSpotName.$this->GetInstanceValueIdentifier();
    }

    /**
     * loads config if not already done and returns a copy of the class property $oConfig.
     *
     * @return TdbPkgMultiModuleModuleConfig|null
     */
    protected function GetConfig()
    {
        if (!$this->IsStatic()) {
            if (is_null($this->oConfig)) {
                $this->oConfig = TdbPkgMultiModuleModuleConfig::GetNewInstance();
                if (!$this->oConfig->LoadFromField('cms_tpl_module_instance_id', $this->instanceID)) {
                    $this->oConfig = null;
                }
            }
        }

        return $this->oConfig;
    }

    protected function LoadInstances()
    {
        if (!$this->IsStatic() || ($this->IsStatic() && false === $this->getRequestInfoService()->isCmsTemplateEngineEditMode())) {
            if (is_null($this->aModuleInstances)) {
                $oModuleList = $this->GetModuleList();
                if (is_object($oModuleList)) {
                    /** @var $oModuleItem TdbPkgMultiModuleSetItem */
                    $i = 0;
                    while ($oModuleItem = $oModuleList->Next()) {
                        ++$i;
                        /** @var $oModuleInstance TdbCmsTplModuleInstance */
                        $oModuleInstance = $oModuleItem->GetFieldCmsTplModuleInstance();
                        if (is_object($oModuleInstance)) {
                            if (!is_array($this->aModuleInstances)) {
                                $this->aModuleInstances = array();
                            }

                            /**
                             * overwrite the view configured by the instance,
                             * with the one set by the multi module item.
                             */
                            $oModuleInstance->fieldTemplate = $oModuleItem->sqlData['cms_tpl_module_instance_id_view'];

                            $this->aModuleInstances[] = $oModuleInstance;
                            $this->aModuleInstanceSpots[$oModuleInstance->id] = $this->sModuleSpotName.'__'.$i;

                            if (!is_array($this->aSetItems)) {
                                $this->aSetItems = array();
                            }
                            $this->aSetItems[$oModuleInstance->id] = $oModuleItem;

                            $this->PostAssertInstanceHook($oModuleItem, $oModuleInstance);
                        }
                    }
                }
            }
        }
    }

    protected function GetModuleList()
    {
        $oModuleList = null;
        if (is_null($this->oModuleList)) {
            $oModuleSet = $this->GetMultiModuleSet();
            if (!is_null($oModuleSet)) {
                /** @var $oModuleList TdbPkgMultiModuleSetItemList */
                $oModuleList = $oModuleSet->GetFieldPkgMultiModuleSetItemList();
                $oModuleList->ChangeOrderBy(array('sort_order' => 'ASC'));
            }
            $this->oModuleList = $oModuleList;
        }

        return $oModuleList;
    }

    /**
     * Returns the multi module set form module configuration or from configured module set name (static mode).
     *
     * @return TdbPkgMultiModuleSet
     */
    protected function GetMultiModuleSet()
    {
        $oModuleSet = null;
        if ($this->IsStatic()) {
            $sModuleSetName = $this->GetValueFromStaticParameters(MTPkgMultiModuleCore::NAME_STATIC_MODULE_SET_NAME_PARAMETER);
            if (!empty($sModuleSetName)) {
                $oModuleSet = TdbPkgMultiModuleSet::GetNewInstance();
                if (!$oModuleSet->LoadFromField('name', $sModuleSetName)) {
                    $oModuleSet = null;
                }
            }
        } else {
            $oConfig = $this->GetConfig();
            if (is_object($oConfig)) {
                $oModuleSet = $oConfig->GetFieldPkgMultiModuleSet();
            }
        }

        return $oModuleSet;
    }

    /**
     * Renders module content for an instance via ajax call.
     *
     * @return array
     */
    protected function RenderModuleAjax()
    {
        $aRenderedModuleData = array();
        $oGlobal = TGlobal::instance();
        if ($oGlobal->UserDataExists('sShowModuleInstanceId')) {
            $sModuleInstanceToShow = $oGlobal->GetUserData('sShowModuleInstanceId');
            $this->LoadInstances();
            if (is_array($this->aModuleInstances)) {
                foreach ($this->aModuleInstances as $oModuleInstance) {
                    if ($oModuleInstance->id == $sModuleInstanceToShow) {
                        $aRenderedModuleData['html'] = $oModuleInstance->Render(array(), $this->aModuleInstanceSpots[$oModuleInstance->id], $oModuleInstance->fieldTemplate);
                        $aRenderedModuleData['sModuleContentIdentifier'] = $this->GetModuleContentIdentifier();
                    }
                }
            }
        }

        return $aRenderedModuleData;
    }

    /**
     * hook is executed every time after a module instance was set into the array $aModuleInstances.
     *
     * @param TdbPkgMultiModuleSetItem $oSetItem
     * @param TdbCmsTplModuleInstance  $oModuleInstance
     */
    protected function PostAssertInstanceHook($oSetItem, $oModuleInstance)
    {
    }

    public function GetHtmlFooterIncludes()
    {
        $aIncludes = parent::GetHtmlFooterIncludes();

        $this->LoadInstances();
        if (is_array($this->aModuleInstances)) {
            /** @var $oModuleInstance TdbCmsTplModuleInstance */
            foreach ($this->aModuleInstances as $oModuleInstance) {
                $oModule = $this->GetModuleObjectByInstance($oModuleInstance);
                $aModuleIncludes = $oModule->GetHtmlFooterIncludes();
                if (is_array($aModuleIncludes)) {
                    $aIncludes = array_merge($aIncludes, $aModuleIncludes);
                }
            }
        }

        return $aIncludes;
    }

    public function GetHtmlHeadIncludes()
    {
        $aIncludes = parent::GetHtmlHeadIncludes();

        $this->LoadInstances();
        if (is_array($this->aModuleInstances)) {
            /** @var $oModuleInstance TdbCmsTplModuleInstance */
            foreach ($this->aModuleInstances as $oModuleInstance) {
                $oModule = $this->GetModuleObjectByInstance($oModuleInstance);
                $aModuleIncludes = $oModule->GetHtmlHeadIncludes();
                $aIncludes = array_merge($aIncludes, $aModuleIncludes);
            }
        }
        $aIncludes[] = '<script src="'.TGlobal::GetStaticURL('/chameleon/blackbox/pkgMultiModule/pkgMultiModule.js').'" type="text/javascript"></script>';
        $aIncludes = array_merge($aIncludes, $this->getResourcesForSnippetPackage('common/navigation'));
        $aIncludes = array_merge($aIncludes, $this->getResourcesForSnippetPackage('common/navigation/tabs'));

        return $aIncludes;
    }

    /**
     * injects virtual module spots with modules in $oModuleLoader->modules.
     *
     * @param TUserModuleLoader $oModuleLoader
     */
    public function InjectVirtualModuleSpots(&$oModuleLoader)
    {
        if (!$this->IsStatic() || ($this->IsStatic() && false === $this->getRequestInfoService()->isCmsTemplateEngineEditMode())) {
            $this->LoadInstances();

            if (is_array($this->aModuleInstances)) {
                /** @var $oModuleInstance TdbCmsTplModuleInstance */
                foreach (array_keys($this->aModuleInstances) as $index) {
                    if (is_object($this->aModuleInstances[$index])) {
                        $oModule = $this->GetModuleObjectByInstance($this->aModuleInstances[$index]);
                        $oModule->instanceID = $this->aModuleInstances[$index]->id;
                        $oModule->setController($this->controller);
                        $oModuleLoader->modules[$this->aModuleInstanceSpots[$this->aModuleInstances[$index]->id]] = $oModule;
                    }
                }
                reset($this->aModuleInstances);
            }
        }
    }

    protected function GetModuleObjectByInstance(TdbCmsTplModuleInstance $oModuleInstance)
    {
        $oModule = null;
        if (!is_array($this->aModuleObjects)) {
            $this->aModuleObjects = array();
        }
        if (array_key_exists($oModuleInstance->id, $this->aModuleObjects)) {
            $oModule = $this->aModuleObjects[$oModuleInstance->id];
        } else {
            $dbAccessModule = \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.database_access_layer_cms_tpl_module');
            $oDbModule = $dbAccessModule->loadFromId($oModuleInstance->fieldCmsTplModuleId);
            $oModule = TTools::GetModuleObject($oDbModule->fieldClassname, $oModuleInstance->fieldTemplate, array(), $this->aModuleInstanceSpots[$oModuleInstance->id]);
            $this->aModuleObjects[$oModuleInstance->id] = $oModule;
        }

        return $oModule;
    }

    /**
     * Checks if module is used static.
     *
     * @return bool
     */
    protected function IsStatic()
    {
        $bIsStatic = is_null($this->instanceID);
        if ($bIsStatic && is_array($this->aModuleConfig) && isset($this->aModuleConfig['static']) && $this->aModuleConfig['static'] && isset($this->aModuleConfig['systemName'])) {
            $bIsStatic = true;
        }

        return $bIsStatic;
    }

    /**
     * Returns the name of the module configured in static module parameters.
     *
     * @return string
     */
    protected function GetValueFromStaticParameters($sFieldName)
    {
        $sModuleSetName = '';
        if (is_array($this->aModuleConfig) && isset($this->aModuleConfig[$sFieldName])) {
            $sModuleSetName = $this->aModuleConfig[$sFieldName];
        }

        return $sModuleSetName;
    }

    protected function GetInstanceValueIdentifier()
    {
        $sInstanceValueIdentifier = $this->instanceID;
        if ($this->IsStatic()) {
            $sInstanceValueIdentifier = $this->GetValueFromStaticParameters('name');
        }

        return $sInstanceValueIdentifier;
    }

    /**
     * if this function returns true, then the result of the execute function will be cached.
     *
     * @return bool
     */
    public function _AllowCache()
    {
        // the sub modules will be cached...
        return false;
    }

    private function getRequestInfoService(): RequestInfoServiceInterface
    {
        return ServiceLocator::get('chameleon_system_core.request_info_service');
    }
}
