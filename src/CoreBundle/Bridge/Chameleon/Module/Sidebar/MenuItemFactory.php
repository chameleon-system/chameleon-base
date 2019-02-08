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

class MenuItemFactory implements MenuItemFactoryInterface
{
    /**
     * @var MenuItemProviderInterface[]
     */
    private $menuItemProviders = [];

    public function addMenuItemProvider(string $identifier, MenuItemProviderInterface $menuItemProvider): void
    {
        $this->menuItemProviders[$identifier] = $menuItemProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function createMenuItem(\TdbCmsMenuItem $menuItem): ?MenuItem
    {
        $targetType = $menuItem->GetFieldTargetObjectType();
        if (false === \array_key_exists($targetType, $this->menuItemProviders)) {
            return null;
        }

        return $this->menuItemProviders[$targetType]->createMenuItem($menuItem);
    }
}
