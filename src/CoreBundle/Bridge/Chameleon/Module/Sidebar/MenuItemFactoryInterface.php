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

interface MenuItemFactoryInterface
{
    /**
     * Creates a new menu item based on the passed database menu item.
     * Might return null, e.g. if the current user does not have permission to view this menu item.
     */
    public function createMenuItem(\TdbCmsMenuItem $menuItem): ?MenuItem;
}
