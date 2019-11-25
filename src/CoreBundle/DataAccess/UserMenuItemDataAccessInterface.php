<?php

namespace ChameleonSystem\CoreBundle\DataAccess;

/**
 * Track and retrieve usage statistics in the menu for every user.
 */
interface UserMenuItemDataAccessInterface
{
    public function getMenuItems(string $userId): array;

    public function trackMenuItem(string $userId, string $menuItemId): void;
}
