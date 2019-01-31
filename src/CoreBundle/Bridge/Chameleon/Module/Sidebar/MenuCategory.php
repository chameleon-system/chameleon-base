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

class MenuCategory
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var MenuItem[]
     */
    private $menuItems;

    /**
     * @param string     $name
     * @param MenuItem[] $menuItems
     */
    public function __construct(string $name, array $menuItems)
    {
        $this->name = $name;
        $this->menuItems = $menuItems;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getMenuItems(): array
    {
        return $this->menuItems;
    }
}
