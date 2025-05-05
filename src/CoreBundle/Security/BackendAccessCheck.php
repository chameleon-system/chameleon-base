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

use ChameleonSystem\SecurityBundle\Voter\CmsUserRoleConstants;
use Scheb\TwoFactorBundle\Model\Google\TwoFactorInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;

class BackendAccessCheck
{
    private RouterInterface $router;
    private \ICmsCoreRedirect $redirect;
    private RequestStack $requestStack;
    /**
     * @var array<string, string[]>
     */
    private array $ipRestrictedPageDefs = [];

    public function __construct(
        RouterInterface $router,
        \ICmsCoreRedirect $redirect,
        RequestStack $requestStack,
        readonly private Security $security,
        readonly private bool $twoFactorEnabled = false
    ) {
        $this->router = $router;
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
            return;
        }

        if (!$request->attributes->has('pagedef')) {
            return;
        }
        $pagedef = $request->attributes->get('pagedef');
        if ($this->pagedefIsAllowed($pagedef, $request->getClientIp())) {
            return;
        }

        // Redirect to login page if user is not in session.
        $this->checkLogin();
    }

    /**
     * @return void
     */
    protected function checkLogin()
    {
        if (true === $this->twoFactorEnabled) {
            if (true === $this->checkTwoFactorIsSetupCorrectly()) {
                return;
            }
        } else {
            if (true === $this->security->isGranted(CmsUserRoleConstants::CMS_USER)) {
                return;
            }
        }

        $this->checkLoginOnAjax();
        $this->redirectToLogout();
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
        if (is_array($aModuleFNC) && array_key_exists('contentmodule', $aModuleFNC)
            && 'ExecuteAjaxCall' === $aModuleFNC['contentmodule']) {
            $aJson = ['loggedoutajax', $this->router->generate('app_logout')];
            echo json_encode($aJson);
            exit;
        }
    }

    private function pagedefIsAllowed(string $pagedef, string $clientIp): bool
    {
        if (array_key_exists($pagedef, $this->ipRestrictedPageDefs)) {
            $allowedIps = $this->ipRestrictedPageDefs[$pagedef];

            return 0 === count($allowedIps) || in_array($clientIp, $allowedIps);
        }

        return false;
    }

    private function getCurrentParametersAsUrl(): string
    {
        return \urlencode(\http_build_query($this->requestStack->getCurrentRequest()->query->all()));
    }

    /* If 2factor authorization isn't correctly setup the user is not allowed to login
     * this is a fix because, during the setup process of 2fa the user is "logged in" as
     * he logged in with username + password and 2fa is not setup, but once he manipulates
     * the url to /cms, the user would be able to access the backend
     */
    private function checkTwoFactorIsSetupCorrectly(): bool
    {
        $user = $this->security->getUser();

        if (
            true === ($user instanceof TwoFactorInterface)
            && true === $user->isGoogleAuthenticatorEnabled()
            && '' !== $user->getGoogleAuthenticatorSecret()
        ) {
            return true;
        }

        return false;
    }

    private function redirectToLogout(): void
    {
        $logout = $this->router->generate('app_logout');
        $this->redirect->redirect($logout);
    }
}
