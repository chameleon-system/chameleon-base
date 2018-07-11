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
 * the base class for all menu (category) items in the cms. all menu items
 * must inherit from this.
/**/
class TCMSMenuItem
{
    /**
     * holds the menu item data.
     *
     * @var array
     */
    public $data = null;

    /**
     * set the data for the item.
     *
     * @param array $data
     */
    public function SetData($data)
    {
        $this->data = $data;
    }

    /**
     * return an html link used to call the items detail page.
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
