<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Service;

class GoogleApiKeyProvider implements GoogleApiKeyProviderInterface
{
    /**
     * @var string|null
     */
    private $googleApiKey;

    /**
     * @param string|null $googleApiKey
     */
    public function __construct($googleApiKey)
    {
        if ('' === $googleApiKey) {
            $googleApiKey = null;
        }

        $this->googleApiKey = $googleApiKey;
    }

    /**
     * {@inheritdoc}
     */
    public function getMapsApiKey()
    {
        return $this->googleApiKey;
    }
}
