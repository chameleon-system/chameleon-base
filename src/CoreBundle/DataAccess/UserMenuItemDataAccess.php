<?php

namespace ChameleonSystem\CoreBundle\DataAccess;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Psr\Log\LoggerInterface;

class UserMenuItemDataAccess implements UserMenuItemDataAccessInterface
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
    public function getMenuItems(string $userId): array
    {
        if ('' === $userId) {
            return [];
        }

        $query = 'SELECT `cms_menu_item`.* 
                    FROM `cms_user_cms_menu_item_mlt`
              INNER JOIN `cms_menu_item` ON `cms_user_cms_menu_item_mlt`.`target_id` = `cms_menu_item`.`id`
                   WHERE `cms_user_cms_menu_item_mlt`.`source_id` = :userId
                ORDER BY `cms_user_cms_menu_item_mlt`.`entry_sort` DESC';

        try {
            $rows = $this->connection->fetchAll($query, ['userId' => $userId]);
        } catch (DBALException $exception) {
            $this->logger->error('Cannot get menu items for user', ['exception' => $exception]);

            return [];
        }

        if (false === $rows) {
            return [];
        }

        $menuItems = [];


        // todo USE MenuItemFactory here?

        foreach ($rows as $row) {
            $menuItem = \TdbCmsMenuItem::GetNewInstance();
            $menuItem->LoadFromRow($row);

            $menuItems[] = $menuItem->id;
        }

        return $menuItems;
    }

    /**
     * {@inheritDoc}
     */
    public function trackMenuItem(string $userId, string $menuItemId): void
    {
        if ('' === $userId || '' === $menuItemId) {
            return;
        }

        $query = 'SELECT `entry_sort` 
                    FROM `cms_user_cms_menu_item_mlt`
                   WHERE `source_id` = :userId
                     AND `target_id` = :menuItemId';

        try {
            $currentCount = $this->connection->fetchColumn($query, ['userId' => $userId, 'menuItemId' => $menuItemId]);

            if (false === $currentCount) {
                $this->connection->insert('cms_user_cms_menu_item_mlt', ['source_id' => $userId, 'target_id' => $menuItemId, 'entry_sort' => 1]);
            } else {
                $this->connection->update('cms_user_cms_menu_item_mlt', ['entry_sort' => $currentCount + 1], ['source_id' => $userId, 'target_id' => $menuItemId]);
            }
        } catch (DBALException $exception) {
            $this->logger->error('Cannot update menu items for user', ['exception' => $exception]);

            return;
        }
    }
}
