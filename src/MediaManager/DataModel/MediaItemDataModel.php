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

class MediaItemDataModel
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $path;

    /**
     * @var string|null
     */
    private $name;

    /**
     * @var string[]
     */
    private $tags = [];

    /**
     * @var string|null
     */
    private $type;

    /**
     * @var int|null
     */
    private $width;

    /**
     * @var int|null
     */
    private $height;

    /**
     * @var string|null
     */
    private $altTag;

    /**
     * @var \DateTime|null
     */
    private $dateChanged;

    /**
     * @var string
     */
    private $systemName = '';

    /**
     * @var string
     */
    private $iconHtml = '';

    /**
     * @param string $id
     * @param string $path
     */
    public function __construct($id, $path)
    {
        $this->id = $id;
        $this->path = $path;
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
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     *
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string[]
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param string[] $tags
     *
     * @return void
     */
    public function setTags($tags)
    {
        $this->tags = $tags;
    }

    /**
     * @return string|null
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string|null $type
     *
     * @return void
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return int|null
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param int|null $width
     *
     * @return void
     */
    public function setWidth($width)
    {
        $this->width = $width;
    }

    /**
     * @return int|null
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @param int|null $height
     *
     * @return void
     */
    public function setHeight($height)
    {
        $this->height = $height;
    }

    /**
     * @return string|null
     */
    public function getAltTag()
    {
        return $this->altTag;
    }

    /**
     * @param string|null $altTag
     *
     * @return void
     */
    public function setAltTag($altTag)
    {
        $this->altTag = $altTag;
    }

    /**
     * @return \DateTime|null
     */
    public function getDateChanged()
    {
        return $this->dateChanged;
    }

    /**
     * @param \DateTime|null $dateChanged
     *
     * @return void
     */
    public function setDateChanged($dateChanged)
    {
        $this->dateChanged = $dateChanged;
    }

    /**
     * @return string
     */
    public function getSystemName()
    {
        return $this->systemName;
    }

    /**
     * @param string $systemName
     *
     * @return void
     */
    public function setSystemName($systemName)
    {
        $this->systemName = $systemName;
    }

    public function getIconHtml(): string
    {
        return $this->iconHtml;
    }

    public function setIconHtml(string $iconHtml): void
    {
        $this->iconHtml = $iconHtml;
    }
}
