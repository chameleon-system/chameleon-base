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

use ChameleonSystem\CoreBundle\Service\LanguageServiceInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;

class CmsPortalDomainsDataAccess implements CmsPortalDomainsDataAccessInterface
{
    /**
     * @var Connection
     */
    private $connection;
    private LanguageServiceInterface $languageService;

    public function __construct(Connection $connection, LanguageServiceInterface $languageService)
    {
        $this->connection = $connection;
        $this->languageService = $languageService;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrimaryDomain($portalId, $languageId)
    {
        $query = "SELECT `cms_portal_domains`.*,
                         `cms_portal`.`use_multilanguage`,
                         `cms_portal`.`cms_language_id` AS `portal_language_id`,
                         EXISTS(
                             SELECT 1
                               FROM `cms_portal_domain_cms_language_mlt`
                              WHERE `source_id` = `cms_portal_domains`.`id`
                         ) AS `has_additional_languages`,
                         EXISTS(
                             SELECT 1
                               FROM `cms_portal_domain_cms_language_mlt`
                              WHERE `source_id` = `cms_portal_domains`.`id`
                                AND `target_id` = :languageId
                         ) AS `has_requested_language`
                    FROM `cms_portal_domains`
              INNER JOIN `cms_portal` ON `cms_portal`.`id` = `cms_portal_domains`.`cms_portal_id`
                   WHERE `cms_portal_domains`.`cms_portal_id` = :portalId
                     AND `cms_portal_domains`.`is_master_domain` = '1'
                ORDER BY `cms_portal_domains`.`cms_language_id` DESC
                 ";

        $rows = $this->connection->fetchAllAssociative($query, [
            'portalId' => $portalId,
            'languageId' => $languageId,
        ]);

        /*
         * Domain selection priority:
         * 1. the requested language is assigned directly to the domain;
         * 2. it is the domain default (domain, portal, then global default) for a multilanguage domain;
         * 3. it is assigned as an additional language to a multilanguage domain;
         * 4. a legacy language-neutral domain.
         */
        $bestRow = null;
        $bestRank = 0;
        foreach ($rows as $row) {
            if ($row['cms_language_id'] === $languageId) {
                $bestRow = $row;
                break;
            }

            $usesAdditionalLanguages = 1 === (int) $row['use_multilanguage'] && 1 === (int) $row['has_additional_languages'];
            $portalDefaultLanguageId = $row['portal_language_id'];
            if ('' === $portalDefaultLanguageId) {
                $portalDefaultLanguageId = $this->languageService->getCmsBaseLanguageId();
            }
            if ($usesAdditionalLanguages) {
                $defaultLanguageId = $row['cms_language_id'];
                if ('' === $defaultLanguageId) {
                    $defaultLanguageId = $portalDefaultLanguageId;
                }
                $rank = $defaultLanguageId === $languageId ? 3 : (1 === (int) $row['has_requested_language'] ? 2 : 0);
            } else {
                $rank = '' === $row['cms_language_id'] ? 1 : 0;
            }

            if ($rank > $bestRank) {
                $bestRow = $row;
                $bestRank = $rank;
            }
        }

        if (null === $bestRow) {
            return null;
        }

        unset(
            $bestRow['use_multilanguage'],
            $bestRow['portal_language_id'],
            $bestRow['has_additional_languages'],
            $bestRow['has_requested_language']
        );

        return \TdbCmsPortalDomains::GetNewInstance($bestRow);
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
