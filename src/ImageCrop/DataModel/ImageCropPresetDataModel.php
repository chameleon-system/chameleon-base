<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\ImageCrop\DataModel;

class ImageCropPresetDataModel
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $systemName;

    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $width;

    /**
     * @var int
     */
    private $height;

    /**
     * @param string $id
     * @param string $systemName
     * @param string $name
     * @param int $width
     * @param int $height
     */
    public function __construct($id, $systemName, $name, $width, $height)
    {
        $this->id = $id;
        $this->systemName = $systemName;
        $this->name = $name;
        $this->width = $width;
        $this->height = $height;
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
    public function getSystemName()
    {
        return $this->systemName;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }
}
