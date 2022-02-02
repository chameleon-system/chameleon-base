<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CmsCoreLogBundle\Listener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\EventListener\ExceptionListener;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class TreatNotFoundErrorListener extends ExceptionListener
{
    /**
     * {@inheritDoc}
     */
    protected function logException(\Exception $exception, $message): void
    {
        if (null === $this->logger) {
            return;
        }

        if (true === $this->isNotFoundOrNotAllowed($exception)) {
            // We don't want an error for something as simple as "wrong url".

            $this->logger->notice($message, ['exception' => $exception]);

            return;
        }

        parent::logException($exception, $message);
    }

    private function isNotFoundOrNotAllowed(\Throwable $exception): bool
    {
        if ($exception instanceof HttpExceptionInterface
            && true === \in_array($exception->getStatusCode(), [
                Response::HTTP_UNAUTHORIZED,
                Response::HTTP_FORBIDDEN,
                Response::HTTP_NOT_FOUND
            ], true)) {

            return true;
        }

        return false;
    }
}
