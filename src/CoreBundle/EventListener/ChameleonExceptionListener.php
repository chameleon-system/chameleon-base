<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\EventListener;

use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\EventListener\ExceptionListener;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

/**
 * @deprecated since 6.3.11 - this does not work anymore (overwriting a class in parameters)
 *
 * ChameleonExceptionListener avoids logging of 404 errors, as 1) they will be handled after the call to logException()
 * and not be worth logging (but the log might get quite flooded in some projects), and 2) the web server's access log
 * will contain information on those errors.
 * It would be better to configure Monolog with the excluded_404s option, but currently this functionality is broken
 * (the "request" service is not available anymore, see https://github.com/symfony/monolog-bundle/issues/166).
 * After that issue is resolved, this class should be removed.
 */
class ChameleonExceptionListener extends ExceptionListener
{
    /**
     * {@inheritDoc}
     *
     * @param string $message
     * @return void
     */
    protected function logException(Exception $exception, $message)
    {
        if (false === ($exception instanceof HttpExceptionInterface)) {
            parent::logException($exception, $message);

            return;
        }

        /**
         * @var HttpExceptionInterface&Exception $exception
         */
        if (Response::HTTP_NOT_FOUND !== $exception->getStatusCode()) {
            parent::logException($exception, $message);
        }
    }
}
