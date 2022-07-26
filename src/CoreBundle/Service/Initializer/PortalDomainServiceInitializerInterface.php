<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Service\Initializer;

use ChameleonSystem\CoreBundle\Service\PortalDomainServiceInterface;

/**
 * Interface PortalDomainServiceInitializerInterface.
 */
interface PortalDomainServiceInitializerInterface
{
    /**
     * @param PortalDomainServiceInterface $portalDomainService
     *
     * @return void
     */
    public function initialize(PortalDomainServiceInterface $portalDomainService);
}
