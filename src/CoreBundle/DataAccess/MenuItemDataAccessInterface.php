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

use ChameleonSystem\CoreBundle\Bridge\Chameleon\Module\Sidebar\MenuCategory;
use ChameleonSystem\CoreBundle\DataModel\MenuCategoryAndItem;

interface MenuItemDataAccessInterface
{
    /**
     * @return MenuCategory[]
     */
    public function getMenuCategories(): array;

    /**
     * @return MenuCategoryAndItem[] - assoc array with the table id as key
     */
    public function getMenuItemsPointingToTable(): array;
}
