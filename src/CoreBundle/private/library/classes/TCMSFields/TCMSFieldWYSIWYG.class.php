<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\Util\UrlUtil;
use ChameleonSystem\CoreBundle\Wysiwyg\CkEditorConfigProviderInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * WYSIWYG text field.
 *
 * you may set the field config variable "disableButtons" in CMS field configuration
 * or global via constant: CHAMELEON_WYSIWYG_DISABLED_BUTTONS
 *
 * @see http://docs.cksource.com/CKEditor_3.x/Developers_Guide/Toolbar for detailed list
 *
 * it`s possible to overwrite the CSS URL by setting: css=[{portalurl}]/pathtowysiwyg.css
 * in field configuration
 *
 * /**/
class TCMSFieldWYSIWYG extends TCMSFieldText
{
    private $sEditorHeight = '530px';

    private $sEditorWidth = null;

    public function GetHTML()
    {
        parent::GetHTML();
        $oViewRenderer = new ViewRenderer();
        $oViewRenderer->AddSourceObject('sEditorName', 'fieldcontent_'.$this->sTableName.'_'.$this->name);
        $oViewRenderer->AddSourceObject('sFieldName', $this->name);
        $oViewRenderer->AddSourceObject('extraPluginsConfiguration', $this->getExtraPluginsConfiguration());
        $oViewRenderer->AddSourceObject('aEditorSettings', $this->getEditorSettings());
        $sUserCssUrl = $this->getEditorCSSUrl();
        if ('' !== $sUserCssUrl) {
            $aStyles = array();
            try {
                $aStyles = $this->getJSStylesSet($sUserCssUrl);
            } catch (Exception $e) {
                $oViewRenderer->AddSourceObject('couldNotLoadCustomCss', true);
                $oViewRenderer->AddSourceObject('customCssUrl', $sUserCssUrl);
            }

            $oViewRenderer->AddSourceObject('aStyles', $aStyles);
        }
        $oViewRenderer->AddSourceObject('data', $this->data);

        return $oViewRenderer->Render('TCMSFieldWYSIWYG/cKEditor/editor.html.twig', null, false);
    }

    /**
     * renders the read only view of the field.
     *
     * @return string
     */
    public function GetReadOnly()
    {
        parent::GetReadOnly();
        $html = $this->GetHTML();

        return $html;
    }

    /**
     * @return array
     */
    private function getDefaultEditorSettings()
    {
        $aEditorSettings = array();

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
            $aIncludes = array();
        }
        $aIncludes[] = '<script src="'.URL_CMS.'/components/ckEditor/ckeditor/ckeditor.js" type="text/javascript"></script>';
        $aIncludes[] = '<script type="text/javascript" src="'.TGlobal::GetStaticURL(
                '/chameleon/blackbox/javascript/CKEditor/chameleon.ckeditor.js'
            ).'"></script>';

        return $aIncludes;
    }

    /**
     * @param array $aToolbar
     *
     * @return string
     */
    private function convertToolbar($aToolbar)
    {
        $sToolbar = '';
        $iCount = 0;
        foreach ($aToolbar as $mSection) {
            ++$iCount;
            if ($this->toolbarOptionIsValid($mSection)) {
                $sToolbar .= '{ ';
                $sToolbar .= "name: '".$mSection['name']."',";
                $sToolbar .= 'items: [ ';
                $iSubCount = 0;
                foreach ($mSection['items'] as $sItem) {
                    ++$iSubCount;
                    $sToolbar .= "'".$sItem."'";
                    if ($iSubCount < count($mSection['items'])) {
                        $sToolbar .= ',';
                    }
                }
                $sToolbar .= ' ]';
                $sToolbar .= ' }';
            } else {
                $sToolbar .= "'".$mSection."'";
            }
            if ($iCount < count($aToolbar)) {
                $sToolbar .= ',';
            }
        }

        return '[ '.$sToolbar.' ]';
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

    /**
     * @param array $aOption
     *
     * @return bool
     */
    private function toolbarOptionIsValid($aOption)
    {
        return is_array($aOption) && isset($aOption['name']) && isset($aOption['items']);
    }

    /**
     * @param TCMSUser $oUser
     * @param array    $aToolbar
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
     * @param array  $aToolbar
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
     * @param array  $aToolbar
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

    /**
     * @param array $aDisabledPlugins
     *
     * @return string
     */
    private function convertDisabledPlugins($aDisabledPlugins)
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
     *
     * @return string
     */
    private function getLanguageCode()
    {
        /** @var $oUser TdbCmsUser */
        $oUser = TdbCmsUser::GetActiveUser();
        $oBackendLanguage = $oUser->GetFieldCmsLanguage();

        return $oBackendLanguage->fieldIso6391;
    }

    /**
     * enterMode defines how line break will be handled by the editor, this is defined by CHAMELEON_WYSIWYG_LINE_ENDINGS constant
     * possible values are DIV, BR and P default (and highly recommended) is P
     * this values will be translated into CKEDITOR.ENTER_DIV, CKEDITOR.ENTER_BR, and CKEDITOR.ENTER_P.
     *
     * @return string
     */
    private function getEnterMode()
    {
        switch (CHAMELEON_WYSIWYG_LINE_ENDINGS) {
            case 'DIV':
                return 'CKEDITOR.ENTER_DIV';
                break;
            case 'BR':
                return 'CKEDITOR.ENTER_BR';
                break;
            case 'P':
            default:
                return 'CKEDITOR.ENTER_P';
                break;
        }
    }

    /**
     * Loads a custom CSS file for the editor based on CMS config, portal, template or field configuration (in this order).
     *
     * @return string
     */
    private function getEditorCSSUrl()
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
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('request_stack')->getCurrentRequest();
    }

    /**
     * @return UrlUtil
     */
    protected function getUrlUtilService()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.util.url');
    }

    /**
     * @return string
     */
    private function getTemplateCssUrl()
    {
        $connectedPage = $this->getConnectedPageForCurrentRecord();

        if (null === $connectedPage) {
            return '';
        }

        if ('' === $connectedPage->fieldCmsMasterPagedefId) {
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
     *
     * @return string
     */
    private function getPortalCssUrl()
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

    /**
     * @param string $sPortalCssUrl
     *
     * @return string
     */
    private function getUserCssUrl($sPortalCssUrl)
    {
        $fieldSpecificCustomCssUrl = $this->oDefinition->GetFieldtypeConfigKey('css');
        if (null === $fieldSpecificCustomCssUrl) {
            return '';
        }

        if (false !== strpos($fieldSpecificCustomCssUrl, '[{portalurl}]')) {
            if ('' !== $sPortalCssUrl && '.css' !== substr($sPortalCssUrl, -4)) {
                // it doesn`t end with a CSS file, so it`s a prefix (e.g. /css/portalname/)

                $fieldSpecificCustomCssUrl = str_replace('[{portalurl}]', $sPortalCssUrl, $fieldSpecificCustomCssUrl);

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
     * transforms css file path to a usable stylesSet name.
     *
     * @param string $sUserCssUrl
     *
     * @return string
     */
    private function getUniqueStylesSetName($sUserCssUrl)
    {
        return str_replace(array(':', '.', '/', '-'), '_', $sUserCssUrl);
    }

    /**
     * parse user css file and translate the styles for usage in javascript array collection / map.
     *
     * @param string $sUserCssUrl
     *
     * @return array
     */
    private function getJSStylesSet($sUserCssUrl)
    {
        $aStyles = array();

        $aCustomCSSClasses = $this->GetWYSIWYGCustomerStyles($sUserCssUrl);
        foreach ($aCustomCSSClasses as $sClassName) {
            $aStyle = array();
            if ('@' == substr($sClassName, 0, 1)) {
                continue;
            }
            if ('.' == substr($sClassName, 0, 1)) {
                $sClassName = substr($sClassName, 1);
                $aStyle['name'] = "'".$sClassName."'";
                $aStyle['element'] = "['p', 'div', 'span', 'a', 'h1', 'h2', 'h3', 'h4', 'ul', 'ol', 'li']";
                $aStyle['attributes']['class'] = "'".$sClassName."'";
            } else {
                // split tag type from css. note: we only allow one class!
                $aClassParts = explode('.', $sClassName);
                if (2 == count($aClassParts)) {
                    $sElement = $aClassParts[0];
                    $sElementSubParts = explode(' ', $sElement);
                    $sElement = $sElementSubParts[count($sElementSubParts) - 1];
                    $sClassName = $aClassParts[1];
                    $aStyle['name'] = "'".$sClassName.'('.$sElement.")'";
                    $aStyle['element'] = "'".$sElement."'";
                    $aStyle['attributes']['class'] = "'".$sClassName."'";
                }
            }

            if (count($aStyle) > 0) {
                $aStyles[] = $aStyle;
            }
        }

        return $aStyles;
    }

    /**
     * @param string $sUserCSSURL
     *
     * @return array
     */
    protected function GetWYSIWYGCustomerStyles($sUserCSSURL)
    {
        static $aStyleCache;
        if (!$aStyleCache) {
            $aParameters = array('class' => 'CSSTree', 'cssurl' => $sUserCSSURL);
            $cache = \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.cache');
            $key = $cache->getKey($aParameters, false);
            $aStyleCache = $cache->get($key);
            if (null === $aStyleCache) {
                $aStyles = TTools::GetClassNamesFromCSSFile($sUserCSSURL);
                $aStyleCache = $aStyles;
                $cache->set($key, $aStyleCache, null);
            }
        }

        return $aStyleCache;
    }

    /**
     * returns the length of a field
     * sets field max-width and field CSS width.
     */
    public function _GetFieldWidth()
    {
        if (0 != $this->oDefinition->sqlData['field_width']) {
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
        return $this->sEditorHeight;
    }

    /**
     * @return string
     */
    protected function getEditorWidth()
    {
        if (null === $this->sEditorWidth) {
            if (!empty($this->fieldCSSwidth)) {
                $sEditorWidth = $this->fieldCSSwidth;
            } else {
                $sEditorWidth = '100%';
            }
            $this->setEditorWidth($sEditorWidth);

            return $sEditorWidth;
        } else {
            return $this->sEditorWidth;
        }
    }

    /**
     * @param $sEditorHeight
     */
    protected function setEditorHeight($sEditorHeight)
    {
        $this->sEditorHeight = $sEditorHeight;
    }

    /**
     * @param $sEditorWidth
     */
    protected function setEditorWidth($sEditorWidth)
    {
        $this->sEditorWidth = $sEditorWidth;
    }

    /**
     * @return CkEditorConfigProviderInterface
     */
    private function getCkeditorConfigProvider()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.wysiwyg.ckeditor_config_provider');
    }
}
