<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Controller;

use ChameleonSystem\CoreBundle\Security\BackendAccessCheck;

class ChameleonBackendController extends ChameleonController
{
    /**
     * @var BackendAccessCheck
     */
    private $backendAccessCheck;
    /**
     * @var string
     */
    private $homePagedef;

    /**
     * {@inheritdoc}
     */
    public function getResponse()
    {
        $request = $this->getRequest();
        $pagedef = $this->getInputFilterUtil()->getFilteredInput('pagedef', $this->homePagedef);
        $request->attributes->set('pagedef', $pagedef);

        $this->backendAccessCheck->assertAccess();

        return $this->GeneratePage($pagedef);
    }

    /**
     * check if ip of user is in ip white list of cms config
     * but only if a white list or the config constant x is set.
     *
     * The entry point into the controller. It will render a page given a
     * pagedefinition file. If no definition file is passed, it will use
     * "default" as the active definition file
     *
     * @param string $pagedef - The name of the page definition file to render.
     *                        Notice that only the name should be passed. The location of
     *                        the definition file is defined in the config.inc.php file loaded
     *                        in TGlobal (PATH_PAGE_DEFINITIONS)
     *
     * @return void
     */
    public function HandleRequest($pagedef)
    {
        $oCMSConfig = &\TdbCmsConfig::GetInstance();
        if (!$oCMSConfig) { // sometimes config comes corrupted from cache then reload config from db
            $oCMSConfig = &\TdbCmsConfig::GetInstance(true);
        }
        if ($oCMSConfig && $oCMSConfig->CurrentIpIsWhiteListed()) {
            $this->accessCheckHook();
        } else {
            // TODO
        }
    }

    /**
     * @param BackendAccessCheck $backendAccessCheck
     *
     * @return void
     */
    public function setBackendAccessCheck($backendAccessCheck)
    {
        $this->backendAccessCheck = $backendAccessCheck;
    }

    public function setHomePagedef(string $homePagedef): void
    {
        $this->homePagedef = $homePagedef;
    }
}
