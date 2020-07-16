<?php

namespace ChameleonSystem\CoreBundle\Field;

use ChameleonSystem\CoreBundle\Bridge\Chameleon\Module\Sidebar\MenuItem;
use ChameleonSystem\CoreBundle\DataAccess\MenuItemDataAccessInterface;
use ChameleonSystem\CoreBundle\ServiceLocator;

class FieldSidebarConnected extends \TCMSFieldVarchar
{
    /**
     * @var MenuItemDataAccessInterface
     */
    private $menuItemDataAccess;

    public function __construct(?MenuItemDataAccessInterface $menuItemDataAccess = null)
    {
        // TODO isVirtual?

        if (null === $menuItemDataAccess) {
            $menuItemDataAccess = ServiceLocator::get('chameleon_system_core.sidebar.menu_access');
        }
        $this->menuItemDataAccess = $menuItemDataAccess;
    }

    /**
     * {@inheritDoc}
     */
    public function GetReadOnly(){
        $menuPath = $this->getMenuPath($this->recordId);

        // TODO propery render "readonly" also without class "form-control" ?
        return sprintf('<div class="form-control form-control-sm" readonly>%s</div>', $menuPath);
    }

    private function getMenuPath(string $tableId): string
    {
        // Heavy-weight AND heuristic approach
//        $menuCategories = $this->menuItemDataAccess->getMenuCategories();
//
//        foreach ($menuCategories as $menuCategory) {
//            foreach ($menuCategory->getMenuItems() as $menuItem) {
//                // TODO also this needs the information which table is linked (see todo in BreadcrumbBackendModule::getMatchingEntryFromMenu)
//                if (false !== \strpos($menuItem->getUrl(), '='.$tableId)) {
//                    return $menuCategory->getName() . ' > ' . $menuItem->getName();
//                }
//            }
//        }
//
//        return '';

        $menuEntry = $this->getMatchingEntryFromMenu($tableId);

        if (null === $menuEntry) {
            return '';
        }

        return $menuEntry[0]->getName().' / '.$menuEntry[1]->getName();
    }

    private function getMatchingEntryFromMenu(string $tableId): ?array
    {
        $tablePointerMenuItems = $this->getMenuItemsPointingToTable();

        if (false === \array_key_exists($tableId, $tablePointerMenuItems)) {
            return null;
        }

        return $tablePointerMenuItems[$tableId];
    }

    private function getMenuItemsPointingToTable(): array
    {
        // TODO / NOTE this method also exists in BreadcrumbBackendModule

        $tableMenuItems = [];

        $menuCategories = $this->menuItemDataAccess->getMenuCategories();

        foreach ($menuCategories as $menuCategory) {
            foreach ($menuCategory->getMenuItems() as $menuItem) {
                if (null !== $menuItem->getTableId()) {
                    $tableMenuItems[$menuItem->getTableId()] = [$menuCategory, $menuItem];
                }
            }
        }

        // TODO return type
        return $tableMenuItems;
    }

    /**
     * {@inheritDoc}
     */
    public function CreateFieldDefinition($returnDDL = false, $oField = null)
    {
        return '';
    }

    /**
     * {@inheritDoc}
     */
    public function ChangeFieldDefinition($oldName, $newName, &$postData = null)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function DeleteFieldDefinition()
    {
    }
}
