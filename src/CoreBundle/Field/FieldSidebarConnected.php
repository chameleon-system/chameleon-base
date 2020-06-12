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
        $menuCategories = $this->menuItemDataAccess->getMenuCategories();

        foreach ($menuCategories as $menuCategory) {
            foreach ($menuCategory->getMenuItems() as $menuItem) {
                // TODO also this needs the information which table is linked (see todo in BreadcrumbBackendModule::getMatchingEntryFromMenu)
                if (false !== \strpos($menuItem->getUrl(), '='.$tableId)) {
                    return $menuCategory->getName() . ' > ' . $menuItem->getName();
                }
            }
        }

        return '';



        // TODO this more light-weight does not work
//        $menuEntry = $this->getMatchingEntryFromMenu($tableId);
//
//        if (null === $menuEntry) {
//            return '';
//        }
//
//        return $menuEntry->getName();
    }

    private function getMatchingEntryFromMenu(string $tableId): ?MenuItem
    {
        // TODO see todo in BreadcrumbBackendModule::getMatchingEntryFromMenu

        $tablePointerMenuItems = $this->menuItemDataAccess->getMenuItemsPointingToTables();

        if (false === \array_key_exists($tableId, $tablePointerMenuItems)) {
            return null;
        }

        return $tablePointerMenuItems[$tableId];
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
