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
                Request::setTrustedProxies($aTrustedProxies, Request::getTrustedHeaderSet());
            }
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

}
