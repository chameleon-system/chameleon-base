<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Security\Https;

class PermissiveHttpsContext implements HttpsContextInterface
{
    /**
     * {@inheritdoc}
     */
    public function isVerifyPeer()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isVerifyPeerName()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowSelfSigned()
    {
        return true;
    }
}
