<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace esono\pkgCmsRouting\exceptions;

class RedirectRequiredException extends \Exception
{
    /**
     * @var string
     */
    private $url;

    /**
     * @var bool
     */
    private $permanent = false;

    /**
     * @param string $url
     * @param bool $permanent
     */
    public function __construct($url, $permanent = false)
    {
        $this->url = $url;
        $this->permanent = $permanent;
        parent::__construct('redirect required');
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return bool
     */
    public function isPermanent()
    {
        return $this->permanent;
    }
}
