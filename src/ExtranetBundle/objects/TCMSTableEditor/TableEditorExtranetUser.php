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
use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\CoreBundle\Util\InputFilterUtilInterface;
use ChameleonSystem\CoreBundle\Util\UrlUtil;
use ChameleonSystem\ExtranetBundle\LoginByToken\LoginByTokenController;
use ChameleonSystem\ExtranetBundle\LoginByToken\LoginTokenServiceInterface;
use ChameleonSystem\SecurityBundle\Service\SecurityHelperAccess;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class TableEditorExtranetUser extends TCMSTableEditor
{
    private const LOGIN_TOKEN_LIFETIME_SECONDS = 20;

    /**
     * {@inheritDoc}
     *
     * @return void
     */
    public function DefineInterface()
    {
        parent::DefineInterface();

        $this->methodCallAllowed[] = 'LoginAsExtranetUser';
    }

    /**
     * {@inheritDoc}
     *
     * @return void
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
        /** @var SecurityHelperAccess $securityHelper */
        $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);

        if (false === $securityHelper->isGranted('CMS_RIGHT_ALLOW-LOGIN-AS-EXTRANET-USER')) {
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

        if (false === $this->hasUserAValidPassword()) {
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

        if (false === $this->loginTokenService()->isReadyToEncodeTokens()) {
            $menuItem->sCSSClass = 'disabled';
        } else {
            $url = PATH_CMS_CONTROLLER.$this->getUrlUtil()->getArrayAsUrl($urlData, '?', '&');
            $menuItem->sOnClick = sprintf("window.open('%s'); return false;", $url);
        }

        return $menuItem;
    }

    /**
     * Login as the currently selected extranet user (permissions needed).
     * This process works by creating a temporary token to verify the login and
     * redirecting the user to a route on the domain of the corresponding site
     * which uses said token to log the user in.
     *
     * @see LoginByTokenController::loginAction()
     */
    public function LoginAsExtranetUser(): void
    {
        /** @var SecurityHelperAccess $securityHelper */
        $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);

        if (false === $securityHelper->isGranted('CMS_RIGHT_ALLOW-LOGIN-AS-EXTRANET-USER')) {
            return;
        }

        $userId = $this->getInputFilterUtil()->getFilteredGetInput('id');
        if (null === $userId) {
            return;
        }

        $portal = $this->getPortalForExtranetUserId($userId);
        if (false === $portal) {
            return;
        }

        if (false === $this->hasUserAValidPassword()) {
            return;
        }

        $this->redirectUserToTokenLoginOnPortal($userId, $portal);
    }

    /**
     * Without a valid password cms users should not be able to log in as extranet users
     * without having a password, the logged-in user can't process through the basket steps
     * without any warning.
     */
    private function hasUserAValidPassword(): bool
    {
        return '' !== $this->oTable->fieldPassword;
    }

    /**
     * @return false|TdbCmsPortal
     */
    private function getPortalForExtranetUserId(string $userId)
    {
        $extranetUser = TdbDataExtranetUser::GetNewInstance();

        if (false === $extranetUser->Load($userId) || empty($extranetUser->fieldCmsPortalId)) {
            $portalList = TdbCmsPortalList::GetList();
            $portalList->GoToStart();

            return $portalList->Current();
        }

        return TdbCmsPortal::GetNewInstance($extranetUser->fieldCmsPortalId);
    }

    /**
     * @return never-returns - Ends request by redirecting
     */
    private function redirectUserToTokenLoginOnPortal(string $userId, TdbCmsPortal $portal): void
    {
        $token = $this->loginTokenService()->createTokenForUser(
            $userId,
            self::LOGIN_TOKEN_LIFETIME_SECONDS
        );
        $url = $this->router()->generateWithPrefixes(
            'chameleon_system_extranet.login_by_token',
            ['token' => $token],
            $portal,
            null,
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $this->getRedirect()->redirect($url);
    }

    private function getTranslator(): TranslatorInterface
    {
        return ServiceLocator::get('translator');
    }

    private function getInputFilterUtil(): InputFilterUtilInterface
    {
        return ServiceLocator::get('chameleon_system_core.util.input_filter');
    }

    private function getGlobal(): TGlobal
    {
        return ServiceLocator::get('chameleon_system_core.global');
    }

    private function getUrlUtil(): UrlUtil
    {
        return ServiceLocator::get('chameleon_system_core.util.url');
    }

    private function getRedirect(): ICmsCoreRedirect
    {
        return ServiceLocator::get('chameleon_system_core.redirect');
    }

    private function loginTokenService(): LoginTokenServiceInterface
    {
        return ServiceLocator::get('chameleon_system_extranet.login_by_token.service.login_token');
    }

    private function router(): PortalAndLanguageAwareRouterInterface
    {
        return ServiceLocator::get('chameleon_system_core.router.chameleon_frontend');
    }
}
