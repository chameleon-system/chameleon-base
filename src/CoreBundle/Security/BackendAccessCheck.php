<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Security;

use ICmsCoreRedirect;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;
use TGlobal;

class BackendAccessCheck
{
    /**
     * @var \ICmsCoreRedirect
     */
    private $redirect;
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var array<string, string[]>
     */
    private $ipRestrictedPageDefs = array();

    /**
     * @param ICmsCoreRedirect $redirect
     * @param RequestStack     $requestStack
     */
    public function __construct(ICmsCoreRedirect $redirect, RequestStack $requestStack, readonly private Security $security)
    {
        $this->redirect = $redirect;
        $this->requestStack = $requestStack;
    }

    /**
     * Unrestrict a pagedef to a list of client ips. If the array of ips is empty, the pagedef will be unrestricted for all requests.
     *
     * @param string $pagedef
     * @param string[] $ips
     *
     * @return void
     */
    public function unrestrictPagedef($pagedef, array $ips)
    {
        $this->ipRestrictedPageDefs[$pagedef] = $ips;
    }

    /**
     * @return void
     */
    public function assertAccess()
    {
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return ;
        }

        if (!$request->attributes->has('pagedef')) {
            return;
        }
        $pagedef = $request->attributes->get('pagedef');
        if ($this->pagedefIsAllowed($pagedef, $request->getClientIp())) {
            return;
        }

        // redirect to login page if user not in session
        $this->checkLogin();
    }

    /**
     * @return void
     */
    protected function checkLogin()
    {
        if (true === $this->security->isGranted('ROLE_CMS_USER')) {
            return;
        }
        $this->checkLoginOnAjax();
        $this->redirect->redirectToActivePage(array('pagedef' => 'login', 'module_fnc[contentmodule]' => 'Logout'));
    }

    /**
     * if we are on a ajax call return status and redirect url. So ajax can do a redirect to login page if user was logged out.
     *
     * @return void
     */
    protected function checkLoginOnAjax()
    {
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return;
        }
        $aModuleFNC = $request->get('module_fnc');
        if (is_array($aModuleFNC) && array_key_exists('contentmodule', $aModuleFNC) && 'ExecuteAjaxCall' === $aModuleFNC['contentmodule']) {
            $aParameters = array('pagedef' => 'login', 'module_fnc[contentmodule]' => 'Logout');
            $sLocation = PATH_CMS_CONTROLLER.'?' . http_build_query($aParameters);
            $aJson = array('logedoutajax', $sLocation);
            echo json_encode($aJson);
            exit();
        }
    }

    /**
     * @param string $pagedef
     * @param string $clientIp
     *
     * @return bool
     */
    private function pagedefIsAllowed($pagedef, $clientIp)
    {
        if (array_key_exists($pagedef, $this->ipRestrictedPageDefs)) {
            $allowedIps = $this->ipRestrictedPageDefs[$pagedef];

            return 0 === count($allowedIps) || in_array($clientIp, $allowedIps);
        }

        return false;
    }
}
