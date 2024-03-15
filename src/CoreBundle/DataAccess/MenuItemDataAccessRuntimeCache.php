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

class MenuItemDataAccessRuntimeCache implements MenuItemDataAccessInterface
{
    /**
     * @var MenuCategory[]|null
     */
    private ?array $categoryCache = null;

    private MenuItemDataAccessInterface $subject;

    public function __construct(MenuItemDataAccessInterface $subject)
    {
        $this->subject = $subject;
    }

    /**
     * {@inheritDoc}
     */
    public function getMenuCategories(): array
    {
        if (null === $this->categoryCache) {
            $this->categoryCache = $this->subject->getMenuCategories();
        }

        return $this->categoryCache;
    }

    /**
     * {@inheritDoc}
     */
    public function getMenuItemsPointingToTable(): array
    {
        return $this->subject->getMenuItemsPointingToTable();
    }
}
