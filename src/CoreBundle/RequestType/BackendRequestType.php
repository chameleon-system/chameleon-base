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

class BackendRequestType extends AbstractRequestType
{
    /**
     * @return int
     */
    public function getRequestType()
    {
        return RequestTypeInterface::REQUEST_TYPE_BACKEND;
    }

    /**
     * @return void
     */
    public function initialize()
    {
        $this->initBackend();
        $this->sendDefaultHeaders();
    }

    /**
     * @return void
     */
    private function initBackend()
    {
        set_time_limit(1800);
        @ini_set('session.use_only_cookies', 1);
        @ini_set('session.use_trans_sid', 0);
    }

    /**
     * {@inheritdoc}
     */
    public function getControllerName()
    {
        return 'chameleon_system_core.backend_controller';
    }
}
