<?php

namespace ChameleonSystem\CoreBundle\DataModel;

use ChameleonSystem\CoreBundle\Bridge\Chameleon\Module\Sidebar\MenuCategory;
use ChameleonSystem\CoreBundle\Bridge\Chameleon\Module\Sidebar\MenuItem;

class MenuCategoryAndItem
{
    private MenuCategory $menuCategory;
    private MenuItem $menuItem;

    public function __construct(MenuCategory $menuCategory, MenuItem $menuItem)
    {
        $this->menuCategory = $menuCategory;
        $this->menuItem = $menuItem;
    }

    public function getMenuCategory(): MenuCategory
    {
        return $this->menuCategory;
    }

    public function getMenuItem(): MenuItem
    {
        return $this->menuItem;
    }
}
