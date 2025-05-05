<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TPkgCmsActionPluginManager
{
    /**
     * @var array<string, object>|null
     */
    private $aActionPluginList;

    /**
     * @var TCMSActivePage|null
     */
    private $oActivePage;

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
     */
    public function callAction($sPluginName, $sActionName, $aParameter)
    {
        $oPlugin = $this->getActionPlugin($sPluginName);

        if (false === method_exists($oPlugin, $sActionName) || false === is_callable([$oPlugin, $sActionName])) {
            throw new TPkgCmsActionPluginException_ActionNotPublic('action "'.$sActionName.'" does not exists', 0, E_USER_WARNING, __FILE__, __LINE__);
        }

        // make sure the method is public
        $oRefl = new ReflectionMethod($oPlugin, $sActionName);
        if (false === $oRefl->isPublic() || true === $oRefl->isStatic()) {
            throw new TPkgCmsActionPluginException_ActionNotFound('action "'.$sActionName.'" does not exists', 0, E_USER_WARNING, __FILE__, __LINE__);
        }

        call_user_func([$oPlugin, $sActionName], $aParameter);
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
        } else {
            $aActionPluginList = $this->getActionPluginList();

            return $aActionPluginList[$sPluginName];
        }
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

            $oPageDef = $this->oActivePage->GetFieldCmsMasterPagedef();
            $sList = '';
            if (property_exists($this->oActivePage->oActivePortal, 'fieldActionPluginList')) {
                /* @psalm-suppress UndefinedPropertyFetch - Property is checked above. */
                $sList .= trim($this->oActivePage->oActivePortal->fieldActionPluginList);
            }
            if (!empty($sList)) {
                $sList .= "\n";
            }

            if (property_exists($oPageDef, 'fieldActionPluginList')) {
                $sList .= trim($oPageDef->fieldActionPluginList);
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
                    if (2 == count($aParts)) {
                        $this->aActionPluginList[$aParts[0]] = new $aParts[1]();
                    }
                }
            }
        }

        return $this->aActionPluginList;
    }
}
