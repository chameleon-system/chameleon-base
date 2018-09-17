<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\ExtranetBundle;

final class ExtranetEvents
{
    public const USER_LOGIN_SUCCESS = 'chameleon_system_extranet.user_login_success';
    public const USER_LOGIN_FAILURE = 'chameleon_system_extranet.user_login_failure';
    public const USER_BEFORE_LOGOUT = 'chameleon_system_extranet.user_before_logout';
    public const USER_LOGOUT_SUCCESS = 'chameleon_system_extranet.user_logout_success';
}
