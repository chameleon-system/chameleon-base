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
 * all user modules need to be derived from this class, or one of its children.
/**/
class TUserModelBaseCore extends TModelBase
{
    /**
     * holds the instance id of the module (points to a record in the table cms_tpl_module_instance).
     *
     * @var string|null
     */
    public $instanceID = null;

    /**
     * holds the language shortname of the template (example: de, en, ...).
     *
     * @var string|null
     */
    public $templateLanguage = null;

    public function __sleep()
    {
        $aSleep = parent::__sleep();
        $aSleep[] = 'instanceID';

        return $aSleep;
    }

    /**
     * @return array<string, mixed>
     */
    public function &Execute()
    {
        parent::Execute();
        $this->data['instanceID'] = $this->instanceID;
        $this->data['oActivePage'] = $this->getActivePageService()->getActivePage();
        $this->data['templateLanguage'] = $this->templateLanguage;

        return $this->data;
    }

    /**
     * return an assoc array of parameters that describe the state of the module.
     *
     * @return array
     */
    public function _GetCacheParameters()
    {
        $parameters = parent::_GetCacheParameters();
        $parameters['sInstanceId'] = $this->instanceID;

        return $parameters;
    }

    /**
     * Set arry of cache table infos or one cache table info to module intern cache table infos.
     *
     * @param array  $aCacheTableInfos
     * @param string $sTableName
     * @param string $sRecordId
     *
     * @deprecated
     */
    public function SetCacheTableInfos($aCacheTableInfos = array(), $sTableName = '', $sRecordId = '')
    {
    }

    /**
     * Use this method to retrieve resources from snippet packages.
     * This is necessary, should you use an instance of ViewRenderer in your module's old style view.php
     * Here you have to include the resources of the package in your HTMLHeadIncludes by hand.
     *
     * @param $sSnippetPath - the path to the snippet package
     *
     * @return array
     */
    protected function getResourcesForSnippetPackage($sSnippetPath)
    {
        return $this->getViewRendererSnippetDirectory()->getResourcesForSnippetPackage($sSnippetPath);
    }

    /**
     * @return ActivePageServiceInterface
     */
    private function getActivePageService()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.active_page_service');
    }

    /**
     * @return TPkgViewRendererSnippetDirectoryInterface
     */
    private function getViewRendererSnippetDirectory()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_view_renderer.snippet_directory');
    }
}
