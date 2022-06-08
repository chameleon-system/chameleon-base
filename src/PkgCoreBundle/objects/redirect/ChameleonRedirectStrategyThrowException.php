<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class ChameleonRedirectStrategyThrowException implements ChameleonRedirectStrategyInterface
{
    /**
     * @param string $url
     * @param int $status
     * @return never-returns
     * @throws ChameleonRedirectException
     */
    public function redirect($url, $status)
    {
        $redirectException = new ChameleonRedirectException();
        $redirectException->setUrl($url);
        $redirectException->setStatus($status);
        throw $redirectException;
    }
}
