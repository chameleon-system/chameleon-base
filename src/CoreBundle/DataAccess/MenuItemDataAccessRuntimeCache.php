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

use ChameleonSystem\CoreBundle\Bridge\Chameleon\Module\Sidebar\MenuItem;

class MenuItemDataAccessRuntimeCache implements MenuItemDataAccessInterface
{
    private $categoryCache = null;

    /**
     * @var MenuItemDataAccessInterface
     */
    private $subject;

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
    public function getMenuItemsPointingToTables(): array
    {
        // TODO?
        return $this->subject->getMenuItemsPointingToTables();
    }
}
