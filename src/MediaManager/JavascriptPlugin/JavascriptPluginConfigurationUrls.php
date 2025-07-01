<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\MediaManager\JavascriptPlugin;

/**
 * Part of the JavaScript plugin configuration, hold URLs to media manager module functions.
 */
class JavascriptPluginConfigurationUrls
{
    /**
     * used to generate the URL to display a list.
     *
     * @var string|null
     */
    public $listUrl;

    /**
     * used to generate the URL to an editor for media tree properties
     * placeholders:
     * --id-- ID of the media tree node.
     *
     * @var string|null
     */
    public $editMediaTreePropertiesUrlTemplate;

    /**
     * used to generate the URL to get information about a media tree node
     * placeholders:
     * --id-- ID of the media tree node.
     *
     * @var string|null
     */
    public $mediaTreeNodeInfoUrlTemplate;

    /**
     * URL to the endpoint which can be used to insert elements into tree.
     *
     * @var string|null
     */
    public $mediaTreeNodeInsertUrl;

    /**
     * URL to the endpoint which can be used to rename a tree element.
     *
     * @var string|null
     */
    public $mediaTreeNodeRenameUrl;

    /**
     * URL to the endpoint which can be used to delete a tree element.
     *
     * @var string|null
     */
    public $mediaTreeNodeDeleteUrl;

    /**
     * URL to the endpoint which can be used to move a tree element.
     *
     * @var string|null
     */
    public $mediaTreeNodeMoveUrl;

    /**
     * URL to show a delete confirmation.
     *
     * @var string|null
     */
    public $mediaItemDeleteConfirmationUrl;

    /**
     * URL to the endpoint which can be used to delete an image.
     *
     * @var string|null
     */
    public $mediaItemDeleteUrl;

    /**
     * URL to the endpoint which can be used to quickly edit image description or tags.
     *
     * @var string|null
     */
    public $quickEditUrl;

    /**
     * URL to the endpoint which can be used to move images to another media tree node.
     *
     * @var string|null
     */
    public $imagesMoveUrl;

    /**
     * @var string|null
     */
    public $uploaderUrlTemplate;

    /**
     * @var string|null
     */
    public $uploaderReplaceMediaItemUrl;

    /**
     * URL to media details.
     *
     * @var string|null
     */
    public $mediaItemDetailsUrlTemplate;

    /**
     * URL to use for auto completing search results.
     *
     * @var string|null
     */
    public $autoCompleteSearchUrl;

    /**
     * @var string|null URL called after an image was picked
     */
    public $postSelectUrl;

    public $mediaItemFindUsagesUrl;
}
