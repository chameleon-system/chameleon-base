<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\ExtranetBundle\Service;

use ChameleonSystem\CoreBundle\Service\PortalDomainServiceInterface;
use ChameleonSystem\ExtranetBundle\Interfaces\ExtranetConfigurationInterface;

class ExtranetConfiguration implements ExtranetConfigurationInterface
{
    /**
     * @var \TdbDataExtranet
     */
    private $configObject;

    /**
     * @var PortalDomainServiceInterface
     */
    private $portalDomainService;

    public function __construct(
        PortalDomainServiceInterface $portalDomainService
    ) {
        $this->portalDomainService = $portalDomainService;
    }

    /**
     * {@inheritDoc}
     */
    public function getExtranetHandlerSpotName()
    {
        return $this->getExtranetConfigObject()->fieldExtranetSpotName;
    }

    /**
     * {@inheritDoc}
     */
    public function getLink($page)
    {
        $link = null;
        switch ($page) {
            case ExtranetConfigurationInterface::PAGE_ACCESS_DENIED_INVALID_PERMISSIONS:
                $link = $this->getExtranetConfigObject()->GetFieldAccessRefusedNodeIdPageURL();
                break;
            case ExtranetConfigurationInterface::PAGE_LOGIN:
                $link = $this->getExtranetConfigObject()->GetFieldNodeLoginIdPageURL();
                break;
            case ExtranetConfigurationInterface::PAGE_LOGIN_SUCCESS:
                $link = $this->getExtranetConfigObject()->GetFieldLoginSuccessNodeIdPageURL();
                break;
            case ExtranetConfigurationInterface::PAGE_MY_ACCOUNT:
                $link = $this->getExtranetConfigObject()->GetFieldNodeMyAccountCmsTreeIdPageURL();
                break;
            case ExtranetConfigurationInterface::PAGE_REGISTER:
                $link = $this->getExtranetConfigObject()->GetFieldNodeRegisterIdPageURL();
                break;
            case ExtranetConfigurationInterface::PAGE_CONFIRM_REGISTRATION:
                $link = $this->getExtranetConfigObject()->GetFieldNodeConfirmRegistrationPageURL();
                break;
            case ExtranetConfigurationInterface::PAGE_FORGOT_PASSWORD:
                $link = $this->getExtranetConfigObject()->GetFieldForgotPasswordTreenodeIdPageURL();
                break;
            case ExtranetConfigurationInterface::PAGE_ACCESS_DENIED_NOT_LOGGED_IN:
                $link = $this->getExtranetConfigObject()->GetFieldGroupRightDeniedNodeIdPageURL();
                break;
            case ExtranetConfigurationInterface::PAGE_POST_LOGOUT:
                $link = $this->getExtranetConfigObject()->GetFieldLogoutSuccessNodeIdPageURL();
                break;
            case ExtranetConfigurationInterface::PAGE_LOGOUT:
                $link = $this->getExtranetConfigObject()->GetLinkLogout($this->getExtranetHandlerSpotName());
                break;
        }

        return $link;
    }

    /**
     * {@inheritDoc}
     */
    public function getExtranetConfigObject()
    {
        if (null !== $this->configObject) {
            return $this->configObject;
        }

        $portal = $this->portalDomainService->getActivePortal();
        if ($portal) {
            $this->configObject = \TdbDataExtranet::GetNewInstance();
            $this->configObject->LoadFromField('cms_portal_id', $portal->id);
        } else {
            $configList = \TdbDataExtranetList::GetList();
            $this->configObject = $configList->Current();
        }

        return $this->configObject;
    }
}
