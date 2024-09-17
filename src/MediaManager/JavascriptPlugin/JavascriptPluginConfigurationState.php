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
 * This is the a state object for the media manager JavaScript plugin, it will be passed to the plugin encoded
 * as JSON.
 */
class JavascriptPluginConfigurationState
{
    /**
     * @var string|null
     */
    public $mediaTreeNodeId;

    /**
     * @var int
     */
    public $pageNumber = 0;

    /**
     * @var int
     */
    public $pageSize = -1;

    /**
     * @var string|null
     */
    public $searchTerm;

    /**
     * @var string|null
     */
    public $listView;

    /**
     * @var bool
     */
    public $showSubtree = true;

    /**
     * @var bool
     */
    public $deleteWithUsageSearch = true;

    /**
     * @var string|null
     */
    public $sortColumn;

    /**
     * Should elements to pick images be shown?
     *
     * @var bool
     */
    public $pickImageMode = false;

    /**
     * Javascript callback to execute after picking an image.
     *
     * @var string
     */
    public $pickImageCallback = '';

    /**
     * Javascript callback iFrame after picking an image.
     *
     * @var string
     */
    public $parentIFrame = '';

    /**
     * Is it possible to pick crops of images too?
     *
     * @var bool
     */
    public $pickImageWithCrop = false;
}
