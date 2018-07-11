<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\DataAccess;

use Doctrine\DBAL\Connection;
use TdbCmsPortalDomains;

class CmsPortalDomainsDataAccess implements CmsPortalDomainsDataAccessInterface
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrimaryDomain($portalId, $languageId)
    {
        $query = "SELECT *
                  FROM `cms_portal_domains` WHERE `cms_portal_id` = :portalId
                  AND `is_master_domain` = '1'
                  AND (`cms_language_id` = :languageId OR `cms_language_id` = '')
                  ORDER BY `cms_language_id` DESC LIMIT 0,1
                 ";

        $rows = $this->connection->fetchAll($query, [
            'portalId' => $portalId,
            'languageId' => $languageId,
        ]);

        if (0 === count($rows)) {
            return null;
        }

        return TdbCmsPortalDomains::GetNewInstance($rows[0]);
    }
}
