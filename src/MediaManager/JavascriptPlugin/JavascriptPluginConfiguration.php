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

use ChameleonSystem\MediaManager\AccessRightsModel;

/**
 * This is the a configuration object for the media manager JavaScript plugin, it will be passed to the plugin encoded
 * as JSON.
 */
class JavascriptPluginConfiguration
{
    /**
     * Url configuration.
     *
     * @var JavascriptPluginConfigurationUrls|null
     */
    public $urls;

    /**
     * @var AccessRightsModel|null
     */
    public $accessRightsMedia;

    /**
     * @var AccessRightsModel|null
     */
    public $accessRightsMediaTree;

    /**
     * @var string|null
     */
    public $activeMediaItemId;
}
