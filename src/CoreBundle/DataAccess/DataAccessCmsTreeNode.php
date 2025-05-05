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
use TdbCmsTreeNode;

/**
 * @implements DataAccessInterface<TdbCmsTreeNode>
 */
class DataAccessCmsTreeNode implements DataAccessInterface
{
    /**
     * @var Connection
     */
    private $databaseConnection;
    /**
     * @var LanguageServiceInterface
     */
    private $languageService;

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
        $query = 'SELECT * FROM `cms_tree_node`';

        $result = $this->databaseConnection->fetchAllAssociative($query);
        $treeNodes = [];
        if (null === $languageId) {
            $languageId = $this->languageService->getActiveLanguageId();
        }
        foreach ($result as $row) {
            $treeNodeId = $row['id'];
            $tree = \TdbCmsTreeNode::GetNewInstance();
            $tree->SetLanguage($languageId);
            $tree->LoadFromRow($row);
            $treeNodes[$treeNodeId] = $tree;
        }

        return $treeNodes;
    }

    /**
     * @return string[]
     */
    public function getCacheTriggers()
    {
        return [
            'cms_tree_node',
        ];
    }
}
