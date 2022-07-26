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

use TdbDataExtranetUser;

/**
 * Provides the active extranet user.
 */
interface ExtranetUserProviderInterface
{
    /**
     * Returns the currently active extranet user.
     *
     * This user might not be initialized (if reset() was called before; otherwise it is safe to assume that a real user
     * object is returned). Returns null if not even an uninitialized user can be returned, e.g. if there is no session
     * available.
     *
     * @return TdbDataExtranetUser|null
     */
    public function getActiveUser();

    /**
     * Resets the active extranet user.
     *
     * After calling this method, calls to self::getActiveUser() will return an uninitialized TdbExtranetUser instance.
     *
     * @return void
     */
    public function reset();
}
