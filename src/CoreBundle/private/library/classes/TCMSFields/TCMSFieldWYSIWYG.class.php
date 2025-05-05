<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\Service\CssClassExtractorInterface;
use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\CoreBundle\Util\UrlUtil;
use ChameleonSystem\CoreBundle\Wysiwyg\CkEditorConfigProviderInterface;
use esono\pkgCmsCache\CacheInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * WYSIWYG text field.
 *
 * You may set the field config variable "disableButtons" in CMS field configuration
 * or global via constant: CHAMELEON_WYSIWYG_DISABLED_BUTTONS.
 *
 * @see http://docs.cksource.com/CKEditor_3.x/Developers_Guide/Toolbar for detailed list
 *
 * It`s possible to overwrite the CSS URL by setting: css=[{portalurl}]/pathtowysiwyg.css
 * in the field configuration.
 *
 * /**/
class TCMSFieldWYSIWYG extends TCMSFieldText
{
    private string $editorHeight = '450px';

    private ?string $editorWidth = null;

    public function GetHTML()
    {
        parent::GetHTML();
        $viewRenderer = new ViewRenderer();
        $viewRenderer->AddSourceObject('sEditorName', 'fieldcontent_'.$this->sTableName.'_'.$this->name);
        $viewRenderer->AddSourceObject('sFieldName', $this->name);
        $viewRenderer->AddSourceObject('extraPluginsConfiguration', $this->getExtraPluginsConfiguration());
        $viewRenderer->AddSourceObject('aEditorSettings', $this->getEditorSettings());
        $sUserCssUrl = $this->getEditorCSSUrl();
        if ('' !== $sUserCssUrl) {
            $cssStyles = [];
            try {
                $cssStyles = $this->getJSStylesSet($sUserCssUrl);
            } catch (Exception $e) {
                $viewRenderer->AddSourceObject('couldNotLoadCustomCss', true);
                $viewRenderer->AddSourceObject('customCssUrl', $sUserCssUrl);
            }

            $viewRenderer->AddSourceObject('cssStyles', $cssStyles);
        }
        $viewRenderer->AddSourceObject('data', $this->data);
        $viewRenderer->AddSourceObject('editorHeight', (int) str_replace('px', '', $this->getEditorHeight()));

        $viewRenderer->AddSourceObject('isCalledInModal', $this->isCalledInModal());

        return $viewRenderer->Render('TCMSFieldWYSIWYG/cKEditor/editor.html.twig', null, false);
    }

    /**
     * renders the read only view of the field.
     *
     * @return string
     */
    public function GetReadOnly()
    {
        parent::GetReadOnly();
        $this->bReadOnlyMode = true;

        return $this->GetHTML();
    }

    private function getDefaultEditorSettings(): array
    {
        $aEditorSettings = [];

        $sReadOnlyMode = 'false';
        if ($this->bReadOnlyMode) {
            $sReadOnlyMode = 'true';
        }
        $aEditorSettings['readOnly'] = $sReadOnlyMode;
        $aEditorSettings['height'] = "'".$this->getEditorHeight()."'";
        $aEditorSettings['width'] = "'".$this->getEditorWidth()."'";
        $aEditorSettings['customConfig'] = "'".$this->getCustomConfigPath()."'";

        $configProvider = $this->getCkeditorConfigProvider();
        $settings = $this->bReadOnlyMode ? $configProvider->getSettingsReadonlyMode() : $configProvider->getSettingsEditableMode();

        $aEditorSettings = array_merge($settings, $aEditorSettings);
        if (false === $this->bReadOnlyMode) {
            $aEditorSettings['toolbar_ChameleonDefault'] = $this->convertToolbar($this->getToolbar());
            $aEditorSettings['extraPlugins'] = "'".implode(',', $this->getExtraPlugins())."'";
            $aEditorSettings['fillEmptyBlocks'] = 'true';
        }
        $aEditorSettings['removePlugins'] = $this->convertDisabledPlugins($this->getDisabledPlugins());

        $aEditorSettings['language'] = "'".$this->getLanguageCode()."'";

        $aEditorSettings['enterMode'] = $this->getEnterMode();

        $sUserCssUrl = $this->getEditorCSSUrl();
        if ('' !== $sUserCssUrl) {
            $aEditorSettings['contentsCss'] = "'".$sUserCssUrl."'";
            $aEditorSettings['stylesSet'] = "'".$this->getUniqueStylesSetName($sUserCssUrl)."'";
        }

        return $aEditorSettings;
    }

    /**
     * @return string
     */
    protected function getCustomConfigPath()
    {
        return sprintf('%s/components/ckEditor/chameleonConfig.js', URL_CMS);
    }

    /**
     * @return array
     */
    protected function getEditorSettings()
    {
        return $this->getDefaultEditorSettings();
    }

    /**
     * Returns a list of all custom plugins and plugins not in full ckEditor bundle.
     *
     * @return array
     */
    protected function getExtraPlugins()
    {
        return $this->getCkeditorConfigProvider()->getExtraPlugins();
    }

    /**
     * Returns array of all custom plugin and plugin configurations not in full ckEditor bundle.
     *
     * @return array
     */
    protected function getExtraPluginsConfiguration()
    {
        return $this->getCkeditorConfigProvider()->getExtraPluginsConfiguration();
    }

    /**
     * return an array of all js, css, or other header includes that are required
     * in the cms for this field. each include should be in one line, and they
     * should always be typed the same way so that no includes are included mor than once.
     *
     * @return array
     */
    public function GetCMSHtmlHeadIncludes()
    {
        $aIncludes = parent::GetCMSHtmlHeadIncludes();
        if (!is_array($aIncludes)) {
            $aIncludes = [];
        }
        $aIncludes[] = '<script src="'.URL_CMS.'/components/ckEditor/ckeditor/ckeditor.js" type="text/javascript"></script>';
        $aIncludes[] = '<script type="text/javascript" src="'.TGlobal::GetStaticURL(
            '/chameleon/blackbox/javascript/CKEditor/chameleon.ckeditor.js'
        ).'"></script>';

        return $aIncludes;
    }

    private function convertToolbar(array $toolbarSections): string
    {
        $toolbar = '';
        $count = 0;
        foreach ($toolbarSections as $section) {
            ++$count;
            if ($this->toolbarOptionIsValid($section)) {
                $toolbar .= '{ ';
                $toolbar .= "name: '".$section['name']."',";
                $toolbar .= 'items: [ ';
                $subCount = 0;
                foreach ($section['items'] as $sItem) {
                    ++$subCount;
                    $toolbar .= "'".$sItem."'";
                    if ($subCount < count($section['items'])) {
                        $toolbar .= ',';
                    }
                }
                $toolbar .= ' ]';
                $toolbar .= ' }';
            } else {
                $toolbar .= "'".$section."'";
            }
            if ($count < count($toolbarSections)) {
                $toolbar .= ',';
            }
        }

        return '[ '.$toolbar.' ]';
    }

    /**
     * @return array
     */
    protected function getToolbar()
    {
        $aToolbar = $this->getCkeditorConfigProvider()->getToolbar();
        $oUser = &TCMSUser::GetActiveUser();
        $aToolbar = $this->getModifiedToolbarByUser($oUser, $aToolbar);
        $aToolbar = $this->getModifiedToolbarByConstant($aToolbar);
        $aToolbar = $this->getModifiedToolbarByFieldConfig($aToolbar);

        return $aToolbar;
    }

    private function toolbarOptionIsValid(array|string $optionData): bool
    {
        return is_array($optionData) && isset($optionData['name']) && isset($optionData['items']);
    }

    /**
     * @param array $aToolbar
     *
     * @return array
     */
    protected function getModifiedToolbarByUser(TCMSUser $oUser, $aToolbar)
    {
        if (!$oUser->oAccessManager->PermitFunction('cms_wysiwyg_htmlcodeview')) {
            $aToolbar = $this->removeItemFromToolbar($aToolbar, 'Source');
        }

        return $aToolbar;
    }

    /**
     * remove buttons from toolbar of the editor defined by CHAMELEON_WYSIWYG_DISABLED_BUTTONS constant.
     *
     * @see http://docs.cksource.com/CKEditor_3.x/Developers_Guide/Toolbar for detailed list
     *
     * @param array $aToolbar
     *
     * @return array
     */
    protected function getModifiedToolbarByConstant($aToolbar)
    {
        $sRemoveButtonList = CHAMELEON_WYSIWYG_DISABLED_BUTTONS;
        $aRemoveButtonList = explode(',', $sRemoveButtonList);

        return $this->removeItemListFromToolbar($aToolbar, $aRemoveButtonList);
    }

    /**
     * remove buttons from toolbar of the editor defined by the field config variable "disableButtons" in CMS field configuration.
     *
     * @see http://docs.cksource.com/CKEditor_3.x/Developers_Guide/Toolbar for detailed list
     *
     * @param array $aToolbar
     *
     * @return array
     */
    protected function getModifiedToolbarByFieldConfig($aToolbar)
    {
        $sRemoveButtonList = $this->oDefinition->GetFieldtypeConfigKey('disableButtons');
        $sRemoveButtonList = explode(',', $sRemoveButtonList);

        return $this->removeItemListFromToolbar($aToolbar, $sRemoveButtonList);
    }

    /**
     * @param array $aToolbar
     * @param string $sSectionName
     *
     * @return array
     */
    protected function removeSectionFromToolbar($aToolbar, $sSectionName)
    {
        foreach ($aToolbar as $sKey => $mSection) {
            if ($this->toolbarOptionIsValid($mSection)) {
                if ($mSection['name'] == $sSectionName) {
                    unset($aToolbar[$sKey]);
                }
            }
        }
        reset($aToolbar);

        return $aToolbar;
    }

    /**
     * @param array $aToolbar
     * @param array $aItemList
     *
     * @return array
     */
    protected function removeItemListFromToolbar($aToolbar, $aItemList)
    {
        foreach ($aItemList as $sItem) {
            $sItem = trim($sItem);
            if (strlen($sItem) > 1) {
                if (preg_match('/^[a-zA-Z0-9_]+$/', $sItem)) {
                    $aToolbar = $this->removeItemFromToolbar($aToolbar, $sItem);
                }
            }
        }

        return $aToolbar;
    }

    /**
     * @param array $aToolbar
     * @param string $sItemName
     *
     * @return array
     */
    protected function removeItemFromToolbar($aToolbar, $sItemName)
    {
        foreach ($aToolbar as $sKey => $mSection) {
            if ($this->toolbarOptionIsValid($mSection)) {
                $sSearchKey = array_search($sItemName, $mSection['items']);
                if (false !== $sSearchKey) {
                    unset($aToolbar[$sKey]['items'][$sSearchKey]);
                }
            }
        }
        reset($aToolbar);

        return $aToolbar;
    }

    private function convertDisabledPlugins(array $aDisabledPlugins): string
    {
        $sDisabledPlugins = '';
        $iCount = 0;
        foreach ($aDisabledPlugins as $sPlugin) {
            $sDisabledPlugins .= $sPlugin;
            if ($iCount < count($aDisabledPlugins)) {
                $sDisabledPlugins .= ',';
            }
        }

        return "'".$sDisabledPlugins."'";
    }

    /**
     * @return array
     */
    protected function getDisabledPlugins()
    {
        return $this->getCkeditorConfigProvider()->getDisabledPlugins();
    }

    /**
     * returns the ISO6391 language code of the current CMS user.
     */
    private function getLanguageCode(): string
    {
        $user = TdbCmsUser::GetActiveUser();
        $backendLanguage = $user->GetFieldCmsLanguage();

        return $backendLanguage->fieldIso6391;
    }

    /**
     * enterMode defines how line break will be handled by the editor, this is defined by CHAMELEON_WYSIWYG_LINE_ENDINGS constant
     * possible values are DIV, BR and P default (and highly recommended) is P
     * this values will be translated into CKEDITOR.ENTER_DIV, CKEDITOR.ENTER_BR, and CKEDITOR.ENTER_P.
     */
    private function getEnterMode(): string
    {
        switch (CHAMELEON_WYSIWYG_LINE_ENDINGS) {
            case 'DIV':
                return 'CKEDITOR.ENTER_DIV';
            case 'BR':
                return 'CKEDITOR.ENTER_BR';
            case 'P':
            default:
                return 'CKEDITOR.ENTER_P';
        }
    }

    /**
     * Loads a custom CSS file for the editor based on CMS config, portal, template or field configuration (in this order).
     */
    private function getEditorCSSUrl(): string
    {
        // load portal based CSS URL
        $portalCssUrl = $this->getPortalCssUrl();

        // it is possible to set a custom css url per wysiwyg field
        $customCssUrl = $this->getUserCssUrl($portalCssUrl);

        if ('' === $customCssUrl) {
            // tries to load a CSS file from the layout of the first found page connected to a module instance of the current record/module table
            $templateCssUrl = $this->getTemplateCssUrl();

            if ('' !== $templateCssUrl) {
                $customCssUrl = $templateCssUrl;
            } elseif ('' !== $portalCssUrl && '.css' === substr($portalCssUrl, -4)) {
                $customCssUrl = $portalCssUrl;
            } else {
                $customCssUrl = TdbCmsConfig::GetInstance()->fieldWysiwygeditorCssUrl;
            }
        }

        if ('' === $customCssUrl) {
            return '';
        }

        return $this->getUrlUtilService()->getAbsoluteUrl(
            $customCssUrl,
            true,
            null,
            $this->getPortalForCurrentRecord()
        );
    }

    /**
     * @return Request
     */
    protected function getCurrentRequest()
    {
        return ServiceLocator::get('request_stack')->getCurrentRequest();
    }

    /**
     * @return UrlUtil
     */
    protected function getUrlUtilService()
    {
        return ServiceLocator::get('chameleon_system_core.util.url');
    }

    /**
     * @return string
     */
    private function getTemplateCssUrl()
    {
        $connectedPage = $this->getConnectedPageForCurrentRecord();

        if (null === $connectedPage || '' === $connectedPage->fieldCmsMasterPagedefId) {
            return '';
        }

        $pageTemplate = TdbCmsMasterPagedef::GetNewInstance();
        if (false === $pageTemplate->Load($connectedPage->fieldCmsMasterPagedefId)) {
            return '';
        }

        return $pageTemplate->fieldWysiwygCssUrl;
    }

    /**
     * possible values in portal table:
     * - http://www.fuu.baa/assets/myCustom.css
     * - https://www.fuu.baa/assets/myCustom.css
     * - [{portalurl}]/assets/myCustom.css
     * - /assets/my-portal-name.de/css/.
     */
    private function getPortalCssUrl(): string
    {
        $portal = $this->getPortalForCurrentRecord();

        if (null === $portal || '' === $portal->fieldWysiwygCssUrl) {
            return '';
        }

        return $portal->fieldWysiwygCssUrl;
    }

    /**
     * @return TdbCmsPortal|null
     */
    protected function getPortalForCurrentRecord()
    {
        $connectedPage = $this->getConnectedPageForCurrentRecord();

        if (null !== $connectedPage && '' !== $connectedPage->fieldCmsPortalId) {
            return $connectedPage->GetFieldCmsPortal();
        }

        if (array_key_exists(
            'cms_portal_id',
            $this->oTableRow->sqlData
        ) && !empty($this->oTableRow->sqlData['cms_portal_id'])
        ) {
            return TdbCmsPortal::GetNewInstance($this->oTableRow->sqlData['cms_portal_id']);
        }

        return null;
    }

    /**
     * @return TdbCmsTplPage|null
     */
    protected function getConnectedPageForCurrentRecord()
    {
        if (false === array_key_exists(
            'cms_tpl_module_instance_id',
            $this->oTableRow->sqlData
        ) || '' === $this->oTableRow->sqlData['cms_tpl_module_instance_id']
        ) {
            return null;
        }

        $cmsTplModuleInstance = TdbCmsTplModuleInstance::GetNewInstance();

        if (false === $cmsTplModuleInstance->Load($this->oTableRow->sqlData['cms_tpl_module_instance_id'])) {
            return null;
        }

        $connectedPage = $cmsTplModuleInstance->GetConnectedPage();
        if (null === $connectedPage) {
            return null;
        }

        return $connectedPage;
    }

    private function getUserCssUrl(string $portalCssUrl): string
    {
        $fieldSpecificCustomCssUrl = $this->oDefinition->GetFieldtypeConfigKey('css');
        if (null === $fieldSpecificCustomCssUrl) {
            return '';
        }

        if (false !== strpos($fieldSpecificCustomCssUrl, '[{portalurl}]')) {
            if ('' !== $portalCssUrl && '.css' !== substr($portalCssUrl, -4)) {
                // it doesn`t end with a CSS file, so it`s a prefix (e.g. /css/portalname/)

                $fieldSpecificCustomCssUrl = str_replace('[{portalurl}]', $portalCssUrl, $fieldSpecificCustomCssUrl);

                return $this->getUrlUtilService()->getAbsoluteUrl(
                    $fieldSpecificCustomCssUrl,
                    true,
                    null,
                    $this->getPortalForCurrentRecord()
                );
            } else {
                $fieldSpecificCustomCssUrl = str_replace('[{portalurl}]', '', $fieldSpecificCustomCssUrl);
            }
        }

        return $this->getUrlUtilService()->getAbsoluteUrl(
            $fieldSpecificCustomCssUrl,
            true,
            null,
            $this->getPortalForCurrentRecord()
        );
    }

    /**
     * Transforms css file path to a usable stylesSet name.
     */
    private function getUniqueStylesSetName(string $userCssUrl): string
    {
        return str_replace([':', '.', '/', '-'], '_', $userCssUrl);
    }

    /**
     * Parse custom user css definitions and prepare wysiwyg editor style setup.
     */
    private function getJSStylesSet(string $sUserCssUrl): array
    {
        $styles = [];

        $customerStyles = $this->GetWYSIWYGCustomerStyles($sUserCssUrl);
        foreach ($customerStyles as $className => $htmlTags) {
            if (0 === count($htmlTags)) {
                // if no elements are specified, we use the class for the following default elements
                $elements = ['p', 'div', 'span', 'a', 'h1', 'h2', 'h3', 'h4', 'ul', 'ol', 'li'];
            }
            foreach ($htmlTags as $htmlTag) {
                $styleData = [];
                $styleData['name'] = "'".$className.'('.$htmlTag.")'";
                $styleData['element'] = "'".$htmlTag."'";
                $styleData['attributes']['class'] = "'".$className."'";
                $styles[] = $styleData;
            }
        }

        return $styles;
    }

    /**
     * @param string $sUserCSSURL
     *
     * @return array
     */
    protected function GetWYSIWYGCustomerStyles($sUserCSSURL)
    {
        static $styleCache;
        if (!$styleCache) {
            $aParameters = ['class' => 'CSSTree', 'cssurl' => $sUserCSSURL];
            $cache = $this->getCache();
            $key = $cache->getKey($aParameters, false);
            $styleCache = $cache->get($key);
            if (null === $styleCache) {
                $styleCache = $this->getCssClassExtractor()->extractCssClasses($sUserCSSURL);
                $cache->set($key, $styleCache, []);
            }
        }

        return $styleCache;
    }

    /**
     * returns the length of a field
     * sets field max-width and field CSS width.
     */
    public function _GetFieldWidth()
    {
        if (0 !== $this->oDefinition->sqlData['field_width'] && '0' !== $this->oDefinition->sqlData['field_width']) {
            // max length
            $this->fieldCSSwidth = ($this->oDefinition->sqlData['field_width'] + 30).'px';
            $this->fieldWidth = $this->oDefinition->sqlData['field_width'];
        } else {
            // the real length of the field
            $this->fieldWidth = $this->oDefinition->sqlData['field_width'];
            $this->fieldCSSwidth = '100%';
        }

        return $this->fieldWidth;
    }

    /** instance properties setter and getter */

    /**
     * @return string
     */
    protected function getEditorHeight()
    {
        if (true === $this->isCalledInModal()) {
            return '650';
        }

        return $this->editorHeight;
    }

    /**
     * @return string
     */
    protected function getEditorWidth()
    {
        if (null === $this->editorWidth) {
            if (!empty($this->fieldCSSwidth)) {
                $editorWidth = $this->fieldCSSwidth;
            } else {
                $editorWidth = '100%';
            }
            $this->setEditorWidth($editorWidth);

            return $editorWidth;
        }

        return $this->editorWidth;
    }

    /**
     * @param string $sEditorHeight
     */
    protected function setEditorHeight($sEditorHeight)
    {
        $this->editorHeight = $sEditorHeight;
    }

    /**
     * @param string $sEditorWidth
     */
    protected function setEditorWidth($sEditorWidth)
    {
        $this->editorWidth = $sEditorWidth;
    }

    private function isCalledInModal(): bool
    {
        return '1' === $this->getCurrentRequest()->get('isInModal');
    }

    private function getCkeditorConfigProvider(): CkEditorConfigProviderInterface
    {
        return ServiceLocator::get('chameleon_system_core.wysiwyg.ckeditor_config_provider');
    }

    private function getCssClassExtractor(): CssClassExtractorInterface
    {
        return ServiceLocator::get('chameleon_system_core.service.css_class_extractor');
    }

    private function getCache(): CacheInterface
    {
        return ServiceLocator::get('chameleon_system_core.cache');
    }
}
