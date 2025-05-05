<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\ExtranetBundle\Interfaces;

interface ExtranetConfigurationInterface
{
    public const PAGE_LOGIN = 1;
    public const PAGE_LOGIN_SUCCESS = 2;
    public const PAGE_MY_ACCOUNT = 3;
    public const PAGE_REGISTER = 4;
    public const PAGE_CONFIRM_REGISTRATION = 5;
    public const PAGE_FORGOT_PASSWORD = 6;
    public const PAGE_ACCESS_DENIED_NOT_LOGGED_IN = 7;
    public const PAGE_ACCESS_DENIED_INVALID_PERMISSIONS = 8;
    public const PAGE_POST_LOGOUT = 9;
    public const PAGE_LOGOUT = 10;

    /**
     * @return string
     */
    public function getExtranetHandlerSpotName();

    /**
     * @param int $page
     *
     * @psalm-param self::PAGE_* $page
     *
     * @return string|null
     */
    public function getLink($page);

    /**
     * @return \TdbDataExtranet|false
     */
    public function getExtranetConfigObject();
}
