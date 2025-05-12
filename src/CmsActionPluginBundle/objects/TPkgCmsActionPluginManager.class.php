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

class TPkgCmsActionPluginManager
{
    /**
     * @var array<string, object>|null
     */
    private ?array $aActionPluginList;

    private ?TCMSActivePage $oActivePage;

    public function __construct(TCMSActivePage $oActivePage)
    {
        $this->oActivePage = $oActivePage;
    }

    /**
     * @param string $sPluginName
     *
     * @return bool
     */
    public function actionPluginExists($sPluginName)
    {
        $aPluginList = $this->getActionPluginList();

        return isset($aPluginList[$sPluginName]);
    }

    /**
     * @param string $sPluginName - systemname of the plugin
     * @param string $sActionName - the method to call
     * @param array $aParameter - parameters to pass to the action
     *
     * @return void
     *
     * @throws TPkgCmsActionPluginException_ActionNotFound
     * @throws TPkgCmsActionPluginException_ActionNotPublic
     * @throws ReflectionException
     */
    public function callAction($sPluginName, $sActionName, $aParameter)
    {
        $plugin = $this->getActionPlugin($sPluginName);

        if (false === is_callable([$plugin, $sActionName]) || false === method_exists($plugin, $sActionName)) {
            throw new TPkgCmsActionPluginException_ActionNotPublic('action "'.$sActionName.'" does not exists', 0, E_USER_WARNING, __FILE__, __LINE__);
        }

        // make sure the method is public
        $reflection = new ReflectionMethod($plugin, $sActionName);
        if (false === $reflection->isPublic() || true === $reflection->isStatic()) {
            throw new TPkgCmsActionPluginException_ActionNotFound('action "'.$sActionName.'" does not exists', 0, E_USER_WARNING, __FILE__, __LINE__);
        }

        $plugin->$sActionName($aParameter);
    }

    /**
     * @param string $sPluginName
     *
     * @return AbstractPkgActionPlugin|null
     */
    protected function getActionPlugin($sPluginName)
    {
        if (false === $this->actionPluginExists($sPluginName)) {
            return null;
        }

        $aActionPluginList = $this->getActionPluginList();

        return $aActionPluginList[$sPluginName];
    }

    /**
     * @return object[]
     *
     * @psalm-return array<string, object>
     */
    protected function getActionPluginList()
    {
        if (null === $this->aActionPluginList) {
            $this->aActionPluginList = [];

            $pageDef = $this->oActivePage->GetFieldCmsMasterPagedef();
            $sList = '';

            $activePortal = $this->getActivePortal();

            if (null === $activePortal) {
                return [];
            }

            if (property_exists($activePortal, 'fieldActionPluginList')) {
                /* @psalm-suppress UndefinedPropertyFetch - Property is checked above. */
                $sList .= trim($activePortal->fieldActionPluginList);
            }
            if (!empty($sList)) {
                $sList .= "\n";
            }

            if (property_exists($pageDef, 'fieldActionPluginList')) {
                /* @psalm-suppress UndefinedPropertyFetch - Property is checked above. */
                $sList .= trim($pageDef->fieldActionPluginList);
            }

            $aList = explode("\n", $sList);
            foreach ($aList as $sLine) {
                $sLine = trim($sLine);
                $aParts = explode('=', $sLine);
                foreach ($aParts as $sKey => $sVal) {
                    $aParts[$sKey] = trim($sVal);
                    if (empty($aParts[$sKey])) {
                        unset($aParts[$sKey]);
                    }
                    if (2 === count($aParts)) {
                        $this->aActionPluginList[$aParts[0]] = new $aParts[1]();
                    }
                }
            }
        }

        return $this->aActionPluginList;
    }

    private function getActivePortal(): ?TCMSPortal
    {
        ServiceLocator::get('chameleon_system_core.portal_domain_service')->getActivePortal();
    }
}
