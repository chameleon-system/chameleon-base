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

class FrontendRequestType extends AbstractRequestType
{
    /**
     * @return int
     */
    public function getRequestType()
    {
        return RequestTypeInterface::REQUEST_TYPE_FRONTEND;
    }

    /**
     * @return void
     */
    public function initialize()
    {
        $this->initFrontend();
        $this->sendDefaultHeaders();
    }

    /**
     * @return void
     */
    private function initFrontend()
    {
        // force users to use cookies
        @ini_set('session.use_only_cookies', 1);
        @ini_set('session.use_trans_sid', 0);
    }

    /**
     * @return void
     */
    protected function sendDefaultHeaders()
    {
        parent::sendDefaultHeaders();
    }

    /**
     * {@inheritdoc}
     */
    public function getControllerName()
    {
        return 'chameleon_system_core.frontend_controller';
    }
}
