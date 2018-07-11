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

/**
 * HttpsContextInterface defines a service that determines the level of security when opening an encrypted connection to
 * an external host. This interface encapsulates some of the options described in the "SSL context options" chapter in
 * the PHP documentation (https://secure.php.net/manual/en/context.ssl.php).
 */
interface HttpsContextInterface
{
    /**
     * If true, verification of the peer's SSL/TLS certificate is required.
     *
     * @return bool
     */
    public function isVerifyPeer();

    /**
     * If true, verification of the peer name is required.
     *
     * @return bool
     */
    public function isVerifyPeerName();

    /**
     * If true, self-signed certificates are allowed (only if isVerifyPeer() returns true).
     *
     * @return bool
     */
    public function isAllowSelfSigned();
}
