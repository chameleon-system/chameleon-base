<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\DatabaseAccessLayer\DatabaseAccessLayerCmsTPlModule;
use ChameleonSystem\CoreBundle\ServiceLocator;

abstract class MTPkgViewRendererAbstractModuleMapper extends TUserCustomModelBase implements IViewMapper
{
    /**
     * {@inheritdoc}
     */
    public function GetRequirements(IMapperRequirementsRestricted $oRequirements): void
    {
        $oRequirements->NeedsSourceObject('instanceID', 'string', null);
        $oRequirements->NeedsSourceObject('aModuleConfig', 'array');
        $oRequirements->NeedsSourceObject('sModuleSpotName', 'string');
    }

    /**
     * @return void
     */
    final public function ClearCache()
    {
    }

    /**
     * {@inheritdoc}
     */
    final public function Execute()
    {
        return parent::Execute();
    }

    /**
     * {@inheritdoc}
     */
    final public function _GetCacheTableInfos()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function GetHtmlHeadIncludes()
    {
        $headIncludes = parent::GetHtmlHeadIncludes();
        $viewName = $this->aModuleConfig['view'];
        /** @var DatabaseAccessLayerCmsTPlModule $dbAccessLayer */
        $dbAccessLayer = ServiceLocator::get('chameleon_system_core.database_access_layer_cms_tpl_module');
        if (!isset($this->aModuleConfig['model'])) {
            throw new ErrorException(
                'unable to run GetHtmlHeadIncludes for '.get_class(
                    $this
                ).' in '.$this->sModuleSpotName.' because model parameter is missing in aModuleConfig: '.print_r(
                    $this->aModuleConfig,
                    true
                ), 0, E_USER_ERROR, __FILE__, __LINE__
            );
        }
        $module = $dbAccessLayer->loadFromClassOrServiceId($this->aModuleConfig['model']);
        if (null !== $module) {
            $viewMapperConfig = $module->getViewMapperConfig();
            $sSnippet = $viewMapperConfig->getSnippetForConfig($viewName);
            $sSnippetPath = dirname($sSnippet);
            $aAdditionalIncludes = $this->getResourcesForSnippetPackage($sSnippetPath);
            $headIncludes = array_merge($headIncludes, $aAdditionalIncludes);
        }

        return $headIncludes;
    }
}
