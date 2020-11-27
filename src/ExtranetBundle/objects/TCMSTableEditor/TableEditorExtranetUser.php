<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\i18n\TranslationConstants;
use ChameleonSystem\CoreBundle\Routing\PortalAndLanguageAwareRouterInterface;
use ChameleonSystem\CoreBundle\Service\PageServiceInterface;
use ChameleonSystem\CoreBundle\Util\InputFilterUtilInterface;
use ChameleonSystem\CoreBundle\Util\UrlUtil;
use ChameleonSystem\ExtranetBundle\Interfaces\ExtranetUserProviderInterface;
use Symfony\Component\Translation\TranslatorInterface;
use ChameleonSystem\ExtranetBundle\LoginByTransferToken\TransferTokenServiceInterface;
use ChameleonSystem\ExtranetBundle\LoginByTransferToken\LoginController;
use ChameleonSystem\ExtranetBundle\LoginByTransferToken\RouteGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class TableEditorExtranetUser extends TCMSTableEditor
{
    /**
     * {@inheritdoc}
     */
    public function DefineInterface()
    {
        parent::DefineInterface();

        $this->methodCallAllowed[] = 'LoginAsExtranetUser';
    }

    /**
     * {@inheritdoc}
     */
    protected function GetCustomMenuItems()
    {
        parent::GetCustomMenuItems();

        $menuItem = $this->loginAsExtranetUserButton();
        if (null !== $menuItem) {
            $this->oMenuItems->AddItem($menuItem);
        }
    }

    private function loginAsExtranetUserButton(): ?TCMSTableEditorMenuItem
    {
        if (false === $this->isBackendUserLoggedInWithPermission('allow-login-as-extranet-user')) {
            return null;
        }

        $menuItem = new TCMSTableEditorMenuItem();
        $menuItem->sItemKey = 'ExtranetUserLogin';
        $menuItem->setTitle($this->getTranslator()->trans(
            'chameleon_system_extranet.action.login_as_extranet_user',
            [],
            TranslationConstants::DOMAIN_BACKEND
        ));
        $menuItem->sIcon = 'fas fa-user-check';

        $executingModulePointer = $this->getGlobal()->GetExecutingModulePointer();
        if (null === $executingModulePointer) {
            return null;
        }

        $pagedef = $this->getInputFilterUtil()->getFilteredInput('pagedef');
        $urlData = [
            'module_fnc' => [$executingModulePointer->sModuleSpotName => 'LoginAsExtranetUser'],
            '_noModuleFunction' => 'true',
            'pagedef' => $pagedef,
            'id' => $this->sId,
            'tableid' => $this->oTableConf->id,
        ];
        $url = PATH_CMS_CONTROLLER.$this->getUrlUtil()->getArrayAsUrl($urlData, '?', '&');
        $menuItem->sOnClick = sprintf("window.location.href = '%s'; return false;", $url);

        return $menuItem;
    }

    /**
     * Login as the currently selected extranet user (permissions needed).
     * This process works by creating a temporary token to verify the login and
     * redirecting the user to a route on the domain of the corresponding site
     * which uses said token to log the user in.
     *
     * @see LoginController::loginAction()
     */
    public function LoginAsExtranetUser(): void
    {
        if (false === $this->isBackendUserLoggedInWithPermission('allow-login-as-extranet-user')) {
            return;
        }

        $userId = $this->getInputFilterUtil()->getFilteredGetInput('id');
        if (null === $userId) {
            return;
        }

        $extranetUser = $this->loadExtranetUser($userId);
        $portal = $this->portalForExtranetUser($extranetUser);
        $this->redirectUserToTokenLoginOnPortal($userId, $portal);
    }

    private function isBackendUserLoggedInWithPermission(string $permission): bool
    {
        $cmsUser = TCMSUser::GetActiveUser();

        return $cmsUser && $cmsUser->oAccessManager && $cmsUser->oAccessManager->PermitFunction($permission);
    }

    private function loadExtranetUser(string $userId): TdbDataExtranetUser
    {
        $extranetUserProvider = $this->getExtranetUserProvider();
        $extranetUserProvider->reset();

        /** @var TdbDataExtranetUser $extranetUser */
        $extranetUser = $extranetUserProvider->getActiveUser();
        $extranetUser->Load($userId);

        return $extranetUser;
    }

    private function portalForExtranetUser(TdbDataExtranetUser $extranetUser): TdbCmsPortal
    {
        if (empty($extranetUser->fieldCmsPortalId)) {
            $portalList = TdbCmsPortalList::GetList();
            $portalList->GoToStart();
            return $portalList->Current();
        }

        return TdbCmsPortal::GetNewInstance($extranetUser->fieldCmsPortalId);
    }

    private function redirectUserToTokenLoginOnPortal(string $userId, TdbCmsPortal $portal): void
    {
        $token = $this->transferTokenService()->createTransferTokenForUser($userId, 120);
        $url = $this->router()->generateWithPrefixes(
            RouteGenerator::ROUTE_NAME,
            [ 'token' => $token ],
            $portal,
            null,
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $this->getRedirect()->redirect($url);
    }

    /**
     * @return TranslatorInterface
     */
    private function getTranslator()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('translator');
    }

    /**
     * @return InputFilterUtilInterface
     */
    private function getInputFilterUtil()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.util.input_filter');
    }

    /**
     * @return TGlobal
     */
    private function getGlobal()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.global');
    }

    /**
     * @return UrlUtil
     */
    private function getUrlUtil()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.util.url');
    }

    /**
     * @return ExtranetUserProviderInterface
     */
    private function getExtranetUserProvider()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_extranet.extranet_user_provider');
    }

    /**
     * @return ICmsCoreRedirect
     */
    private function getRedirect()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.redirect');
    }

    private function transferTokenService(): TransferTokenServiceInterface
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_extranet.login_by_transfer_token.transfer_token_service');
    }

    private function router(): PortalAndLanguageAwareRouterInterface
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.router.chameleon_frontend');
    }
}
