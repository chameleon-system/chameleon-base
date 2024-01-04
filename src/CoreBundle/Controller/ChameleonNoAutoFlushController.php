<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Controller;

use esono\pkgCmsCache\CacheInterface;

/**
 * @deprecated since 7.2.0 - early flushing should not be used
 */
class ChameleonNoAutoFlushController implements ChameleonControllerInterface
{
    /**
     * @var ChameleonController
     */
    private $controller;

    public function __construct(ChameleonController $controller)
    {
        $this->controller = $controller;
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke()
    {
        $original = $this->controller->getBlockAutoFlushToBrowser();
        $this->controller->SetBlockAutoFlushToBrowser(true);
        $response = $this->controller->__invoke();
        $this->controller->SetBlockAutoFlushToBrowser($original);

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function getResponse()
    {
        return $this->controller->getResponse();
    }

    /**
     * {@inheritdoc}
     */
    public function setCache(CacheInterface $cache)
    {
        $this->controller->setCache($cache);
    }

    /**
     * {@inheritdoc}
     */
    public function AddHTMLHeaderLine($sLine)
    {
        $this->controller->AddHTMLHeaderLine($sLine);
    }

    /**
     * {@inheritdoc}
     */
    public function AddHTMLFooterLine($sLine)
    {
        $this->controller->AddHTMLFooterLine($sLine);
    }

    /**
     * {@inheritdoc}
     */
    public function FlushContentToBrowser($enableAutoFlush = false)
    {
        $this->controller->FlushContentToBrowser($enableAutoFlush);
    }
}
