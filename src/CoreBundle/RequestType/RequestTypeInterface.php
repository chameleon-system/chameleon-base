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
    public const REQUEST_TYPE_FRONTEND = 1;
    public const REQUEST_TYPE_BACKEND = 2;
    /**
     * @deprecated since 6.1.6 - see deprecation note for \ChameleonSystem\CoreBundle\RequestType\AssetRequestType.
     */
    public const REQUEST_TYPE_ASSETS = 5;

    /**
     * @return int
     *
     * @psalm-return self::REQUEST_TYPE_*
     */
    public function getRequestType();

    /**
     * @return void
     */
    public function initialize();

    /**
     * @return string
     */
    public function getControllerName();
}
