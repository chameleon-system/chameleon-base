<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CmsBackendBundle\BackendSession\BackendSessionInterface;
use ChameleonSystem\CoreBundle\Interfaces\FlashMessageServiceInterface;
use ChameleonSystem\CoreBundle\SanityCheck\MessageCheckOutput;
use ChameleonSystem\CoreBundle\Security\AuthenticityToken\AuthenticityTokenManagerInterface;
use ChameleonSystem\CoreBundle\Service\BackendBreadcrumbServiceInterface;
use ChameleonSystem\CoreBundle\Service\LanguageServiceInterface;
use ChameleonSystem\CoreBundle\Service\PageServiceInterface;
use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\CoreBundle\Util\InputFilterUtilInterface;
use ChameleonSystem\CoreBundle\Util\UrlUtil;
use ChameleonSystem\DatabaseMigration\Exception\AccessDeniedException;
use ChameleonSystem\DatabaseMigrationBundle\Bridge\Chameleon\Recorder\MigrationRecorderStateHandler;
use ChameleonSystem\SecurityBundle\Service\SecurityHelperAccess;
use ChameleonSystem\ViewRendererBundle\objects\TPkgViewRendererLessCompiler;
use Doctrine\DBAL\Connection;
use esono\pkgCmsCache\CacheInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * fetches the cms header data.
/**/
class MTHeader extends TCMSModelBase
{
    /**
     * {@inheritdoc}
     */
    public function Init()
    {
        $this->CheckTemplateEngineStatus();

        $migrationRecorderStateHandler = $this->getMigrationRecorderStateHandler();
        if (null === $migrationRecorderStateHandler->getCurrentBuildNumber()) {
            $migrationRecorderStateHandler->setCurrentBuildNumber((string) time());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function Execute()
    {
        parent::Execute();

        /** @var SecurityHelperAccess $securityHelper */
        $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);


        $this->data['table_id_cms_tpl_page'] = TTools::GetCMSTableId('cms_tpl_page');
        if (stristr($this->viewTemplate, 'title.view.php')) {
            $this->data['sBackendTitle'] = $this->GetBackendTitle();
        } else {
            if (ACTIVE_TRANSLATION || ACTIVE_BACKEND_TRANSLATION) {
                $this->GetEditLanguagesHTML();
            }
            $this->CheckNavigationRights();

            $this->data['sLogoURL'] = $this->GetLogo();
            $this->data['sQuickLinksHTML'] = '';

            $this->data['clearCacheURL'] = '';
            $this->data['bHeaderIsHidden'] = false;

            if ($securityHelper->isGranted('ROLE_CMS_USER')) {
                $breadcrumb = $this->getBreadcrumbService()->getBreadcrumb();
                $this->data['breadcrumb'] = $breadcrumb->GetBreadcrumb(true);

                $this->mapCacheClearUrl();

                if (array_key_exists('chameleon_header', $_COOKIE) && 'hidden' === $_COOKIE['chameleon_header']) {
                    $this->data['bHeaderIsHidden'] = true;
                }

                $this->data['iTableIDCMSUser'] = TTools::GetCMSTableId('cms_user');
            }

            $this->data['aCustomMenuItems'] = $this->GetCustomNavigationItems();

            if (true === $securityHelper->isGranted('ROLE_CMS_USER')) {
                $this->GetPortalQuickLinks();
            }

            $this->RemoveLock();
        }

        if ($securityHelper->isGranted('IS_IMPERSONATOR')) {
            $this->data['logoutUrl'] = PATH_CMS_CONTROLLER.'?'.$this->getUrlUtil()->getArrayAsUrl([
                    '_switch_user' => '_exit',
                ], '', '&');
        } else {
            /** @var RouterInterface $router */
            $router = ServiceLocator::get('router');
            $this->data['logoutUrl'] = $router->generate('app_logout');
        }



        return $this->data;
    }

    private function mapCacheClearUrl(): void
    {
        $request = $this->getCurrentRequest();
        $baseUri = $request->getRequestUri();
        if (false === \strpos($baseUri, '?')) {
            $prefix = '?';
        } else {
            $prefix = '&';
        }

        $this->data['clearCacheURL'] = $this->getUrlUtil()->getArrayAsUrl([
            'module_fnc['.$this->sModuleSpotName.']' => 'ExecuteAjaxCall',
            '_fnc' => 'ClearCache',
        ], $baseUri.$prefix, '&');
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
                $this->getLogger()->error(
                    sprintf('Error while generating portal links: %s', $e->getMessage()),
                    [
                        'e.message' => $e->getMessage(),
                        'e.file' => $e->getFile(),
                        'e.line' => $e->getLine(),
                    ]
                );
            }
        }

        $this->data['aPortalLinks'] = $aPortalLinks;
    }

    /**
     * @return TdbCmsPortalList|null
     */
    protected function GetPortalList()
    {
        $oCmsPortalList = null;
        /** @var SecurityHelperAccess $securityHelper */
        $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);
        $user = $securityHelper->getUser();
        $portalList = $user?->getPortals();

        if (class_exists('TdbCmsPortalList')) {
            $aUserPortalID = null !== $portalList ? array_keys($portalList) : [];
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
        $oConfig = TdbCmsConfig::GetInstance();

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
     * checks the user rights for header shortcut links.
     */
    protected function CheckNavigationRights()
    {
        /** @var SecurityHelperAccess $securityHelper */
        $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);

        $this->data['showCacheButton'] = $securityHelper->isGranted('CMS_RIGHT_FLUSH_CMS_CACHE');
    }

    public function DefineInterface()
    {
        parent::DefineInterface();
        /** @var SecurityHelperAccess $securityHelper */
        $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);

        if ($securityHelper->isGranted('CMS_RIGHT_FLUSH_CMS_CACHE')) {
            $this->methodCallAllowed[] = 'ClearCache';
        }
        $this->methodCallAllowed[] = 'ChangeEditLanguage';
        $this->methodCallAllowed[] = 'ChangeActiveEditPortal';
        $this->methodCallAllowed[] = 'GetCurrentTransactionInfo';
        $this->methodCallAllowed[] = 'addTabToUrlHistory';
        if ($securityHelper->isGranted('CMS_RIGHT_DBCHANGELOG-MANAGER')) {
            $this->methodCallAllowed[] = 'SwitchLoggingState';
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

            $breadcrumb = $this->getBreadcrumbService()->getBreadcrumb();
            $breadcrumb->AddItem($_params, $name);

            return $breadcrumb->GetBreadcrumb(true);
        }
    }

    /**
     * Empties the CMS cache completely, including the joined static JS/CSS includes.
     *
     * @return string - message to show as toaster
     */
    public function ClearCache()
    {
        $this->getCache()->clearAll();

        $translator = $this->getTranslator();
        // clear compiled less css and resource collection files
        if ($this->global->UserDataExists('clearFiles')) {
            $returnMessage = $translator->trans('chameleon_system_core.cms_module_header.msg_full_cache_cleared');
            $this->clearCacheFiles();
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
        /** @var SecurityHelperAccess $securityHelper */
        $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);

        if ($securityHelper->isGranted('ROLE_CMS_USER')) {
            /** @var BackendSessionInterface $backendSession */
            $backendSession = ServiceLocator::get('chameleon_system_cms_backend.backend_session');

            $currentLanguage = $backendSession->getCurrentEditLanguageIso6391();

            $oCmsConfig = TdbCmsConfig::GetInstance();
            $oAvailableLanguages = $oCmsConfig->GetFieldCmsLanguageList();

            $aAvailableLanguageIds = $oAvailableLanguages->GetIdList();
            $availableEditLanguages = $securityHelper->getUser()?->getAvailableEditLanguages();
            if (null !== $availableEditLanguages && count($availableEditLanguages) > 0) {
                $languageIdListString = implode(', ', array_map(fn(string $languageId) => $this->getDatabaseConnection()->quote($languageId), array_values($availableEditLanguages)));
            } else {
                $languageIdListString = "'-1'";
            }

            $oEditLanguages = TdbCmsLanguageList::GetList(sprintf('SELECT * FROM cms_language WHERE `id` IN (%s) ORDER BY `name`', $languageIdListString));
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
        $editLanguageIso = $this->getInputFilterUtil()->getFilteredGetInput('editLanguageIsoCode');
        if (null === $editLanguageIso) {
            return;
        }
        /** @var BackendSessionInterface $backendSession */
        $backendSession = ServiceLocator::get('chameleon_system_cms_backend.backend_session');
        $backendSession->setCurrentEditLanguageIso6391($editLanguageIso);

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
            $oActiveUser = TCMSUser::GetActiveUser();

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
        $includes[] = '<link href="'.TGlobal::GetStaticURLToWebLib('/iconFonts/fontawesome-free-5.8.1/css/all.css').'" media="screen" rel="stylesheet" type="text/css" />';
        $includes[] = '<link href="'.TGlobal::GetStaticURLToWebLib('/iconFonts/fileIconVectors/file-icon-square-o.css').'" media="screen" rel="stylesheet" type="text/css" />';

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

        $includes[] = '<script src="'.TGlobal::GetStaticURLToWebLib('/javascript/jquery/cookie/jquery.cookie.js').'" type="text/javascript"></script>';

        $sessionTimeout = @ini_get('session.gc_maxlifetime');
        if (!empty($sessionTimeout)) {
            $sessionTimeout = ($sessionTimeout - 60) * 1000; // cut 1min to be sure the logout will be processed before the server kicks the session / convert to milliseconds for JS
            $includes[] = '
      <script type="text/javascript">
        $(document).ready(function() {
          setTimeout("window.location = \''.PATH_CMS_CONTROLLER.'?'.TTools::GetArrayAsURL(array('pagedef' => 'login', 'module_fnc' => array('contentmodule' => 'Logout'))).'\'",'.$sessionTimeout.');
        });
      </script>';
        }

        return $includes;
    }

    /**
     * return an assoc array of parameters that describe the state of the module.
     *
     * @return array
     */
    public function _GetCacheParameters()
    {
        $parameters = parent::_GetCacheParameters();
        /** @var BackendSessionInterface $backendSession */
        $backendSession = ServiceLocator::get('chameleon_system_cms_backend.backend_session');

        $parameters['currentEditLanguage'] = $backendSession->getCurrentEditLanguageIso6391();

        return $parameters;
    }

    private function getDatabaseConnection(): Connection
    {
        return ServiceLocator::get('database_connection');
    }

    private function getInputFilterUtil(): InputFilterUtilInterface
    {
        return ServiceLocator::get('chameleon_system_core.util.input_filter');
    }

    private function getCurrentRequest(): ?Request
    {
        return ServiceLocator::get('request_stack')->getCurrentRequest();
    }

    private function getUrlUtil(): UrlUtil
    {
        return ServiceLocator::get('chameleon_system_core.util.url');
    }

    private function getRedirect(): IcmsCoreRedirect
    {
        return ServiceLocator::get('chameleon_system_core.redirect');
    }

    private function getPageService(): PageServiceInterface
    {
        return ServiceLocator::get('chameleon_system_core.page_service');
    }

    private function getFlashMessages(): FlashMessageServiceInterface
    {
        return ServiceLocator::get('chameleon_system_core.flash_messages');
    }

    private function getLogger(): LoggerInterface
    {
        return ServiceLocator::get('logger');
    }

    private function getLanguageService(): LanguageServiceInterface
    {
        return ServiceLocator::get('chameleon_system_core.language_service');
    }

    private function getFileManager(): IPkgCmsFileManager
    {
        return ServiceLocator::get('chameleon_system_core.filemanager');
    }

    private function getCache(): CacheInterface
    {
        return ServiceLocator::get('chameleon_system_cms_cache.cache');
    }

    private function getCacheDir(): string
    {
        return ServiceLocator::getParameter('kernel.cache_dir');
    }

    private function getMigrationRecorderStateHandler(): MigrationRecorderStateHandler
    {
        return ServiceLocator::get('chameleon_system_database_migration.recorder.migration_recorder_state_handler');
    }

    private function getTranslator(): TranslatorInterface
    {
        return ServiceLocator::get('translator');
    }

    private function getLessCompiler(): TPkgViewRendererLessCompiler
    {
        return ServiceLocator::get('chameleon_system_view_renderer.less_compiler');
    }

    private function getBreadcrumbService(): BackendBreadcrumbServiceInterface
    {
        return ServiceLocator::get('chameleon_system_core.service.backend_breadcrumb');
    }
}
