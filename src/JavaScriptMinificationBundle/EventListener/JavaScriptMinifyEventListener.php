<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\JavaScriptMinificationBundle\EventListener;

use ChameleonSystem\CoreBundle\Event\ResourceCollectionJavaScriptCollectedEventInterface;
use ChameleonSystem\JavaScriptMinification\Exceptions\MinifyJsIntegrationException;
use ChameleonSystem\JavaScriptMinification\Interfaces\MinifyJsServiceInterface;
use Psr\Log\LoggerInterface;

class JavaScriptMinifyEventListener
{
    /**
     * @var MinifyJsServiceInterface
     */
    private $minifyJsService;
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(MinifyJsServiceInterface $minifyJsService, LoggerInterface $logger)
    {
        $this->minifyJsService = $minifyJsService;
        $this->logger = $logger;
    }

    /**
     * @return void
     */
    public function onMinifyRequest(ResourceCollectionJavaScriptCollectedEventInterface $event)
    {
        $content = $event->getContent();
        try {
            $content = $this->minifyJsService->minifyJsContent($content);
        } catch (MinifyJsIntegrationException $e) {
            $this->logger->error($e->getMessage());
        }
        $event->setContent($content);
    }
}
