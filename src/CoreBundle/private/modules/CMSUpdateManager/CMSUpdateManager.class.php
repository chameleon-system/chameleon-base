<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\CoreBundle\Util\InputFilterUtilInterface;
use ChameleonSystem\DatabaseMigration\DataModel\MigrationResult;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;

class CMSUpdateManager extends TModelBase
{
    public function Execute()
    {
        $this->data = parent::Execute();
        clearstatcache(true);

        return $this->data;
    }

    protected function DefineInterface()
    {
        parent::DefineInterface();
        $externalFunctions = [
            'RunUpdates',
            'runSingleUpdate',
            'ajaxProxyUpdateAllTables',
            'ajaxProxyUpdateVirtualNonDbClasses',
            'ajaxProxyClearCache',
        ];
        $this->methodCallAllowed = array_merge($this->methodCallAllowed, $externalFunctions);
    }

    /**
     * loads update manager and executes all available new updates.
     */
    public function RunUpdates()
    {
        define('CMSUpdateManagerRunning', true);
        $this->SetTemplate('CMSUpdateManager', 'runUpdate');
        $oUpdateManager = TCMSUpdateManager::GetInstance();
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
        $inputFilter = $this->getInputFilter();

        $fileName = $inputFilter->getFilteredInput('fileName');
        $bundleName = $inputFilter->getFilteredInput('bundleName');

        if (null === $fileName || null === $bundleName) {
            return '';
        }

        return TCMSUpdateManager::GetInstance()->runSingleUpdate($fileName, $bundleName);
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
        $cache = ServiceLocator::get('chameleon_system_core.cache');
        $cache->clearAll();
    }

    private function getAutoclassesCacheWarmer(): CacheWarmerInterface
    {
        return ServiceLocator::get('chameleon_system_autoclasses.cache_warmer');
    }

    private function getInputFilter(): InputFilterUtilInterface
    {
        return ServiceLocator::get('chameleon_system_core.util.input_filter');
    }
}
