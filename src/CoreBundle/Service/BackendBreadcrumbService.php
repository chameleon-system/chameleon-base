<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Service;

use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\SecurityBundle\Service\SecurityHelperAccess;
use Symfony\Component\HttpFoundation\RequestStack;

class BackendBreadcrumbService implements BackendBreadcrumbServiceInterface
{
    private const BREADCRUMB_SESSION_KEY = '_cmsurlhistory';

    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(
        RequestStack $requestStack
    ) {
        $this->requestStack = $requestStack;
    }

    public function getBreadcrumb(): ?\TCMSURLHistory
    {
        /** @var SecurityHelperAccess $securityHelper */
        $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);

        if (false === $securityHelper->isGranted('ROLE_CMS_USER')) {
            return null;
        }

        $breadCrumbHistory = $this->getBreadcrumbFromSession();

        if (null === $breadCrumbHistory || false === $breadCrumbHistory->paramsParameterExists()) {
            $this->reset();
        }

        return $this->getBreadcrumbFromSession();
    }

    private function getBreadcrumbFromSession(): ?\TCMSURLHistory
    {
        if (false === isset($_SESSION[self::BREADCRUMB_SESSION_KEY])) {
            $this->reset();
        }

        return $_SESSION[self::BREADCRUMB_SESSION_KEY];
    }

    /**
     * empties the breadcrumb in the session.
     */
    private function reset(): void
    {
        $_SESSION[self::BREADCRUMB_SESSION_KEY] = new \TCMSURLHistory();
    }
}
