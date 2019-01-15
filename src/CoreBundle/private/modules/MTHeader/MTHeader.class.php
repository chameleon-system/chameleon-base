<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\Interfaces\FlashMessageServiceInterface;
use ChameleonSystem\CoreBundle\SanityCheck\MessageCheckOutput;
use ChameleonSystem\CoreBundle\Security\AuthenticityToken\AuthenticityTokenManagerInterface;
use ChameleonSystem\CoreBundle\Service\LanguageServiceInterface;
use ChameleonSystem\CoreBundle\Service\PageServiceInterface;
use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\CoreBundle\Util\InputFilterUtilInterface;
use ChameleonSystem\CoreBundle\Util\UrlUtil;
use ChameleonSystem\DatabaseMigration\Constant\MigrationRecorderConstants;
use ChameleonSystem\DatabaseMigration\Exception\AccessDeniedException;
use ChameleonSystem\DatabaseMigrationBundle\Bridge\Chameleon\Recorder\MigrationRecorderStateHandler;
use ChameleonSystem\ViewRendererBundle\objects\TPkgViewRendererLessCompiler;
use Doctrine\DBAL\Connection;
use esono\pkgCmsCache\CacheInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * fetches the cms header data.
/**/
class MTHeader extends TCMSModelBase
{
    /**
     * @deprecated since 6.2.0 - no longer used.
     */
    const DB_LOGGING_STATE = 'DB_LOGGING_STATE';
    /**
     * @deprecated since 6.2.0 - no longer used.
     */
    const CONFIGPARAM_DB_COUNTER = 'dbversion-counter';
    /**
     * @deprecated since 6.2.0 - no longer used.
     */
    const CONFIGPARAM_TIMESTAMP = 'dbversion-timestamp';
    /**
     * @deprecated since 6.2.0 - no longer used.
     */
    const TIMESTAMP_CREATED_IN_SESSION = 'TIMESTAMP_CREATED_IN_SESSION';

    /**
     * {@inheritdoc}
     */
    public function Init()
    {
        $this->data['startTreeID'] = 99;

        $this->CheckTemplateEngineStatus();

        $this->data['oUser'] = TCMSUser::GetActiveUser();

        $sRemoveCommand = $this->global->GetUserData('_rmhist');

        $this->data['pagedef'] = $this->global->GetUserData('pagedef');

        if ('true' == $sRemoveCommand) {
            // if no _histid is given, then we will need to search for one given the current parameters

            if (!$this->global->UserDataExists('_histid')) {
                $parameters = $this->global->GetUserData();
                unset($parameters['_rmhist']);
                $histid = $this->global->GetURLHistory()->FindHistoryId($parameters);
            } else {
                $histid = $this->global->GetUserData('_histid');
            }
            if (false !== $histid) {
                $this->global->GetURLHistory()->Clear($histid);
            } else {
                trigger_error('lookup history id failed in MTHeader. Parameters: '.print_r($parameters, true), E_USER_WARNING);
            }
        }

        $migrationRecorderStateHandler = $this->getMigrationRecorderStateHandler();
        if (null === $migrationRecorderStateHandler->getCurrentBuildNumber()) {
            $migrationRecorderStateHandler->setCurrentBuildNumber((string) time());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function &Execute()
    {
        parent::Execute();

        $this->data['table_id_cms_tpl_page'] = TTools::GetCMSTableId('cms_tpl_page');
        if (stristr($this->viewTemplate, 'title.view.php')) {
            $this->data['sBackendTitle'] = $this->GetBackendTitle();
        } else {
            $this->_LoadUserImage();

            if (ACTIVE_TRANSLATION || ACTIVE_BACKEND_TRANSLATION) {
                $this->GetEditLanguagesHTML();
            }
            $this->CheckNavigationRights();

            /** @var FlashBagInterface $flashBag */
            $flashBag = $this->getCurrentRequest()->getSession()->getFlashBag();
            if ($flashBag->has(MessageCheckOutput::CONSUMER_NAME)) {
                $this->data['check_messages'] = $flashBag->get(MessageCheckOutput::CONSUMER_NAME);
            }

            $this->data['sLogoURL'] = $this->GetLogo();
            $this->data['sQuickLinksHTML'] = '';

            $this->data['clearCacheURL'] = '';
            $this->data['bHeaderIsHidden'] = false;

            if (TGlobal::CMSUserDefined()) {
                $this->data['breadcrumb'] = $this->global->GetURLHistory()->GetBreadcrumb(true);

                $lastHistNode = end($this->data['breadcrumb']);
                reset($this->data['breadcrumb']);
                $lastHistNode = str_replace('_rmhist=true', '_rmhist=false', $lastHistNode['url']);
                $this->data['clearCacheURL'] = $lastHistNode.'&'.urlencode('module_fnc['.$this->sModuleSpotName.']').'=ExecuteAjaxCall&_fnc=ClearCache';

                $this->FetchCounterInformation();

                if (array_key_exists('chameleon_header', $_COOKIE) && 'hidden' === $_COOKIE['chameleon_header']) {
                    $this->data['bHeaderIsHidden'] = true;
                }

                $this->data['iTableIDCMSUser'] = TTools::GetCMSTableId('cms_user');
            }

            $this->data['aCustomMenuItems'] = $this->GetCustomNavigationItems();

            if (TGlobal::CMSUserDefined()) {
                $this->GetPortalQuickLinks();
            }

            $this->RemoveLock();
        }

        return $this->data;
    }

    /**
     * generates the backend title.
     *
     * @return string
     */
    protected function GetBackendTitle()
    {
        $sBackendTitle = '';

        $oConfig = TdbCmsConfig::GetInstance();
        $sCMSOwner = $oConfig->GetName();

        if (!empty($sCMSOwner)) {
            $sBackendTitle .= $sCMSOwner.' - ';
        }
        if (isset($this->data['oUser'])) {
            $sBackendTitle .= CMS_BACKEND_TITLE.' V. '.CMS_VERSION_MAJOR.'.'.CMS_VERSION_MINOR.' (IP: '.$_SERVER['SERVER_ADDR'].')';
        } else {
            $sBackendTitle .= CMS_BACKEND_TITLE;
        }

        return $sBackendTitle;
    }

    /**
     * checks referrer if last opened page was a tableEditor the record lock
     * will be removed (if activated).
     */
    protected function RemoveLock()
    {
        if (false === isset($_SERVER['HTTP_REFERER']) || empty($_SERVER['HTTP_REFERER'])) {
            return;
        }
        $sReferer = $_SERVER['HTTP_REFERER'];
        if (false === stristr($sReferer, 'pagedef=tableeditor')) {
            return;
        }
        $aURLParts = parse_url($sReferer);
        if (false === isset($aURLParts['query']) || empty($aURLParts['query'])) {
            return;
        }
        $sLastOpenRecordID = null;
        $sLastOpenTableID = null;

        $aParams = explode('&', $aURLParts['query']);
        foreach ($aParams as $sParam) {
            if ('id=' === substr($sParam, 0, 3)) {
                $sLastOpenRecordID = substr($sParam, 3);
            } elseif ('tableid=' === substr($sParam, 0, 8)) {
                $sLastOpenTableID = substr($sParam, 8);
            }
        }

        if (null === $sLastOpenRecordID || null === $sLastOpenTableID) {
            return;
        }
        $oTableEditor = new TCMSTableEditorManager();
        if ($oTableEditor->Init($sLastOpenTableID, $sLastOpenRecordID)) {
            $oTableEditor->RemoveLock();
        }
    }

    /**
     * use hook to add custom navigation items to the beginning of the navigation list
     * format: array(0=>array('width'=>'', 'naviLink'=>'', 'iconUrl'=>'','name'=>'',),1=>array...).
     *
     * @return array
     */
    protected function GetCustomNavigationItems()
    {
        return array();
    }

    /**
     * loads the portal list and renders a select box or href of all portal home links.
     */
    protected function GetPortalQuickLinks()
    {
        $oCmsPortalList = $this->GetPortalList();
        $aPortalLinks = array();

        if (null !== $oCmsPortalList) {
            $activeLanguage = $this->getLanguageService()->getActiveLanguage();
            try {
                while ($portal = $oCmsPortalList->Next()) {
                    $activeLanguagesForPortal = $portal->GetActiveLanguages();
                    if ($activeLanguagesForPortal->IsInList($activeLanguage->id)) {
                        $language = $activeLanguage;
                    } else {
                        $language = $portal->GetFieldCmsLanguage();
                    }
                    $sURL = $this->getPageService()->getLinkToPortalHomePageAbsolute(array(), $portal, $language);
                    $sName = trim($portal->fieldTitle);
                    if (empty($sName)) {
                        $sName = $portal->GetName();
                    }
                    $aPortalLinks[$portal->id] = array('name' => $sName, 'url' => $sURL);
                }
            } catch (Exception $e) {
                $this->getFlashMessages()->addBackendToasterMessage('chameleon_system_core.cms_module_header.error_generate_portal_links');
                $this->getLogger()->error('Error while generating portal links', __FILE__, __LINE__, array(
                    'e.message' => $e->getMessage(),
                    'e.file' => $e->getFile(),
                    'e.line' => $e->getLine(),
                ));
            }
        }

        $this->data['aPortalLinks'] = $aPortalLinks;
    }

    /**
     * @return null|TdbCmsPortalList
     */
    protected function GetPortalList()
    {
        $oCmsPortalList = null;
        $oCMSUser = &TCMSUser::GetActiveUser();
        if (class_exists('TdbCmsPortalList')) {
            $aUserPortalID = $oCMSUser->GetMLTIdList('cms_portal', 'cms_portal_mlt');
            if (count($aUserPortalID) > 0) {
                $databaseConnection = $this->getDatabaseConnection();
                $idListString = implode(',', array_map(array($databaseConnection, 'quote'), $aUserPortalID));
                $sQuery = "SELECT * FROM `cms_portal` WHERE `cms_portal`.`id` IN ($idListString)";
                $oCmsPortalList = TdbCmsPortalList::GetList($sQuery);
            }
        }

        $this->data['oCmsPortalList'] = $oCmsPortalList;

        return $oCmsPortalList;
    }

    /**
     * returns the url to the logo
     * overwrite this to add your custom logo.
     *
     * @return string
     */
    protected function GetLogo()
    {
        $oConfig = &TdbCmsConfig::GetInstance();

        return $oConfig->GetThemeURL().'/images/chameleon_logo_header.png';
    }

    /**
     * checks if the template engine button should show up in the cms header navi
     * available via: $data['show_template_engine'] in views.
     */
    protected function CheckTemplateEngineStatus()
    {
        $this->data['show_template_engine'] = TdbCmsConfig::GetInstance()->fieldShowTemplateEngine;
    }

    /**
     * checks the user rights for header shortcut links to document manager, media manager, navigation...
     */
    protected function CheckNavigationRights()
    {
        $userIsInWebsiteEditGroup = false;
        $userHasWebsiteEditRight = false;

        $currentUser = &TCMSUser::GetActiveUser();
        if (null === $currentUser) {
            $this->data['showWebsiteEditNavi'] = false;

            return;
        }

        $databaseConnection = $this->getDatabaseConnection();
        $query = "SELECT `id` FROM `cms_usergroup` WHERE `internal_identifier` = 'website_editor'";
        $websiteEditorGroupId = $databaseConnection->fetchColumn($query);

        if (false !== $websiteEditorGroupId) {
            $userIsInWebsiteEditGroup = $currentUser->oAccessManager->user->IsInGroups($websiteEditorGroupId);
            $userHasWebsiteEditRight = $currentUser->oAccessManager->HasEditPermission('cms_tpl_page');
        }

        $this->data['showWebsiteEditNavi'] = $userIsInWebsiteEditGroup && $userHasWebsiteEditRight;
        $this->data['showImageManagerNavi'] = ($currentUser->oAccessManager->PermitFunction('cms_image_pool_upload') || $currentUser->oAccessManager->PermitFunction('cms_image_pool_delete'));
        $this->data['showDocumentManagerNavi'] = ($currentUser->oAccessManager->PermitFunction('cms_data_pool_upload') || $currentUser->oAccessManager->PermitFunction('cms_datei_pool_delete'));
        $this->data['showCacheButton'] = $currentUser->oAccessManager->PermitFunction('flush_cms_cache');
        $this->data['showNaviManager'] = $currentUser->oAccessManager->HasEditPermission('cms_tree');
    }

    public function DefineInterface()
    {
        parent::DefineInterface();
        $oUser = &TCMSUser::GetActiveUser();
        if ($oUser && $oUser->oAccessManager && $oUser->oAccessManager->PermitFunction('flush_cms_cache')) {
            $this->methodCallAllowed[] = 'ClearCache';
        }
        $this->methodCallAllowed[] = 'ChangeEditLanguage';
        $this->methodCallAllowed[] = 'ChangeActiveEditPortal';
        $this->methodCallAllowed[] = 'GetCurrentTransactionInfo';
        $this->methodCallAllowed[] = 'addTabToUrlHistory';
        if ($oUser && $oUser->oAccessManager && $oUser->oAccessManager->PermitFunction('dbchangelog-manager')) {
            $this->methodCallAllowed[] = 'ChangeActiveDbCounter';
            $this->methodCallAllowed[] = 'SwitchLoggingState';
            $this->methodCallAllowed[] = 'AddCounter';
            $this->methodCallAllowed[] = 'UpdateUnixTimeStamp';
        }
    }

    /**
     * @return int
     */
    public function UpdateUnixTimeStamp()
    {
        $newtime = time();
        $this->getMigrationRecorderStateHandler()->setCurrentBuildNumber((string) $newtime);

        return $newtime;
    }

    public function addTabToUrlHistory()
    {
        $url = $this->global->GetUserData('url');
        $name = $this->global->GetUserData('name');

        if (isset($url) && !empty($url) && isset($name) && !empty($name)) {
            $urlParts = parse_url($url);

            parse_str($urlParts['query'], $params);

            $_params = array();
            $_params['pagedef'] = $params['pagedef'];
            $_params['tableid'] = $params['tableid'];
            $_params['id'] = $params['id'];

            if (array_key_exists('sRestriction', $params)) {
                $_params['sRestriction'] = $params['sRestriction'];
            }

            if (array_key_exists('sRestrictionField', $params)) {
                $_params['sRestrictionField'] = $params['sRestrictionField'];
            }

            if (array_key_exists('fragment', $urlParts)) {
                $_params['fragment'] = $urlParts['fragment'];
            }

            $urlHistory = $this->global->GetURLHistory();
            $urlHistory->AddItem($_params, $name);

            return $urlHistory->GetBreadcrumb(true);
        }
    }

    /**
     * loads the user image or a default icon.
     */
    protected function _LoadUserImage()
    {
        $currentUser = TCMSUser::GetActiveUser();
        if (null === $currentUser) {
            return;
        }
        $userImage = TGlobal::GetPathTheme().'/images/nav_icons/user.gif';

        $imageID = TCMSUser::GetActiveUser()->fieldImages;
        if ($imageID >= 1000 || !is_numeric($imageID)) {
            $oImage = new TCMSImage();
            if (null !== $oImage) {
                $oImage->Load($imageID);
                $oThumb = $oImage->GetSquareThumbnail(40);
                if (null !== $oThumb) {
                    $userImage = $oThumb->GetFullURL();
                }
            }
        }

        $this->data['userImage'] = $userImage;
    }

    /**
     * Empties the CMS cache completely, including the joined static JS/CSS includes.
     * Note: This method is independent from the deprecated method in TModelBase. Do not use it interchangeably and do
     * not remove it when removing the parent method.
     *
     * @return string - message to show as toaster
     */
    public function ClearCache()
    {
        $this->getCache()->clearAll();

        $translator = $this->getTranslator();
        // clear compiled less css and resource collection files
        if ($this->global->UserDataExists('clearFiles')) {
            $this->clearCacheFiles();
            $returnMessage = $translator->trans('chameleon_system_core.cms_module_header.msg_full_cache_cleared');
        } else {
            $returnMessage = $translator->trans('chameleon_system_core.cms_module_header.msg_page_object_cache_cleared');
        }

        return $returnMessage;
    }

    /**
     *  delete the file in directory PATH_OUTBOX that is defined in \private\config\advanced_config.inc.php.
     *
     * @var string $sDir - is sub directory in PATH_OUTBOX that will be cleared
     */
    protected function ClearOutBox($sDir)
    {
        if (false === is_dir(PATH_OUTBOX)) {
            return;
        }
        $sDir = PATH_OUTBOX.$sDir;

        $this->clearFilesInDir($sDir);
    }

    /**
     * Delete all the files in the given dir - except .gitkeep files.
     *
     * @param string $dir
     *
     * NOTE also see deleteFileOrDirectoryContent() which does something similar
     */
    private function clearFilesInDir(string $dir): void
    {
        $dir = rtrim($dir, '/').'/';

        $fileManager = $this->getFileManager();
        $files = array_values(preg_grep('/^((?!.gitkeep).)*$/', glob($dir.'*.*')));
        if ($files) {
            foreach ($files as $file) {
                $fileManager->unlink($file);
            }
        }
    }

    private function clearCacheFiles()
    {
        $this->ClearOutBox('static/js/');
        $this->ClearOutBox('static/css/');

        $lessCompiler = $this->getLessCompiler();
        $cssTargetDir = $lessCompiler->getLocalPathToCompiledLess();
        $this->clearFilesInDir($cssTargetDir);
        $cssCacheDir = $lessCompiler->getLocalPathToCachedLess();
        $this->clearFilesInDir($cssCacheDir);

        $oConfig = TdbCmsConfig::GetInstance();
        $aAdditionalFiles = explode("\n", $oConfig->fieldAdditionalFilesToDeleteFromCache);
        foreach ($aAdditionalFiles as $path) {
            $path = trim($path);
            if ('' === $path) {
                continue;
            }
            if (false === $this->isCleanPath($path)) {
                continue;
            }
            $this->deleteFileOrDirectoryContent(realpath($path));
        }

        $fileManager = $this->getFileManager();
        $cacheDir = $this->getCacheDir();

        // always clear cache dir as well
        $oldCache = realpath($cacheDir).'-'.time();
        $fileManager->move($cacheDir, $oldCache);
        $fileManager->mkdir($cacheDir);
        $fileManager->mkdir($cacheDir.'/raw');
        $fileManager->deldir($oldCache, true);
    }

    /**
     * @param string $sPath
     *
     * @return bool
     */
    private function isCleanPath($sPath)
    {
        $sBase = realpath(PATH_PROJECT_BASE);
        // the path must not contain anything that allows it to esacpe
        $fullPath = PATH_WEB.'/'.$sPath;
        do {
            $tmpPath = realpath($fullPath);
            if (substr($tmpPath, 0, strlen($sBase)) == $sBase) {
                return true;
            }
            $lastSlash = strrpos($fullPath, '/');
            if (false === $lastSlash) {
                $fullPath = '';
            } else {
                $fullPath = substr($fullPath, 0, $lastSlash);
            }
        } while ('' !== $fullPath);

        return false;
    }

    /**
     * @param string $path
     */
    private function deleteFileOrDirectoryContent($path)
    {
        if (false === file_exists($path) && false === is_link($path)) {
            return;
        }
        if (is_dir($path)) {
            if (false === ($handle = opendir($path))) {
                return;
            }
            while (false !== ($file = readdir($handle))) {
                $this->deleteFileIfAllowed($path.DIRECTORY_SEPARATOR.$file);
            }
            closedir($handle);
        } else {
            $this->deleteFileIfAllowed($path);
        }
    }

    /**
     * @param string $path
     */
    private function deleteFileIfAllowed($path)
    {
        if (false === is_file($path) && false === is_link($path)) {
            return;
        }
        if ('.gitkeep' === basename($path)) {
            return;
        }
        $this->getFileManager()->unlink($path);
    }

    /**
     * adds a counter (inserts a new record).
     *
     * @param null|string $sSystemName
     * @param string      $sName
     * @param int         $iValue
     *
     * @return stdClass
     *
     * @deprecated since 6.2.0 - no longer used.
     */
    public function AddCounter($sSystemName = null, $sName = '', $iValue = 0)
    {
        $oResponse = new stdClass();
        $oResponse->sToasterMessage = '';

        return $oResponse;
    }

    /**
     * changes the active database-counter.
     *
     * @return string - message to show as toaster
     *
     * @deprecated since 6.2.0 - no longer used.
     */
    public function ChangeActiveDbCounter()
    {
        return json_encode([
            'newCounter' => MigrationRecorderConstants::MIGRATION_SCRIPT_NAME,
            'toasterMessage' => '',
        ]);
    }

    /**
     * fetches counter list and active counter and sets them into data array.
     *
     * @deprecated since 6.2.0 - no longer used.
     */
    protected function FetchCounterInformation()
    {
        $this->data['changeActiveDbCounterURL'] = '#';
        $this->data['sAddCounterURL'] = '#';
        $this->data['oActiveCounter'] = TdbCmsConfigParameter::GetNewInstance();
        $this->data['oCounterList'] = &TdbCmsConfigParameterList::GetList();
    }

    /**
     * ajax method for changing the current db logging state.
     *
     * @return string
     */
    protected function SwitchLoggingState()
    {
        $migrationRecorderStateHandler = $this->getMigrationRecorderStateHandler();
        $isActive = $migrationRecorderStateHandler->isDatabaseLoggingActive();
        try {
            $migrationRecorderStateHandler->toggleDatabaseLogging();
            $isActive = $migrationRecorderStateHandler->isDatabaseLoggingActive();
            $toasterMessage = true === $isActive
                ? 'chameleon_system_core.cms_module_header.migration_recording_activated'
                : 'chameleon_system_core.cms_module_header.migration_recording_deactivated';
        } catch (AccessDeniedException $e) {
            $toasterMessage = 'chameleon_system_core.error.access_denied';
        }

        $toasterMessage = $this->getTranslator()->trans($toasterMessage);

        return json_encode([
          'enabled' => $isActive,
          'toasterMessage' => $toasterMessage,
        ]);
    }

    /**
     * generates a select box with all available edit languages for the current user.
     */
    protected function GetEditLanguagesHTML()
    {
        $html = '';
        $editLanguages = array();
        $currentLanguage = '';
        $currentUser = &TCMSUser::GetActiveUser();
        if (null !== $currentUser) {
            $currentLanguage = $currentUser->GetCurrentEditLanguage();

            $oCmsConfig = TdbCmsConfig::GetInstance();
            $oAvailableLanguages = $oCmsConfig->GetFieldCmsLanguageList();

            $aAvailableLanguageIds = $oAvailableLanguages->GetIdList();
            $oEditLanguages = $currentUser->GetMLT('cms_language_mlt');
            while ($oEditLanguage = $oEditLanguages->Next()) {
                if (false === in_array($oEditLanguage->id, $aAvailableLanguageIds) && $oEditLanguage->id != $oCmsConfig->fieldTranslationBaseLanguageId) {
                    continue;
                }
                $isoLang = strtoupper($oEditLanguage->sqlData['iso_6391']);

                $selected = '';
                if (!empty($currentLanguage) && strtoupper($currentLanguage) == $isoLang) {
                    $selected = ' selected="selected"';
                }

                $html .= '<option value="'.TGlobal::OutHTML($isoLang)."\"{$selected}>".TGlobal::OutHTML($oEditLanguage->GetName())."</option>\n";
                $editLanguages[$isoLang] = $oEditLanguage->GetName();
            }
        }
        $this->data['editLanguageOptions'] = $html;
        $this->data['editLanguages'] = $editLanguages;
        $this->data['activeEditLanguageIso'] = $currentLanguage;
    }

    /**
     * ajax method to change the current edit language.
     */
    public function ChangeEditLanguage()
    {
        $editLanguageId = $this->getInputFilterUtil()->getFilteredGetInput('editLanguageID');
        if (null === $editLanguageId) {
            return;
        }
        $language = strtolower($editLanguageId);
        $user = TCMSUser::GetActiveUser();
        $user->SetCurrentEditLanguage($language);
        // we need to redirect to the current page to ensure the change from taking hold
        // now call page again... but without module_fnc
        $authenticityTokenId = AuthenticityTokenManagerInterface::TOKEN_ID;
        $parameterList = $this->global->GetUserData(null, [
            'module_fnc',
            'editLanguageID',
            '_noModuleFunction',
            '_fnc',
            $authenticityTokenId,
            ]);
        $url = PATH_CMS_CONTROLLER.$this->getUrlUtil()->getArrayAsUrl($parameterList, '?', '&');
        $this->getRedirect()->redirect($url);
    }

    /**
     * ajax method to change the current edit language.
     */
    public function ChangeActiveEditPortal()
    {
        if ($this->global->UserDataExists('activePortalID')) {
            $portalID = $this->global->GetUserData('activePortalID');
            $oActiveUser = &TCMSUser::GetActiveUser();

            $oActiveUser->SetActiveEditPortalID($portalID);
        } else {
            unset($_SESSION['_cms_ActiveEditPortalID']);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function GetHtmlHeadIncludes()
    {
        $includes = parent::GetHtmlHeadIncludes();
        $includes[] = '<link href="'.TGlobal::GetPathTheme().'/images/favicon.ico" rel="shortcut icon" />';
        $includes[] = '<link href="'.TGlobal::GetStaticURLToWebLib('/javascript/jquery/jQueryUI/themes/cupertino/cupertino.css').'" media="screen" rel="stylesheet" type="text/css" />';
        $includes[] = '<link href="/chameleon/blackbox/bootstrap/css/glyph-icons.css?v4.1" media="screen" rel="stylesheet" type="text/css" />';
        $includes[] = '<link href="/chameleon/blackbox/iconFonts/fontawesome-free-5.5.0/css/all.css" media="screen" rel="stylesheet" type="text/css" />';

        return $includes;
    }

    /**
     * {@inheritdoc}
     */
    public function GetHtmlFooterIncludes()
    {
        $includes = parent::GetHtmlFooterIncludes();

        if (false === TGlobal::CMSUserDefined()) {
            return $includes;
        }

        $includes[] = '<script src="'.TGlobal::GetStaticURLToWebLib('/javascript/jquery/jQueryUI/ui.core.js').'" type="text/javascript"></script>';
        $includes[] = '<script src="'.TGlobal::GetStaticURLToWebLib('/javascript/jquery-form-4.2.2/jquery.form.min.js').'" type="text/javascript"></script>'; // ajax form plugin
        $includes[] = '<script src="'.TGlobal::GetStaticURLToWebLib('/javascript/jquery/cookie/jquery.cookie.js').'" type="text/javascript"></script>';
        $includes[] = '<link href="/chameleon/blackbox/iconFonts/foundation/foundation-icons.css" media="screen" rel="stylesheet" type="text/css" />';
        $includes[] = '<link href="/chameleon/blackbox/iconFonts/ionicons/ionicons.css" media="screen" rel="stylesheet" type="text/css" />';

        $sessionTimeout = @ini_get('session.gc_maxlifetime');
        if (!empty($sessionTimeout)) {
            $sessionTimeout = ($sessionTimeout - 60) * 1000; // cut 1min to be sure the logout will be processed before the server kicks the session / convert to milliseconds for JS
            $includes[] = '
      <script type="text/javascript">
        $(document).ready(function() {
          setTimeout("window.location = \'' . PATH_CMS_CONTROLLER . '?' . TTools::GetArrayAsURL(array('pagedef' => 'login', 'module_fnc' => array('contentmodule' => 'Logout'))) . '\'",' . $sessionTimeout . ');
        });
      </script>';
        }

        return $includes;
    }

    /**
     * loads the active workflow transaction object and returns an object used for ajax calls.
     *
     * @return stdClass
     *
     * @deprecated since 6.2.0 - workflow is not supported anymore
     */
    public function GetCurrentTransactionInfo()
    {
        return new stdClass();
    }

    /**
     * return an assoc array of parameters that describe the state of the module.
     *
     * @return array
     */
    public function _GetCacheParameters()
    {
        $parameters = parent::_GetCacheParameters();
        $currentUser = TCMSUser::GetActiveUser();
        if (null !== $currentUser) {
            $parameters['currentEditLanguage'] = $currentUser->GetCurrentEditLanguage();
        }

        return $parameters;
    }

    /**
     * @return Connection
     */
    private function getDatabaseConnection()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('database_connection');
    }

    /**
     * @return InputFilterUtilInterface
     */
    private function getInputFilterUtil()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.util.input_filter');
    }

    /**
     * @return null|\Symfony\Component\HttpFoundation\Request
     */
    private function getCurrentRequest()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('request_stack')->getCurrentRequest();
    }

    /**
     * @return UrlUtil
     */
    private function getUrlUtil()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.util.url');
    }

    /**
     * @return IcmsCoreRedirect
     */
    private function getRedirect()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.redirect');
    }

    /**
     * @return PageServiceInterface
     */
    private function getPageService()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.page_service');
    }

    /**
     * @return FlashMessageServiceInterface
     */
    private function getFlashMessages()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.flash_messages');
    }

    /**
     * @return IPkgCmsCoreLog
     */
    private function getLogger()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('cmspkgcore.logchannel.standard');
    }

    /**
     * @return LanguageServiceInterface
     */
    private function getLanguageService()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.language_service');
    }

    /**
     * @return IPkgCmsFileManager
     */
    private function getFileManager()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.filemanager');
    }

    /**
     * @return CacheInterface
     */
    private function getCache()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_cms_cache.cache');
    }

    /**
     * @return string
     */
    private function getCacheDir()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::getParameter('kernel.cache_dir');
    }

    /**
     * @return MigrationRecorderStateHandler
     */
    private function getMigrationRecorderStateHandler()
    {
        return ServiceLocator::get('chameleon_system_database_migration.recorder.migration_recorder_state_handler');
    }

    /**
     * @return TranslatorInterface
     */
    private function getTranslator()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('translator');
    }

    private function getLessCompiler(): TPkgViewRendererLessCompiler
    {
        return ServiceLocator::get('chameleon_system_view_renderer.less_compiler');
    }
}
