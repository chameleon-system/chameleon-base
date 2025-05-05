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
 * holds config info for the custom list module (such as list table, category table, etc).
 * /**/
class MTCustomListCoreConfig
{
    /**
     * a one-record-only table that defines data for the list (like title, intro text, ect).
     *
     * @var string
     */
    public $sListConfigTable = 'module_customlist_config';
    /**
     * the list table that holds all the list records.
     *
     * @var string
     */
    public $sListTable = '';
    /**
     * an optional category field by which the table will be grouped.
     *
     * @var string
     */
    public $sCategoryFieldName;

    public $sItemClass = 'MTCustomListCoreItem';
}
