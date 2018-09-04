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

use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CaseInsensitivePortalExceptionListener
{
    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function onKernelException(GetResponseForExceptionEvent $event): void
    {
        $exception = $event->getException();

        if (false === $exception instanceof NotFoundHttpException) {
            return;
        }

        $request = $event->getRequest();
        $relativePath = $request->getPathInfo();
        $portalPrefixCandidate = \trim($this->getPortalPrefixCandidate($relativePath));
        if ('' === $portalPrefixCandidate) {
            return;
        }
        $prefixList = $this->getPortalPrefixListForDomain($request->getHost());
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
     * Returns the path fragment between first and second backslash (or everything after the first slash if there is no
     * second one) - this might be a portal prefix.
     *
     * @param string $relativePath
     *
     * @return null|string
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

    /**
     * In Chameleon 6.3 this method will be located in CmsPortalDomainsDataAccess. In 6.2.x we avoid the BC break.
     *
     * @param string $domain
     *
     * @return array
     */
    private function getPortalPrefixListForDomain(string $domain)
    {
        if ('' === $domain) {
            return [];
        }

        $query = 'SELECT `cms_portal`.`identifier`
                    FROM `cms_portal_domains`
              INNER JOIN `cms_portal` ON `cms_portal_domains`.`cms_portal_id` = `cms_portal`.`id`
                   WHERE `cms_portal_domains`.`name` = ? OR `cms_portal_domains`.`sslname` = ?
                GROUP BY `cms_portal_domains`.`cms_portal_id`
               ';

        $result = $this->connection->fetchAll($query, [
            $domain,
            $domain,
        ]);
        $prefixList = [];
        foreach ($result as $row) {
            $prefixList[] = $row['identifier'];
        }

        return $prefixList;
    }
}
