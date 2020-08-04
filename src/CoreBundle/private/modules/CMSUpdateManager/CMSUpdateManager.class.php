<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\AutoclassesBundle\CacheWarmer\AutoclassesCacheWarmer;
use ChameleonSystem\DatabaseMigration\DataModel\MigrationResult;
use esono\pkgCmsCache\CacheInterface;

class CMSUpdateManager extends TModelBase
{
    public function &Execute()
    {
        $this->data = parent::Execute();
        clearstatcache(true);

        return $this->data;
    }

    protected function DefineInterface()
    {
        parent::DefineInterface();
        $externalFunctions = array(
            'RunUpdates',
            'RunUpdateSingle',
            'runSingleUpdate',
            'ajaxProxyUpdateAllTables',
            'ajaxProxyUpdateVirtualNonDbClasses',
            'ajaxProxyClearCache',
        );
        $this->methodCallAllowed = array_merge($this->methodCallAllowed, $externalFunctions);
    }

    /**
     * loads update manager and executes all available new updates.
     */
    public function RunUpdates()
    {
        define('CMSUpdateManagerRunning', true);
        $this->SetTemplate('CMSUpdateManager', 'runUpdate');
        $oUpdateManager = &TCMSUpdateManager::GetInstance();
        $this->data['oUpdateManager'] = $oUpdateManager;
    }

    public function RunUpdateSingle()
    {
        $this->SetTemplate('CMSUpdateManager', 'runUpdateSingle');
        $oUpdateManager = &TCMSUpdateManager::GetInstance();
        $this->data['oUpdateManager'] = $oUpdateManager;
    }

    public function GetHtmlHeadIncludes()
    {
        $aIncludes = parent::GetHtmlHeadIncludes();
        $aIncludes[] = '<link href="'.TGlobal::GetPathTheme().'/css/modules/updateManager.css" rel="stylesheet" type="text/css" />';

        return $aIncludes;
    }

    public function GetHtmlFooterIncludes()
    {
        $includes = parent::GetHtmlFooterIncludes();
        $includes[] = '<script src="'.TGlobal::GetStaticURLToWebLib('/javascript/modules/updateManager/updateManager.js').'" type="text/javascript"></script>';

        return $includes;
    }

    /**
     * executes a single update via URL call
     * expects "filename" and "type".
     *
     * @return MigrationResult|string
     */
    public function runSingleUpdate()
    {
        $oGlobal = TGlobal::instance();

        if (false === $oGlobal->UserDataExists('fileName') || false === $oGlobal->UserDataExists('bundleName')) {
            return '';
        }

        $fileName = $oGlobal->GetUserData('fileName');
        $bundleName = $oGlobal->GetUserData('bundleName');

        $oUpdateManager = &TCMSUpdateManager::GetInstance();

        return $oUpdateManager->runSingleUpdate($fileName, $bundleName);
    }

    /*
     * proxy methods for post-update-executing via ajax
     * (register new ones in runUpdate.view.php)
     */

    public function ajaxProxyUpdateAllTables()
    {
        $this->getAutoclassesCacheWarmer()->updateAllTables();
    }

    public function ajaxProxyUpdateVirtualNonDbClasses()
    {
        TCMSLogChange::UpdateVirtualNonDbClasses();
    }

    public function ajaxProxyClearCache()
    {
        /** @var CacheInterface $cache */
        $cache = \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.cache');
        $cache->clearAll();
    }

    /**
     * @return AutoclassesCacheWarmer
     */
    private function getAutoclassesCacheWarmer()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_autoclasses.cache_warmer');
    }

    private function getViewRenderer()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_view_renderer.view_renderer');
    }
}
