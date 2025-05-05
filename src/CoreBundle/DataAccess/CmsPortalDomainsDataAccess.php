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
use Doctrine\DBAL\ParameterType;

class CmsPortalDomainsDataAccess implements CmsPortalDomainsDataAccessInterface
{
    /**
     * @var Connection
     */
    private $connection;

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

        $rows = $this->connection->fetchAllAssociative($query, [
            'portalId' => $portalId,
            'languageId' => $languageId,
        ]);

        if (0 === count($rows)) {
            return null;
        }

        return \TdbCmsPortalDomains::GetNewInstance($rows[0]);
    }

    /**
     * {@inheritDoc}
     *
     * Copied partly from PortalDomainServiceInterface::getDomainNameList().
     */
    public function getAllDomainNames(): array
    {
        $portalList = \TdbCmsPortalList::GetList();

        $portalDomainNames = [];

        while (false !== ($portal = $portalList->Next())) {
            $domains = $portal->GetFieldCmsPortalDomainsList();
            while ($domain = $domains->Next()) {
                $domainName = trim($domain->fieldName);
                if ('' !== $domainName) {
                    $portalDomainNames[$domainName] = true;
                }
                $domainName = trim($domain->fieldSslname);
                if ('' !== $domainName) {
                    $portalDomainNames[$domainName] = true;
                }
            }
        }

        return \array_keys($portalDomainNames);
    }

    /**
     * {@inheritdoc}
     */
    public function getPortalPrefixListForDomain(string $domainName): array
    {
        if ('' === $domainName) {
            return [];
        }

        $query = 'SELECT `cms_portal`.`identifier`
                    FROM `cms_portal_domains`
              INNER JOIN `cms_portal` ON `cms_portal_domains`.`cms_portal_id` = `cms_portal`.`id`
                   WHERE `cms_portal_domains`.`name` = ? OR `cms_portal_domains`.`sslname` = ?
                GROUP BY `cms_portal_domains`.`cms_portal_id`
               ';

        $result = $this->connection->fetchAllAssociative($query, [
            $domainName,
            $domainName,
        ]);
        $prefixList = [];
        foreach ($result as $row) {
            $prefixList[] = $row['identifier'];
        }

        return $prefixList;
    }

    /**
     * {@inheritdoc}
     */
    public function getActivePortalCandidate(array $idRestrictionList, string $identifierRestriction, bool $allowInactivePortals): ?array
    {
        $query = "SELECT *
                    FROM `cms_portal`
                   WHERE `id` IN (?)
                     AND (`identifier` = ? OR `identifier` = '')
        ";

        if (false === $allowInactivePortals) {
            $query .= " AND `cms_portal`.`deactive_portal` != '1' ";
        }
        $query .= ' ORDER BY `identifier` DESC
                       LIMIT 0,1';

        $portalCandidate = $this->connection->fetchAssociative($query, [
             $idRestrictionList,
             $identifierRestriction,
         ], [
             Connection::PARAM_STR_ARRAY,
             ParameterType::STRING,
         ]);

        if (false === $portalCandidate) {
            return null;
        }

        return $portalCandidate;
    }

    /**
     * {@inheritdoc}
     */
    public function getDomainDataByName(string $domainName): array
    {
        if ('' === $domainName) {
            return [];
        }
        $query = 'SELECT *
                    FROM `cms_portal_domains`
                   WHERE `name` = ? OR `sslname` = ?
                GROUP BY `cms_portal_domains`.`cms_portal_id`
               ';

        return $this->connection->fetchAllAssociative($query, [
            $domainName,
            $domainName,
        ]);
    }
}
