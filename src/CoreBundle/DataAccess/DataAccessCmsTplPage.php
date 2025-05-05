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
use TdbCmsTplPage;

/**
 * @implements DataAccessInterface<TdbCmsTplPage>
 */
class DataAccessCmsTplPage implements DataAccessInterface
{
    /**
     * @var Connection
     */
    private $databaseConnection;

    public function __construct(Connection $databaseConnection)
    {
        $this->databaseConnection = $databaseConnection;
    }

    /**
     * {@inheritDoc}
     */
    public function loadAll($languageId = null)
    {
        $query = 'SELECT `cms_tpl_page`.*
                     FROM `cms_tpl_page`
                     ORDER BY `cms_tpl_page`.`cmsident`';

        $pages = $this->databaseConnection->fetchAllAssociative($query);
        $pageList = [];
        foreach ($pages as $page) {
            $pageId = $page['id'];
            $pageList[$pageId] = \TdbCmsTplPage::GetNewInstance($page, $languageId);
        }

        return $pageList;
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheTriggers()
    {
        return [
            'cms_tpl_page',
            'cms_tree_node',
        ];
    }
}
