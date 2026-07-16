<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\DataAccess;

/**
 * CmsPortalDomainsDataAccessInterface defines a service that acts as data access interface for the PortalDomainService.
 */
interface CmsPortalDomainsDataAccessInterface
{
    /**
     * Returns the primary domain object for the given $portalId and the given $languageId, or null if none was found.
     *
     * @param string $portalId
     * @param string $languageId
     *
     * @return \TdbCmsPortalDomains|null
     */
    public function getPrimaryDomain($portalId, $languageId);

    /**
     * @return array - the names of all domains of all portals
     *
     * NOTE \ChameleonSystem\CoreBundle\Service\PortalDomainServiceInterface::getDomainNameList() is similar but works only for the current portal.
     *   TODO This should be joined with that code (the service calling a method here).
     */
    public function getAllDomainNames(): array;

    /**
     * Returns a list of portal prefixes for portals that are available for the passed $domain.
     */
    public function getPortalPrefixListForDomain(string $domainName): array;

    /**
     * Returns all data for a portal that "might be" the currently active portal, given the passed restrictions.
     */
    public function getActivePortalCandidate(array $idRestrictionList, string $identifierRestriction, bool $allowInactivePortals): ?array;

    /**
     * Returns domain Tdb objects for a given domain name (either default name or SSL domain matches).
     *
     * This is a legacy lookup that groups by portal and may therefore drop additional domain rows for the same host.
     * It is not suitable for host resolution that needs to distinguish multiple domain variants per portal.
     */
    public function getDomainDataByName(string $domainName): array;

    /**
     * Returns all domain candidates for a given host (either default name or SSL domain matches).
     *
     * In contrast to getDomainDataByName(), this method does not group by portal and therefore keeps
     * multiple domain rows for the same host intact.
     */
    public function getDomainCandidatesByHost(string $host): array;

    /**
     * Returns all domain candidates for a given host restricted to a single portal.
     *
     * In contrast to getDomainDataByName(), this method does not group by portal and therefore keeps
     * multiple domain rows for the same host intact.
     */
    public function getDomainCandidatesByHostAndPortal(string $host, string $portalId): array;
}
