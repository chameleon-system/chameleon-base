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
 * holds the navigation item data.
/**/
class TCMSBreadcrumbNaviItem
{
    /**
     * name of the node.
     *
     * @var string|null
     */
    public $name = null;

    /**
     * page id of the node.
     *
     * @var string|null
     */
    public $page_id = null;

    /**
     * id of the node.
     *
     * @var string|null
     */
    public $id = null;

    /**
     * parent id of the node.
     *
     * @var string|null
     */
    public $parent_id = null;

    /**
     * the link from tree (can be set instead of a page).
     *
     * @var string|null
     */
    public $link = null;

    /**
     * if a link is given ( in tree) then this determines what window it will open in.
     *
     * @var string|null
     */
    public $linkTarget = null;

    /**
     * set to true if the item is active.
     *
     * @var bool
     */
    public $isActive = false;

    /**
     * the nodes children.
     *
     * @var array
     */
    public $children = array();

    /* ------------------------------------------------------------------------
     * overwrite this function to changes the way the node is rendered.
    /* ----------------------------------------------------------------------*/
    public function Render($depth, &$division)
    {
        return '<div style="padding-left:'.(5 * $depth)."px;background-color:{$division['color_primary_hexcolor']}\">".htmlspecialchars($this->name)."</div>\n";
    }
}
