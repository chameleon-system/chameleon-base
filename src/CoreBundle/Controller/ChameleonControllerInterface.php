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
     * @return void
     */
    public function setCache(CacheInterface $cache);

    /**
     * Adds a text line that is to be added to the header of the output page automatically.
     *
     * @param string $line
     *
     * @return void
     */
    public function AddHTMLHeaderLine($line);

    /**
     * Adds a text line that is to be added to the footer of the output page automatically.
     *
     * @param string $line
     *
     * @return void
     */
    public function AddHTMLFooterLine($line);

    public function getModuleLoader(): \TModuleLoader;
}
