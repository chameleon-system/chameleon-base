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
        try {
            $row = $this->connection->fetchAssociative('SELECT `pkg_cms_theme`.*
                                                                FROM `pkg_cms_theme`
                                                          INNER JOIN `cms_config` ON `pkg_cms_theme`.`id` = `cms_config`.`pkg_cms_theme_id`');
        } catch (Exception $exception) {
            // The field might still be missing
            $this->logger->error('CmsConfigDataAccess: Cannot determine theme', ['exception' => $exception]);

            return null;
        }

        if (false === $row) {
            return null;
        }

        $theme = \TdbPkgCmsTheme::GetNewInstance();
        $theme->LoadFromRow($row);

        return $theme;
    }
}
