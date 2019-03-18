<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * The base class for all menu (category) items in the CMS. All menu items must inherit from this.
 *
 * @deprecated since 6.3.0 - only used for deprecated classic main menu
 */
class TCMSMenuItem
{
    /**
     * @var array
     */
    public $data;

    /**
     * Sets data for the item.
     *
     * @param array $data
     */
    public function SetData($data)
    {
        $this->data = $data;
    }

    /**
     * Returns an HTML link used to call the item's detail page.
     *
     * @return string
     */
    public function GetLink()
    {
        if (is_array($this->data) && array_key_exists('name', $this->data)) {
            return $this->data['name'];
        }

        return '';
    }
}
