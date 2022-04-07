<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Session;

/**
 * ChameleonSessionManagerInterface defines a service that is responsible for starting a correctly configured user
 * session.
 */
interface ChameleonSessionManagerInterface
{
    /**
     * Starts the session.
     *
     * @return void
     */
    public function boot();

    /**
     * Returns true if the session is currently in the process of being started. After the session was started, this
     * method will return false again.
     *
     * @return bool
     */
    public function isSessionStarting();
}
