<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\EventListener;

use ChameleonSystem\CoreBundle\Event\LocaleChangedEvent;
use ChameleonSystem\CoreBundle\Service\LanguageServiceInterface;
use ChameleonSystem\CoreBundle\Service\PortalDomainServiceInterface;

/**
 * Class ChangeChameleonObjectsLocaleListener.
 */
class ChangeChameleonObjectsLocaleListener
{
    /**
     * @var PortalDomainServiceInterface
     */
    private $portalDomainService;
    /**
     * @var LanguageServiceInterface
     */
    private $languageService;

    public function __construct(PortalDomainServiceInterface $portalDomainService, LanguageServiceInterface $languageService)
    {
        $this->portalDomainService = $portalDomainService;
        $this->languageService = $languageService;
    }

    /**
     * @return void
     */
    public function onLocaleChangedEvent(LocaleChangedEvent $event)
    {
        $portal = $this->portalDomainService->getActivePortal();
        if (null !== $portal) {
            $portal->SetLanguage($this->languageService->getActiveLanguageId());
            $portal->LoadFromRow($portal->sqlData);
        }

        $domain = $this->portalDomainService->getActiveDomain();
        if (null !== $domain) {
            $domain->LoadFromRow($domain->sqlData);
        }
    }
}
