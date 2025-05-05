<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TGoogleMapIcon
{
    /**
     * ID/name of the icon
     * needs to be JS safe, because with will be part of the JS variable name
     * use getIconID() to fetch the ID.
     *
     * @var string
     */
    protected $id = '';

    /**
     * URL to the icon image.
     *
     * @var string
     */
    public $icon = '';

    /**
     * @var string
     */
    public $shadow = '';

    /**
     * @var int|null
     */
    public $width;

    /**
     * @var int|null
     */
    public $height;

    /**
     * returns the icon ID
     * this always includes the prefix "icon_"!
     *
     * @return string
     */
    public function getIconID()
    {
        if (empty($this->id)) {
            $this->id = md5($this->icon);
        }

        $sIconID = 'icon_'.$this->id;

        return $sIconID;
    }

    /**
     * sets the icon ID, has to be JS safe!
     *
     * @param string $sID
     */
    public function setIconID($sID)
    {
        $this->id = $sID;
    }

    /**
     * fallback for direct write access to $id.
     *
     * @param string $property
     *
     * @return $this
     */
    public function __set($property, $value)
    {
        if (property_exists($this, $property) && 'id' == $property) {
            $this->$property = $value;
        }
    }

    /**
     * fallback for direct read access to $id.
     *
     * @param string $property
     *
     * @return string
     */
    public function __get($property)
    {
        if (property_exists($this, $property) && 'id' == $property) {
            return $this->getIconID();
        }
    }
}
