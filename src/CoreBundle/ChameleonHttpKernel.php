<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class ChameleonHttpKernel extends HttpKernel
{
    /**
     * @var string
     */
    private $trustedProxies;
    /**
     * @var string
     */
    private $trustedHeaderClientIp;
    /**
     * @var string
     */
    private $trustedHeaderClientHost;
    /**
     * @var string
     */
    private $trustedHeaderClientPort;
    /**
     * @var string
     */
    private $trustedHeaderClientProtocol;

    /**
     * {@inheritdoc}
     */
    public function handle(Request $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true)
    {
        if ('' !== $this->trustedProxies) {
            $aProxies = explode(',', $this->trustedProxies);
            $aTrustedProxies = array();
            foreach ($aProxies as $val) {
                $val = trim($val);
                if ('' !== $val) {
                    $aTrustedProxies[] = $val;
                }
            }
            if (count($aTrustedProxies) > 0) {
                Request::setTrustedProxies($aTrustedProxies);
            }
        }

        if ('' !== $this->trustedHeaderClientIp) {
            Request::setTrustedHeaderName(Request::HEADER_CLIENT_IP, $this->trustedHeaderClientIp);
        }
        if ('' !== $this->trustedHeaderClientHost) {
            Request::setTrustedHeaderName(Request::HEADER_CLIENT_HOST, $this->trustedHeaderClientHost);
        }
        if ('' !== $this->trustedHeaderClientPort) {
            Request::setTrustedHeaderName(Request::HEADER_CLIENT_PORT, $this->trustedHeaderClientPort);
        }
        if ('' !== $this->trustedHeaderClientProtocol) {
            Request::setTrustedHeaderName(Request::HEADER_CLIENT_PROTO, $this->trustedHeaderClientProtocol);
        }

        return parent::handle($request, $type, $catch);
    }

    /**
     * @param string $trustedProxies comma-separated list of proxies to trust
     *
     * @return void
     */
    public function setTrustedProxies($trustedProxies)
    {
        $this->trustedProxies = $trustedProxies;
    }

    /**
     * @param string $trustedHeaderClientIp
     */
    public function setTrustedHeaderClientIp($trustedHeaderClientIp)
    {
        $this->trustedHeaderClientIp = $trustedHeaderClientIp;
    }

    /**
     * @param string $trustedHeaderClientHost
     */
    public function setTrustedHeaderClientHost($trustedHeaderClientHost)
    {
        $this->trustedHeaderClientHost = $trustedHeaderClientHost;
    }

    /**
     * @param string $trustedHeaderClientPort
     */
    public function setTrustedHeaderClientPort($trustedHeaderClientPort)
    {
        $this->trustedHeaderClientPort = $trustedHeaderClientPort;
    }

    /**
     * @param string $trustedHeaderClientProtocol
     */
    public function setTrustedHeaderClientProtocol($trustedHeaderClientProtocol)
    {
        $this->trustedHeaderClientProtocol = $trustedHeaderClientProtocol;
    }
}
