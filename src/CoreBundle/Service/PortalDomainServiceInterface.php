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

use ChameleonSystem\CoreBundle\Exception\InvalidPortalDomainException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use TCMSPortal;
use TCMSPortalDomain;
use TdbCmsPortal;
use TdbCmsPortalDomains;
use TdbCmsTree;

/**
 * PortalDomainServiceInterface defines a service that provides information for the currently active portal and domain.
 */
interface PortalDomainServiceInterface
{
    /**
     * @return null|TdbCmsPortalDomains
     */
    public function getActiveDomain();

    /**
     * @return null|TdbCmsPortal
     */
    public function getActivePortal();

    /**
     * Returns the first defined portal.
     *
     * @return null|TdbCmsPortal
     */
    public function getDefaultPortal();

    /**
     * Returns the tree node for the file-not-found page (HTTP error 404) that is set for the active portal.
     *
     * @return TdbCmsTree
     *
     * @throws ResourceNotFoundException
     */
    public function getFileNotFoundPage();

    /**
     * Returns an array of all domains for the active portal.
     *
     * @return array
     */
    public function getDomainNameList();

    /**
     * Returns the primary domain for the given $portalId and the given $languageId.
     *
     * @param string|null $portalId
     * @param string|null $languageId
     *
     * @return TdbCmsPortalDomains
     *
     * @throws InvalidPortalDomainException if no primary domain was found.
     *                                      Is also thrown if no portal ID or language ID was given and no active values
     *                                      could be determined.
     */
    public function getPrimaryDomain($portalId = null, $languageId = null);

    /**
     * Returns true if a primary domain for the given $portalId and the given $languageId exists, else false.
     *
     * @param string|null $portalId
     * @param string|null $languageId
     *
     * @return bool
     *
     * @throws InvalidPortalDomainException if no $portalId or $languageId was given and no active values could be determined
     */
    public function hasPrimaryDomain($portalId = null, $languageId = null);

    /**
     * Sets the currently active portal.
     *
     * @param TCMSPortal $portal
     */
    public function setActivePortal(TCMSPortal $portal);

    /**
     * Sets the currently active domain.
     *
     * @param TCMSPortalDomain $domain
     */
    public function setActiveDomain(TCMSPortalDomain $domain);
}
