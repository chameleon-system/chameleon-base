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

use ChameleonSystem\CoreBundle\DataAccess\CmsPortalDomainsDataAccessInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CaseInsensitivePortalExceptionListener
{
    /**
     * @var CmsPortalDomainsDataAccessInterface
     */
    private $cmsPortalDomainsDataAccess;

    public function __construct(CmsPortalDomainsDataAccessInterface $cmsPortalDomainsDataAccess)
    {
        $this->cmsPortalDomainsDataAccess = $cmsPortalDomainsDataAccess;
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if (false === $exception instanceof NotFoundHttpException) {
            return;
        }

        $request = $event->getRequest();
        $relativePath = $request->getPathInfo();
        $portalPrefixCandidate = \trim($this->getPortalPrefixCandidate($relativePath));
        if ('' === $portalPrefixCandidate) {
            return;
        }
        $prefixList = $this->cmsPortalDomainsDataAccess->getPortalPrefixListForDomain($request->getHost());
        foreach ($prefixList as $prefix) {
            if ($prefix === $portalPrefixCandidate) {
                /*
                 * We found a portal prefix but it is the same as the one requested by the user, so the cause of the
                 * NotFoundHttpException must be somewhere outside the scope of this listener.
                 */
                break;
            }
            if (\strtolower($prefix) === \strtolower($portalPrefixCandidate)) {
                $url = $this->replacePortalPrefix($relativePath, $portalPrefixCandidate, $prefix);
                $code = 'POST' === $request->getMethod() ? Response::HTTP_TEMPORARY_REDIRECT : Response::HTTP_MOVED_PERMANENTLY;
                $event->setResponse(new RedirectResponse($url, $code));
                break;
            }
        }
    }

    /**
     * Returns the path fragment between first and second slash (or everything after the first slash if there is no
     * second one) - this might be a portal prefix.
     */
    private function getPortalPrefixCandidate(string $relativePath): ?string
    {
        if ('' === $relativePath) {
            return null;
        }
        $secondSlashPosition = strpos($relativePath, '/', 1);
        if (false === $secondSlashPosition) {
            return \substr($relativePath, 1);
        }

        return \substr($relativePath, 1, $secondSlashPosition - 1);
    }

    private function replacePortalPrefix(string $path, string $originalPrefix, string $newPrefix): string
    {
        return \substr_replace($path, $newPrefix, 1, \strlen($originalPrefix));
    }
}
