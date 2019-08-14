<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\RequestType;

class FrontendRequestType extends AbstractRequestType
{
    private $allowedDomainNames = [];

    /**
     * @return int
     */
    public function getRequestType()
    {
        return RequestTypeInterface::REQUEST_TYPE_FRONTEND;
    }

    public function initialize()
    {
        $this->initFrontend();
        $this->sendDefaultHeaders();
    }

    private function initFrontend()
    {
        if (true === USE_ONLY_COOKIES_FOR_SESSION_ID) {
            // force users to use cookies
            @ini_set('session.use_only_cookies', 1);
            @ini_set('session.use_trans_sid', 0);
        } else {
            @ini_set('session.use_only_cookies', 0);
            @ini_set('session.use_trans_sid', 1);
        }
    }

    protected function sendDefaultHeaders()
    {
        parent::sendDefaultHeaders();

        $this->allowAdditionalDomains();
    }

    private function allowAdditionalDomains(): void
    {
        foreach ($this->allowedDomainNames as $allowedDomain) {
            // TODO some sort of "header encode"?
            header("X-Frame-Options: ALLOW-FROM $allowedDomain");
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getControllerName()
    {
        return 'chameleon_system_core.frontend_controller';
    }

    /**
     * {@inheritDoc}
     */
    public function setAllowedDomains(array $domainNames): void
    {
        $this->allowedDomainNames = $domainNames;
    }
}
