<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * defines an extranet user observer.
/**/
interface IDataExtranetUserObserver
{
    /**
     * the method is called by the user object when the user logges out.
     */
    public function OnUserLogoutHook();

    /**
     * the method is called by the user object when the user logs in.
     */
    public function OnUserLoginHook();

    /**
     * the method is called by the user object when the user data changes.
     */
    public function OnUserUpdatedHook();
}
