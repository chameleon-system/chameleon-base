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

use ChameleonSystem\CoreBundle\Interfaces\MediaManagerUrlGeneratorInterface;

class CkEditorConfigProvider implements CkEditorConfigProviderInterface
{
    /**
     * @var MediaManagerUrlGeneratorInterface
     */
    private $mediaManagerUrlGenerator;

    public function __construct(MediaManagerUrlGeneratorInterface $mediaManagerUrlGenerator)
    {
        $this->mediaManagerUrlGenerator = $mediaManagerUrlGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function getExtraPlugins()
    {
        return array_merge(
            $this->getChameleonCkEditorPlugins(),
            $this->getAdditionalCkEditorPlugins(),
            $this->getDefaultCkEditorPlugins()
        );
    }

    /**
     * Returns a list of plugins that are included in the original CKEditor composer package, but not activated by default.
     *
     * @return array
     */
    private function getDefaultCkEditorPlugins()
    {
        return [
            'bidi',
            'dialogadvtab',
            'div',
            'find',
            'iframe',
            'image',
            'justify',
            'pagebreak',
            'scayt',
            'selectall',
            'showblocks',
            'smiley',
            'table',
            'tableresize',
            'tabletools',
        ];
    }

    /**
     * Returns a list of additional plugins not included in the CKEditor composer package.
     *
     * @return array
     */
    private function getAdditionalCkEditorPlugins()
    {
        return [
//            'autosave', // deactivated because of issues with language switching (#40622). Should only be enabled after #40632 is resolved or if the site does not use multiple languages.
            'backgrounds',
            'codemirror',
            'confighelper',
            'gg',
            'insertpre',
            'jsplus_stat',
            'mediaembed',
            'oembed',
            'onchange',
            'youtube',
        ];
    }

    /**
     * Returns a list of additional plugins provided by Chameleon.
     *
     * @return array
     */
    private function getChameleonCkEditorPlugins()
    {
        return [
            'chameleon_content_filter_diff',
            'chameleon_content_filter_extra',
            'chameleon_document_link',
            'chameleon_download_placeholder',
            'chameleon_image',
            'chameleon_link',
            'placeholder',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getExtraPluginsConfiguration()
    {
        $additionalPlugins = $this->getAdditionalCkEditorPlugins();
        $chameleonPlugins = $this->getChameleonCkEditorPlugins();
        $defaultPlugins = $this->getDefaultCkEditorPlugins();
        $dirCkEditorAdditionalPlugins = $this->getPluginDir('ckEditorPlugins');
        $dirCkEditorChameleonPlugins = $this->getPluginDir('ckEditorPluginsChameleon');
        $dirCkEditorDefaultPlugins = $this->getPluginDir('ckeditor/plugins');
        $extraPluginConfList = $this->getConfigurationForPlugins($additionalPlugins, $dirCkEditorAdditionalPlugins);
        $extraPluginConfChameleon = $this->getConfigurationForPlugins($chameleonPlugins, $dirCkEditorChameleonPlugins);
        $extraPluginConfDefault = $this->getConfigurationForPlugins($defaultPlugins, $dirCkEditorDefaultPlugins);

        return array_merge($extraPluginConfList, $extraPluginConfChameleon, $extraPluginConfDefault);
    }

    /**
     * @param string $pluginTypeSubDir
     *
     * @return string
     */
    private function getPluginDir($pluginTypeSubDir)
    {
        return sprintf('%sblackbox/components/ckEditor/%s', URL_USER_CMS_PUBLIC, $pluginTypeSubDir);
    }

    /**
     * @param string $pluginBaseDir
     *
     * @return array
     */
    private function getConfigurationForPlugins(array $extraPlugins, $pluginBaseDir)
    {
        $extraPluginConfigurationList = [];
        foreach ($extraPlugins as $extraPlugin) {
            $extraPluginConfigurationList[] = $this->getConfigurationForPlugin($extraPlugin, $pluginBaseDir);
        }

        return $extraPluginConfigurationList;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigurationForPlugin($pluginName, $pluginBaseDir)
    {
        return [
            'name' => $pluginName,
            'dir' => sprintf('%s/%s/', $pluginBaseDir, $pluginName),
            'jsFile' => 'plugin.js',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getToolbar()
    {
        return [
            [
                'name' => 'document',
                'items' => ['Source'],
            ],
            [
                'name' => 'clipboard',
                'items' => ['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo'],
            ],
            [
                'name' => 'editing',
                'items' => ['Find', 'Replace', '-', 'SelectAll', '-', 'Scayt'],
            ],
            [
                'name' => 'tools',
                'items' => ['Maximize', 'ShowBlocks', '-', 'About'],
            ],
            '/',
            [
                'name' => 'basicstyles',
                'items' => [
                    'Bold',
                    'Italic',
                    'Underline',
                    'Strike',
                    'Subscript',
                    'Superscript',
                    '-',
                    'RemoveFormat',
                ],
            ],
            [
                'name' => 'paragraph',
                'items' => [
                    'NumberedList',
                    'BulletedList',
                    '-',
                    'Outdent',
                    'Indent',
                    '-',
                    'Blockquote',
                    'CreateDiv',
                    '-',
                    'JustifyLeft',
                    'JustifyCenter',
                    'JustifyRight',
                    'JustifyBlock',
                    '-',
                    'BidiLtr',
                    'BidiRtl',
                ],
            ],
            '/',
            [
                'name' => 'styles',
                'items' => ['Styles', 'Format', 'Font', 'FontSize', 'TextColor', 'BGColor'],
            ],
            [
                'name' => 'links',
                'items' => ['Link', 'Unlink', 'Anchor'],
            ],
            [
                'name' => 'custom_stuff',
                'items' => ['CreatePlaceholder', '-', 'chameleon_document_link'],
            ],
            [
                'name' => 'insert',
                'items' => [
                    'Image',
                    'Youtube',
                    'oembed',
                    'Table',
                    'HorizontalRule',
                    'Smiley',
                    'SpecialChar',
                    'PageBreak',
                    'Iframe',
                    'InsertPre',
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getSettingsEditableMode()
    {
        return [
            'bodyClass' => "'cmswysiwyg'",
            'toolbar' => "'ChameleonDefault'",

            'filebrowserBrowseUrl' => "'".PATH_CMS_CONTROLLER."'",
            'filebrowserWindowHeight' => '800',
            'filebrowserWindowWidth' => '1200',

            'filebrowserLinkBrowseUrl' => "'".PATH_CMS_CONTROLLER."?pagedef=navigationTreeSingleSelectWysiwyg'",
            'filebrowserLinkWindowHeight' => '500',
            'filebrowserLinkWindowWidth' => '992',

            'filebrowserImageBrowseLinkUrl' => "'".PATH_CMS_CONTROLLER."?pagedef=navigationTreeSingleSelectWysiwyg'",
            'filebrowserImageWindowHeight' => '500',
            'filebrowserImageWindowWidth' => '992',

            'filebrowserImageBrowseUrl' => "'".$this->mediaManagerUrlGenerator->getUrlToPickImageForWysiwyg()."'",
            'filebrowserImageBrowseUrlWindowHeight' => '800',
            'filebrowserImageBrowseUrkWindowWidth' => '1200',

            'filebrowserCreate_chameleon_document_linkBrowseUrl' => "'".PATH_CMS_CONTROLLER."?pagedef=CMSDocumentManager&mode=wysiwyg'",
            'filebrowserCreate_chameleon_document_linkWindowHeight' => '800',
            'filebrowserCreate_chameleon_document_linkWindowWidth' => '1200',

            'fillEmptyBlocks' => 'true',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getSettingsReadonlyMode()
    {
        return [
            'bodyClass' => "'cmswysiwyg'",
            'toolbar' => "'ChameleonDefault'",
            'toolbar_ChameleonDefault' => '[]',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDisabledPlugins()
    {
        return [
            'elementspath',
            'save',
            'font',
            'colorbutton',
            'stylesheetparser',
        ];
    }
}
