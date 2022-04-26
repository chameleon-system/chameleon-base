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
use TdbCmsTree;

class DataAccessCmsTree implements DataAccessCmsTreeInterface
{
    /**
     * @var Connection
     */
    private $databaseConnection;
    /**
     * @var LanguageServiceInterface
     */
    private $languageService;

    /**
     * @param Connection               $databaseConnection
     * @param LanguageServiceInterface $languageService
     */
    public function __construct(Connection $databaseConnection, LanguageServiceInterface $languageService)
    {
        $this->databaseConnection = $databaseConnection;
        $this->languageService = $languageService;
    }

    /**
     * {@inheritdoc}
     */
    public function loadAll($languageId = null)
    {
        $query = 'SELECT * FROM `cms_tree` ORDER BY `lft`';

        $result = $this->databaseConnection->fetchAll($query);
        $trees = array();
        if (null === $languageId) {
            $languageId = $this->languageService->getActiveLanguageId();
        }
        foreach ($result as $row) {
            $treeId = $row['id'];
            $tree = TdbCmsTree::GetNewInstance();
            $tree->SetLanguage($languageId);
            $tree->LoadFromRow($row);
            $trees[$treeId] = $tree;
        }

        return $trees;
    }

    /**
     * {@inheritdoc}
     */
    public function getAllInvertedNoFollowRulePageIds()
    {
        $rows = $this->databaseConnection->fetchAll('SELECT `source_id`, `target_id` FROM `cms_tree_cms_tpl_page_mlt`');

        if (false === $rows) {
            return array();
        }

        return array_reduce(
            $rows,
            function (array $carry, array $row) {
                if (!isset($carry[$row['source_id']])) {
                    $carry[$row['source_id']] = array();
                }
                $carry[$row['source_id']][] = $row['target_id'];

                return $carry;
            },
            array()
        );
    }

    /**
     * {@inheritdoc}
     *
     * @psalm-suppress FalsableReturnStatement
     */
    public function getInvertedNoFollowRulePageIds($cmsTreeId)
    {
        $query = 'SELECT `target_id` FROM `cms_tree_cms_tpl_page_mlt` WHERE `source_id` = :treeId';

        return $this->databaseConnection->fetchColumn($query, array('treeId' => $cmsTreeId));
    }
}
