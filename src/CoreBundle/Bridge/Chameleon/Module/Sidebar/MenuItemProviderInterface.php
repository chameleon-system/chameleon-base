<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Bridge\Chameleon\Module\Sidebar;

/**
 * Provides a shared interface for different types of menu items.
 */
interface MenuItemProviderInterface
{
    /**
     * Returns a new menu item based on the passed menu item database object.
     */
    public function createMenuItem(\TdbCmsMenuItem $menuItem): ?MenuItem;
}
