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

interface RequestTypeInterface
{
    const REQUEST_TYPE_FRONTEND = 1;
    const REQUEST_TYPE_BACKEND = 2;
    /**
     * boot - start db and autoloader, but not the session.
     *
     * @deprecated since 6.1.6 - not used anymore.
     */
    const REQUEST_TYPE_UNITTEST = 3;
    /**
     * boot only (autoloader, session and db are initialized).
     *
     * @deprecated since 6.1.6 - not used anymore.
     */
    const REQUEST_TYPE_BOOT_ONLY = 4;
    /**
     * @deprecated since 6.1.6 - see deprecation note for \ChameleonSystem\CoreBundle\RequestType\AssetRequestType.
     */
    const REQUEST_TYPE_ASSETS = 5;

    /**
     * @return int
     */
    public function getRequestType();

    public function initialize();

    /**
     * @return string
     */
    public function getControllerName();

    /**
     * Allow further domains to see this page (e.g. in an iframe).
     *
     * @param array $domainNames
     */
    public function setAllowedDomains(array $domainNames): void;
}
