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

class CmsMediaDataModel
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
     * @var string
     */
    private $imagePath;

    /**
     * @var string
     */
    private $imageUrl;

    /**
     * @param string $id
     * @param string $imagePath
     * @param string $name
     * @param string $imageUrl
     */
    public function __construct($id, $imagePath, $name, $imageUrl)
    {
        $this->id = $id;
        $this->imagePath = $imagePath;
        $this->name = $name;
        $this->imageUrl = $imageUrl;
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
    public function getImagePath()
    {
        return $this->imagePath;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getImageUrl()
    {
        return $this->imageUrl;
    }
}
