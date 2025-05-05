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

/**
 * Track and retrieve usage statistics in the menu for every user.
 */
interface UserMenuItemDataAccessInterface
{
    /**
     * @return string[] - the menu items used by the user sorted by click count
     */
    public function getMenuItemIds(string $userId): array;

    public function trackMenuItem(string $userId, string $menuItemId): void;
}
