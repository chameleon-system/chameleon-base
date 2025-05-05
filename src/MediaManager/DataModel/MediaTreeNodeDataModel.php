<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\MediaManager\DataModel;

class MediaTreeNodeDataModel
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var MediaTreeNodeDataModel[]
     */
    private $children;

    /**
     * @var string|null
     */
    private $iconPath;

    /**
     * @param string $id
     * @param string $name
     * @param MediaTreeNodeDataModel[] $children
     */
    public function __construct($id, $name, array $children = [])
    {
        $this->id = $id;
        $this->name = $name;
        $this->children = $children;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return MediaTreeNodeDataModel[]
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @return string|null
     */
    public function getIconPath()
    {
        return $this->iconPath;
    }

    /**
     * @param string|null $iconPath
     *
     * @return void
     */
    public function setIconPath($iconPath)
    {
        $this->iconPath = $iconPath;
    }
}
