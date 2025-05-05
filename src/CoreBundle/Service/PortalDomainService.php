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

use ChameleonSystem\CoreBundle\CoreEvents;
use ChameleonSystem\CoreBundle\DataAccess\CmsPortalDomainsDataAccessInterface;
use ChameleonSystem\CoreBundle\Event\ChangeActiveDomainEvent;
use ChameleonSystem\CoreBundle\Event\ChangeActivePortalEvent;
use ChameleonSystem\CoreBundle\Exception\InvalidPortalDomainException;
use ChameleonSystem\CoreBundle\Service\Initializer\PortalDomainServiceInitializerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

/**
 * Class PortalDomainService.
 */
class PortalDomainService implements PortalDomainServiceInterface
{
    /**
     * @var \TdbCmsPortal|null
     */
    private $portal;
    /**
     * @var \TdbCmsPortalDomains
     */
    private $domain;
    /**
     * @var array|null
     */
    private $portalDomainNames;
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;
    /**
     * @var PortalDomainServiceInitializerInterface
     */
    private $portalDomainServiceInitializer;
    /**
     * @var bool
     */
    private $isInitializing = false;
    /**
     * @var CmsPortalDomainsDataAccessInterface
     */
    private $domainDataAccess;
    /**
     * @var LanguageServiceInterface
     */
    private $languageService;

    public function __construct(EventDispatcherInterface $eventDispatcher, PortalDomainServiceInitializerInterface $portalDomainServiceInitializer, CmsPortalDomainsDataAccessInterface $domainDataAccess, LanguageServiceInterface $languageService)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->portalDomainServiceInitializer = $portalDomainServiceInitializer;
        $this->domainDataAccess = $domainDataAccess;
        $this->languageService = $languageService;
    }

    /**
     * {@inheritdoc}
     */
    public function getActivePortal()
    {
        if (null === $this->portal) {
            $this->initialize();
        }

        return $this->portal;
    }

    /**
     * {@inheritdoc}
     */
    public function getActiveDomain()
    {
        if (null === $this->domain) {
            $this->initialize();
        }

        return $this->domain;
    }

    /**
     * @return void
     */
    private function initialize()
    {
        if ($this->isInitializing) {
            return;
        }
        $this->isInitializing = true;
        $this->portalDomainServiceInitializer->initialize($this);
        $this->isInitializing = false;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultPortal()
    {
        $tcmsPortal = \TdbCmsConfig::GetInstance()->GetPrimaryPortal();

        $tdbPortal = \TdbCmsPortal::GetNewInstance();
        if (false === $tdbPortal->Load($tcmsPortal->id)) {
            return null;
        }

        return $tdbPortal;
    }

    /**
     * {@inheritdoc}
     */
    public function getFileNotFoundPage()
    {
        $portal = $this->getActivePortal();
        $fileNotFoundPage = \TdbCmsTree::GetNewInstance($portal->fieldPageNotFoundNode);
        if (false === $fileNotFoundPage->sqlData) {
            throw new ResourceNotFoundException('No file-not-found page found.');
        }

        return $fileNotFoundPage;
    }

    /**
     * {@inheritdoc}
     */
    public function getDomainNameList()
    {
        if (null !== $this->portalDomainNames) {
            return $this->portalDomainNames;
        }
        $this->portalDomainNames = [];

        $portal = $this->getActivePortal();

        if (null === $portal) {
            return $this->portalDomainNames;
        }

        $domains = $portal->GetFieldCmsPortalDomainsList();
        while ($domain = $domains->Next()) {
            $domainName = trim($domain->fieldName);
            if ('' !== $domainName) {
                $this->portalDomainNames[] = $domainName;
            }
            $domainName = trim($domain->fieldSslname);
            if ('' !== $domainName) {
                $this->portalDomainNames[] = $domainName;
            }
        }

        return $this->portalDomainNames;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrimaryDomain($portalId = null, $languageId = null)
    {
        $domain = $this->doGetPrimaryDomain($portalId, $languageId);

        if (null === $domain) {
            throw new InvalidPortalDomainException(sprintf(
                'No primary domain for portal ID %s and language ID %s found. Be sure to configure a primary domain (also for each language if there is no domain without language selection).', $portalId, $languageId));
        }

        return $domain;
    }

    /**
     * {@inheritdoc}
     */
    public function hasPrimaryDomain($portalId = null, $languageId = null)
    {
        return null !== $this->doGetPrimaryDomain($portalId, $languageId);
    }

    /**
     * @param string|null $portalId
     * @param string|null $languageId
     *
     * @return \TdbCmsPortalDomains|null
     *
     * @throws InvalidPortalDomainException
     */
    private function doGetPrimaryDomain($portalId = null, $languageId = null)
    {
        if (null === $portalId) {
            $portal = $this->getActivePortal();
            if (null === $portal) {
                throw new InvalidPortalDomainException('No portal ID given and no active portal could be found.');
            }
            $portalId = $portal->id;
        }
        if (null === $languageId) {
            $languageId = $this->languageService->getActiveLanguageId();
            if (null === $languageId) {
                throw new InvalidPortalDomainException('No language ID given and no active language could be found.');
            }
        }

        return $this->domainDataAccess->getPrimaryDomain($portalId, $languageId);
    }

    /**
     * {@inheritdoc}
     */
    public function setActivePortal(?\TCMSPortal $portal = null)
    {
        $oldActivePortal = $this->portal;
        $this->portal = $portal;

        if ($oldActivePortal !== $portal) {
            $event = new ChangeActivePortalEvent($oldActivePortal, $portal);
            $this->eventDispatcher->dispatch($event, CoreEvents::CHANGE_ACTIVE_PORTAL);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setActiveDomain(?\TCMSPortalDomain $domain = null)
    {
        $oldActiveDomain = $this->domain;
        $this->domain = $domain;
        if ($oldActiveDomain !== $domain) {
            $event = new ChangeActiveDomainEvent($oldActiveDomain, $domain);
            $this->eventDispatcher->dispatch($event, CoreEvents::CHANGE_ACTIVE_PORTAL);
        }
    }
}
