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
}
