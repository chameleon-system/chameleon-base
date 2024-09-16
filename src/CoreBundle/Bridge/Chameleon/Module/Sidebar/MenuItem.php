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

class MenuItem
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $icon;
    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $id;

    /**
     * @var string|null
     */
    private $tableId;

    public function __construct(string $id, string $name, string $icon, string $url, ?string $tableId = null)
    {
        $this->name = $name;
        $this->icon = $icon;
        $this->url = $url;
        $this->id = $id;
        $this->tableId = $tableId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getIcon(): string
    {
        return $this->icon;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string|null - the table id this menu entry points to - can be null (different menu entry)
     */
    public function getTableId(): ?string
    {
        return $this->tableId;
    }
}
