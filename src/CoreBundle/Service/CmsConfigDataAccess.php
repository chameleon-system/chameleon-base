<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Service;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Psr\Log\LoggerInterface;

class CmsConfigDataAccess implements CmsConfigDataAccessInterface
{
    /**
     * @var Connection
     */
    private $connection;
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(Connection $connection, LoggerInterface $logger)
    {
        $this->connection = $connection;
        $this->logger = $logger;
    }

    /**
     * {@inheritDoc}
     */
    public function getBackendTheme(): ?\TdbPkgCmsTheme
    {
        // TODO ? use a runtime-cache; this might be called several times...

        try {
            $themeId = $this->connection->fetchOne('SELECT `pkg_cms_theme_id` FROM `cms_config`');
        } catch (Exception $exception) {
            // The field might still be missing
            $this->logger->error('CmsConfigDataAccess: Cannot determine theme', ['exception' => $exception]);

            return null;
        }

        if (false === $themeId) {
            return null;
        }

        $theme = \TdbPkgCmsTheme::GetNewInstance();

        if (false === $theme->Load($themeId)) {
            return null;
        }

        return $theme;
    }
}
