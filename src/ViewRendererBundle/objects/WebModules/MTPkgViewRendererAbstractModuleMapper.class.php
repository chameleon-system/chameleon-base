<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

abstract class MTPkgViewRendererAbstractModuleMapper extends TUserCustomModelBase implements IViewMapper
{
    /**
     * {@inheritdoc}
     */
    public function GetRequirements(IMapperRequirementsRestricted $oRequirements)
    {
        $oRequirements->NeedsSourceObject('instanceID', 'string', null);
        $oRequirements->NeedsSourceObject('aModuleConfig', 'array');
        $oRequirements->NeedsSourceObject('sModuleSpotName', 'string');
    }

    /**
     * {@inheritdoc}
     */
    final public function ClearCache(): void
    {
    }

    /**
     * {@inheritdoc}
     */
    final public function &Execute()
    {
        return parent::Execute();
    }

    /**
     * @param array  $aCacheTableInfos
     * @param string $sTableName
     * @param string $sRecordId
     *
     * @return array
     */
    final public function SetCacheTableInfos($aCacheTableInfos = array(), $sTableName = '', $sRecordId = '')
    {
        return array();
    }

    /**
     * {@inheritdoc}
     */
    final public function _GetCacheTableInfos()
    {
        return array();
    }

    /**
     * {@inheritdoc}
     */
    public function GetHtmlHeadIncludes()
    {
        $aHeadIncludes = parent::GetHtmlHeadIncludes();
        $sViewName = $this->aModuleConfig['view'];
        /** @var $dbAccessLayer ChameleonSystem\CoreBundle\DatabaseAccessLayer\DatabaseAccessLayerCmsTPlModule* */
        $dbAccessLayer = \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.database_access_layer_cms_tpl_module');
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
        $oModule = $dbAccessLayer->loadFromClassOrServiceId($this->aModuleConfig['model']);
        if (null !== $oModule) {
            $viewMapperConfig = $oModule->getViewMapperConfig();
            $sSnippet = $viewMapperConfig->getSnippetForConfig($sViewName);
            $sSnippetPath = dirname($sSnippet);
            $aAdditionalIncludes = $this->getResourcesForSnippetPackage($sSnippetPath);
            $aHeadIncludes = array_merge($aHeadIncludes, $aAdditionalIncludes);
        }

        return $aHeadIncludes;
    }
}
