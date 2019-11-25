<?php

namespace ChameleonSystem\CoreBundle\DataAccess;

/**
 * Track and retrieve usage statistics in the menu for every user.
 */
interface UserMenuItemDataAccessInterface
{
    /**
     * @param string $userId
     * @return string[] - the menu items used by the user sorted by click count
     */
    public function getMenuItemIds(string $userId): array;

    public function trackMenuItem(string $userId, string $menuItemId): void;
}
