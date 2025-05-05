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
use TdbCmsPortalSystemPage;

/**
 * @implements DataAccessInterface<TdbCmsPortalSystemPage>
 */
class DataAccessCmsPortalSystemPage implements DataAccessInterface
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
        $query = 'SELECT `cms_portal_system_page`.*
                     FROM `cms_portal_system_page`
                     ORDER BY `cms_portal_system_page`.`cmsident`';

        $systemPages = $this->databaseConnection->fetchAllAssociative($query);
        $systemPageList = [];
        foreach ($systemPages as $systemPage) {
            $pageId = $systemPage['id'];
            $systemPageList[$pageId] = \TdbCmsPortalSystemPage::GetNewInstance($systemPage, $languageId);
        }

        return $systemPageList;
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheTriggers()
    {
        return [
            'cms_portal_system_page',
            'cms_tree',
        ];
    }
}
