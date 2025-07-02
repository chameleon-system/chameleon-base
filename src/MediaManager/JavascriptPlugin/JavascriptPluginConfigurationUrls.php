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
     */
    public ?string $listUrl;

    /**
     * used to generate the URL to an editor for media tree properties
     * placeholders:
     * --id-- ID of the media tree node.
     */
    public ?string $editMediaTreePropertiesUrlTemplate;

    /**
     * used to generate the URL to get information about a media tree node
     * placeholders:
     * --id-- ID of the media tree node.
     */
    public ?string $mediaTreeNodeInfoUrlTemplate;

    /**
     * URL to the endpoint which can be used to insert elements into tree.
     */
    public ?string $mediaTreeNodeInsertUrl;

    /**
     * URL to the endpoint which can be used to rename a tree element.
     */
    public ?string $mediaTreeNodeRenameUrl;

    /**
     * URL to the endpoint which can be used to delete a tree element.
     */
    public ?string $mediaTreeNodeDeleteUrl;

    /**
     * URL to the endpoint which can be used to move a tree element.
     */
    public ?string $mediaTreeNodeMoveUrl;

    /**
     * URL to show a delete confirmation.
     */
    public ?string $mediaItemDeleteConfirmationUrl;

    /**
     * URL to the endpoint which can be used to delete an image.
     */
    public ?string $mediaItemDeleteUrl;

    /**
     * URL to the endpoint which can be used to quickly edit image description or tags.
     */
    public ?string $quickEditUrl;

    /**
     * URL to the endpoint which can be used to move images to another media tree node.
     */
    public ?string $imagesMoveUrl;

    public ?string $uploaderUrlTemplate;

    public ?string $uploaderReplaceMediaItemUrl;

    /**
     * URL to media details.
     */
    public ?string $mediaItemDetailsUrlTemplate;

    /**
     * URL to use for auto completing search results.
     */
    public ?string $autoCompleteSearchUrl;

    /**
     * URL called after an image was picked.
     */
    public ?string $postSelectUrl;
    /**
     * url to the endpoint which can be used to find usages of a media item.
     */
    public ?string $mediaItemFindUsagesUrl;

    /**
     * url to the endpoint which can be used to find crops of a media item.
     */
    public ?string $mediaItemFindCropsUrl;
}
