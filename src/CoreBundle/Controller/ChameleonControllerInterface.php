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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Interface ChameleonControllerInterface defines a Chameleon controller which is responsible for
 * returning a response for the current request.
 */
interface ChameleonControllerInterface
{
    /**
     * Returns the response for the page definition determined by the routing.
     *
     * @return Response
     *
     * @throws AccessDeniedHttpException
     * @throws NotFoundHttpException
     */
    public function __invoke();

    /**
     * This method implies that you could get the response from it, but this is a lie.
     * Call __invoke() instead and wait for the interface to improve.
     *
     * @return Response
     *
     * @throws AccessDeniedHttpException
     * @throws NotFoundHttpException
     */
    public function getResponse();

    /**
     * Setter for a cache service to be used by the controller.
     *
     * @param CacheInterface $cache
     * @return void
     */
    public function setCache(CacheInterface $cache);

    /**
     * Adds a text line that is to be added to the header of the output page automatically.
     *
     * @param string $line
     * @return void
     */
    public function AddHTMLHeaderLine($line);

    /**
     * Adds a text line that is to be added to the footer of the output page automatically.
     *
     * @param string $line
     * @return void
     */
    public function AddHTMLFooterLine($line);

    /**
     * Flushes all buffered content to the browser. If true is passed for $enableAutoFlush, content will
     * automatically be flushed for each module from this call on. Note that implementations may decide not to flush or
     * disable content buffering altogether, so don't rely too heavily on the assumption that flushing is only
     * controlled by this method.
     *
     * @param bool $enableAutoFlush
     * @return void
     */
    public function FlushContentToBrowser($enableAutoFlush = false);
}
