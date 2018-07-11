<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Util\UrlNormalization;

class UrlNormalizerBasicEnd implements UrlNormalizerInterface
{
    /**
     * @var bool
     */
    private $toLowerCase;

    /**
     * UrlNormalizerBasicEnd constructor.
     *
     * @param bool $toLowerCase
     */
    public function __construct($toLowerCase = false)
    {
        $this->toLowerCase = $toLowerCase;
    }

    /**
     * {@inheritdoc}
     */
    public function normalizeUrl($url, $spacer)
    {
        if (!empty($spacer)) {
            $url = preg_replace('#['.$spacer.']+#', $spacer, $url);
        }

        if ($this->toLowerCase) {
            $url = mb_strtolower($url);
        }

        return $url;
    }
}
