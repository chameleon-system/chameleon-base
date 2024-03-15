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
use ChameleonSystem\CoreBundle\Bridge\Chameleon\Module\Sidebar\MenuItemFactoryInterface;
use ChameleonSystem\CoreBundle\DataModel\MenuCategoryAndItem;

class MenuItemDataAccess implements MenuItemDataAccessInterface
{
    private MenuItemFactoryInterface $menuItemFactory;

    public function __construct(MenuItemFactoryInterface $menuItemFactory)
    {
        $this->menuItemFactory = $menuItemFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function getMenuCategories(): array
    {
        $activeUser = \TCMSUser::GetActiveUser();
        if (null === $activeUser) {
            return [];
        }

        $menuCategories = [];

        $tdbCategoryList = \TdbCmsMenuCategoryList::GetList();
        $tdbCategoryList->ChangeOrderBy(['`cms_menu_category`.`position`' => 'ASC']);

        foreach ($tdbCategoryList as $tdbCategory) {
            $menuItems = [];
            $tdbMenuItemList = $tdbCategory->GetFieldCmsMenuItemList();
            $tdbMenuItemList->ChangeOrderBy([
                '`cms_menu_item`.`cms_menu_category_id`' => 'ASC',
                '`cms_menu_item`.`position`' => 'ASC',
            ]);

            foreach ($tdbMenuItemList as $tdbMenuItem) {
                $menuItem = $this->menuItemFactory->createMenuItem($tdbMenuItem);
                if (null !== $menuItem) {
                    $menuItems[] = $menuItem;
                }
            }
            if (\count($menuItems) > 0) {
                $menuCategories[] = new MenuCategory(
                    $tdbCategory->id,
                    $tdbCategory->fieldName,
                    $tdbCategory->fieldIconFontCssClass,
                    $menuItems
                );
            }
        }

        return $menuCategories;
    }

    /**
     * {@inheritDoc}
     */
    public function getMenuItemsPointingToTable(): array
    {
        $tableMenuItems = [];

        $menuCategories = $this->getMenuCategories();

        foreach ($menuCategories as $menuCategory) {
            foreach ($menuCategory->getMenuItems() as $menuItem) {
                if (null !== $menuItem->getTableId()) {
                    $tableMenuItems[$menuItem->getTableId()] = new MenuCategoryAndItem($menuCategory, $menuItem);
                }
            }
        }

        return $tableMenuItems;
    }
}
