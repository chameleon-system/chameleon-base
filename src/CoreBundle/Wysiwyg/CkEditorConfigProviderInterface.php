<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Wysiwyg;

/**
 * CkEditorConfigProviderInterface provides various configuration values in order to properly initialize CKEditor
 * instances.
 */
interface CkEditorConfigProviderInterface
{
    /**
     * Returns a list of all plugins that are not active in the full CKEditor (both plugins provided by CKEditor but not
     * active by default and custom plugins).
     *
     * @return array
     */
    public function getExtraPlugins();

    /**
     * Returns configuration of all plugins that are not active in the full CKEditor (both plugins provided by CKEditor but not
     * active by default and custom plugins).
     *
     * @return array
     */
    public function getExtraPluginsConfiguration();

    /**
     * Returns the configuration of the CKEditor toolbar.
     *
     * @return array
     */
    public function getToolbar();

    /**
     * Returns configuration values for the CKEditor in editable mode.
     *
     * @return array
     */
    public function getSettingsEditableMode();

    /**
     * Returns configuration values for the CKEditor in readonly mode (normally there will be no toolbar elements).
     *
     * @return array
     */
    public function getSettingsReadonlyMode();

    /**
     * Returns a list of CKEditor plugins that are enabled by default, but should be disabled instead.
     *
     * @return array
     */
    public function getDisabledPlugins();

    /**
     * Returns the configuration of the given $pluginName located in $pluginBaseDir.
     *
     * @param string $pluginName
     * @param string $pluginBaseDir
     *
     * @return array
     */
    public function getConfigurationForPlugin($pluginName, $pluginBaseDir);
}
