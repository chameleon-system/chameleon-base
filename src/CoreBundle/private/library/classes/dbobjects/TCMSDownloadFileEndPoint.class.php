<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\DataModel\DownloadLinkDataModel;
use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\CoreBundle\Util\UrlNormalization\UrlNormalizationUtil;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class TCMSDownloadFileEndPoint extends TCMSRecord
{
    protected $_cacheParameter = array();
    protected $_outboxFolder = null;

    /**
     * SEO filename used in outbox folder for symlinks.
     *
     * @var string|null
     */
    protected $sSEOFileName = null;

    /**
     * full SEO URL path to symlink in outbox folder.
     *
     * @var string|null
     */
    public $fileURL = null;

    public function __construct($id = null)
    {
        parent::__construct('cms_document', $id);
    }

    /**
     * @deprecated Named constructors are deprecated and will be removed with PHP8. When calling from a parent, please use `parent::__construct` instead.
     * @see self::__construct
     */
    public function TCMSDownloadFileEndPoint()
    {
        $this->callConstructorAndLogDeprecation(func_get_args());
    }

    /**
     * returns the real file path on the server to the file.
     *
     * @return string
     */
    public function GetRealPath()
    {
        return PATH_CMS_CUSTOMER_DATA.'/'.$this->GetRealFileName();
    }

    /**
     * returns the name of the file on the server.
     *
     * @return string
     */
    public function GetRealFileName()
    {
        $oFileType = &$this->GetFileType();

        return $this->id.'.'.$oFileType->sqlData['file_extension'];
    }

    /**
     * returns sanitized SEO filename.
     *
     * @param bool $bForceNewSeoName
     *
     * @return string
     */
    public function GetTargetFileName($bForceNewSeoName = false)
    {
        if ('' == $this->sqlData['seo_name'] || $bForceNewSeoName) {
            $oFileType = &$this->GetFileType();
            $sFileName = $this->sqlData['filename'];
            if (defined('CHAMELEON_ENABLE_ID_SUFFIX_IN_DOWNLOAD_FILENAMES') && CHAMELEON_ENABLE_ID_SUFFIX_IN_DOWNLOAD_FILENAMES) {
                $sFileName .= '_'.$this->sqlData['cmsident'];
            }
            $this->sqlData['seo_name'] = $this->getUrlNormalizationUtil()->normalizeUrl($sFileName).'.'.$oFileType->sqlData['file_extension'];
        }

        return $this->sqlData['seo_name'];
    }

    /**
     * creates a public readable and reusable symlink.
     */
    public function CreatePublicSymLink()
    {
        $filemanager = $this->getFileManager();

        $fullPath = PATH_OUTBOX.'/public/'.$this->sqlData['cms_document_tree_id'].'/';
        if (!is_dir($fullPath)) {
            $filemanager->mkdir($fullPath, 0777, true);
        }
        $targetFile = $this->GetRealPath();

        $sSEOTargetFileName = $this->GetTargetFileName();
        if (!$this->localFileExists()) {
            // trigger error only in live mode because sometimes files are missing in the development environment
            trigger_error('Error: Download source ['.$targetFile.'] does not exist, or is not readable!', E_USER_NOTICE);
        } else {
            $filemanager->unlink($fullPath.'/'.$sSEOTargetFileName);
            if (!file_exists($fullPath.'/'.$sSEOTargetFileName)) {
                if ($filemanager->symlink($targetFile, $fullPath.'/'.$sSEOTargetFileName)) {
                    $this->fileURL = URL_OUTBOX.'/public/'.$this->sqlData['cms_document_tree_id'].'/'.$sSEOTargetFileName;
                } else {
                    trigger_error('Error: Unable to create SymLink ['.$targetFile.'] -> ['.$fullPath.'/'.$sSEOTargetFileName.']', E_USER_WARNING);
                }
            }
        }
    }

    /**
     * deletes a public symlink.
     */
    public function RemovePublicSymLink()
    {
        $oImageType = &$this->GetFileType();
        if ($oImageType) {
            $symLink = PATH_OUTBOX.'/public/'.$this->sqlData['cms_document_tree_id'].'/'.$this->GetTargetFileName();
            if (file_exists($symLink)) {
                $this->getFileManager()->unlink($symLink);
            }
        }
    }

    /**
     * returns the filetype of the download.
     *
     * @return TdbCmsFiletype
     */
    public function &GetFileType()
    {
        if (!array_key_exists('oFileType', $this->_cacheParameter)) {
            $this->_cacheParameter['oFileType'] = null;
            if (array_key_exists('cms_filetype_id', $this->sqlData) && !empty($this->sqlData['cms_filetype_id'])) {
                $this->_cacheParameter['oFileType'] = TdbCmsFiletype::GetNewInstance($this->sqlData['cms_filetype_id']);
            }
        }

        return $this->_cacheParameter['oFileType'];
    }

    /**
     * Renders HTML tag of download with icon and size.
     */
    public function getDownloadHtmlTag(
        bool $isWysiwygBackendLink = false,
        bool $hideName = false,
        bool $hideSize = false,
        bool $hideIcon = false,
        string $downloadLinkName = ''): string
    {
        if (array_key_exists('downloadHtmlTag', $this->_cacheParameter)) {
            return $this->_cacheParameter['downloadHtmlTag'];
        }

        $viewRenderer = new \ViewRenderer();

        $downloadUrl = $this->GetPlainDownloadLink($isWysiwygBackendLink);

        $fileName = TGlobalBase::OutHTML($this->GetName());
        if ('' !== $downloadLinkName) {
            $fileName = TGlobal::OutHTML($downloadLinkName, false);
        }

        $downloadLinkDataModel = new DownloadLinkDataModel($this->id, $downloadUrl, $fileName);
        $downloadLinkDataModel->setIsBackendLink($isWysiwygBackendLink);
        $downloadLinkDataModel->setHumanReadableFileSize(self::GetHumanReadableFileSize($this->sqlData['filesize']));
        $downloadLinkDataModel->setShowSize(!$hideSize);

        if (true === $hideName) {
            $downloadLinkDataModel->setShowFilename(false);
            $downloadLinkDataModel->setLinkStyle('text-decoration:none');
        }

        if (false === $hideIcon) {
            $fileType = $this->GetFileType();
            $iconClass = $this->getFileTypeIconCssStyle().TGlobalBase::OutHTML($fileType->sqlData['file_extension']);
            $downloadLinkDataModel->setIconCssClass($iconClass);
        }

        $viewRenderer->AddSourceObject('downloadLink', $downloadLinkDataModel);

        $this->_cacheParameter['downloadHtmlTag'] = $viewRenderer->Render('common/download/download.html.twig');

        return $this->_cacheParameter['downloadHtmlTag'];
    }

    protected function getFileTypeIconCssStyle(): string
    {
        return 'fiv-sqo fiv-icon-';
    }

    /**
     * Get download link for Wysiwyg editor.
     *
     * @return string
     */
    public function GetWysiwygDownloadLink()
    {
        $sName = $this->GetName();
        $sName = str_replace(',', '', $sName);
        $sWysiwygDownloadLink = $this->id.',dl,'.$sName.',ico,kb';

        return $sWysiwygDownloadLink;
    }

    /**
     * returns full URL to the file.
     *
     * @param bool                     $dummyLink     - prevent output of download url
     * @param bool                     $bCreateToken  - @deprecated if a download needs a token set this in database
     * @param bool                     $bRelativeURL  - if set to true, the method will return the URL as relative URl without domain e.g. /chameleon/outbox/.../filename.pdf
     * @param TdbDataExtranetUser|null $oExtranetUser (you may set a specific user to get a token with user binding)
     *
     * @return string
     */
    public function GetPlainDownloadLink($dummyLink = false, $bCreateToken = false, $bRelativeURL = false, $oExtranetUser = null)
    {
        if (isset($this->sqlData['token_protected']) && '1' == $this->sqlData['token_protected'] && array_key_exists('sFullDownloadURL', $this->_cacheParameter)) {
            unset($this->_cacheParameter['sFullDownloadURL']);
        }
        if (!array_key_exists('sFullDownloadURL', $this->_cacheParameter)) {
            $sLink = '';
            if (!$dummyLink) {
                if ('1' == $this->sqlData['private'] || (isset($this->sqlData['token_protected']) && '1' == $this->sqlData['token_protected']) || $bCreateToken) {
                    $sLink = $this->getProtectedDownLoadLink($bRelativeURL);
                    if ((isset($this->sqlData['token_protected']) && '1' == $this->sqlData['token_protected']) || $bCreateToken) {
                        $bCreateTokenWithUserBinding = false;
                        if (!is_null($oExtranetUser)) {
                            $bCreateTokenWithUserBinding = true;
                        }
                        $sLink .= 'token/'.$this->createDownloadFileToken($bCreateTokenWithUserBinding, $oExtranetUser);
                    }
                    $sLink = TGlobalBase::OutHTML($sLink);
                    $this->fileURL = $sLink;
                } else {
                    $sSEOFileName = $this->GetTargetFileName();
                    if (CMS_USE_SEO_HANDLER_FOR_PUBLIC_DOWNLOADS === true) {
                        $sLink = URL_DOCUMENT_VIRTUAL_OUTBOX.$this->id.'/'.$sSEOFileName;
                    } else {
                        $sLink = TGlobalBase::OutHTML(URL_OUTBOX.'/public/'.$this->sqlData['cms_document_tree_id'].'/'.$sSEOFileName);
                        $sLink = str_replace('//outbox', '/outbox', $sLink);
                        $sLink = str_replace('//public', '/public', $sLink);
                    }
                    if ($bRelativeURL) {
                        $sLink = str_replace('https://'.$_SERVER['HTTP_HOST'], '', $sLink);
                        $sLink = str_replace('http://'.$_SERVER['HTTP_HOST'], '', $sLink);
                    }
                }
            } else {
                $sLink = $this->id;
            }

            $this->_cacheParameter['sFullDownloadURL'] = $sLink;
        }

        return $this->_cacheParameter['sFullDownloadURL'];
    }

    /**
     * @param bool $bAsDownload if true deliver download original header. If false deliver download with streming header
     *
     * @return string
     */
    public function getBackendDownloadLink($bAsDownload)
    {
        $oTableConf = $this->GetTableConf();
        $aParams = array();
        $aParams['pagedef'] = 'tableeditor';
        $aParams['id'] = $this->id;
        $aParams['tableid'] = $oTableConf->id;
        $aParams['module_fnc'] = array('contentmodule' => 'downloadDocument');
        $aParams['callFieldMethod'] = '1';
        $aParams['_noModuleFunction'] = 'true';
        $aParams['asDownload'] = '0';
        if (true === $bAsDownload) {
            $aParams['asDownload'] = '1';
        }
        $sURL = PATH_CMS_CONTROLLER.'?'.TTools::GetArrayAsURLForJavascript($aParams);

        return $sURL;
    }

    /**
     * get the download as stream or show content directly.
     *
     * @param bool $bAsDownload if true download as stream if false show content directly
     */
    public function downloadDocument($bAsDownload = false)
    {
        $sContentType = 'application/octet-stream';
        $sFileName = $this->fieldFilename;

        /**
         * @var $oContentType TdbCmsFiletype
         */
        $oFileType = $this->GetFileType();
        if ($oFileType) {
            if (false === $bAsDownload) {
                $sContentType = $oFileType->fieldContentType;
            }
            $sFileName .= '.'.$oFileType->fieldFileExtension;
        }

        $response = new BinaryFileResponse(
            new File($this->GetRealPath()),
            Response::HTTP_OK,
            [
                'Content-Type' => $sContentType,
            ],
            false,
            null,
            true
        );

        $disposition = (true === $bAsDownload) ? ResponseHeaderBag::DISPOSITION_ATTACHMENT : ResponseHeaderBag::DISPOSITION_INLINE;
        $response->setContentDisposition($disposition, $sFileName);

        $request = $this->getCurrentRequest();
        $response->prepare($request);

        $response->send();
        exit;
    }

    /**
     * @param bool $bRelativeURL
     *
     * @return string
     */
    protected function getProtectedDownLoadLink($bRelativeURL = false)
    {
        $sLink = URL_PROTECTED_DOCUMENT_VIRTUAL_OUTBOX.$this->id.'/';
        if ($bRelativeURL) {
            $sLink = str_replace('https://'.$_SERVER['HTTP_HOST'], '', $sLink);
            $sLink = str_replace('http://'.$_SERVER['HTTP_HOST'], '', $sLink);
            if ('/' != substr($sLink, 0, 1)) {
                $sLink = '/'.$sLink;
            }
        }

        return $sLink;
    }

    /**
     * creates a token record and returns the token.
     *
     * @param bool $bCreateTokenWithUserBinding - off by default (adds the current user as token owner, if $oExtranetUser is given, user binding is forced)
     * @param TdbDataExtranetUser|null if no user object is given the current logged in user is used instead
     *
     * @return string
     */
    protected function createDownloadFileToken($bCreateTokenWithUserBinding = false, $oExtranetUser = null)
    {
        $aData = $this->getDocumentSecurityHashTokenData($bCreateTokenWithUserBinding, $oExtranetUser);
        $sToken = $aData['token'];

        $oTableEditor = new TCMSTableEditorManager();
        $iTableID = TTools::GetCMSTableId('cms_document_security_hash');
        /** @var $oTableEditor TCMSTableEditor */
        $oTableEditor->Init($iTableID);
        $oTableEditor->AllowEditByAll(true);
        $oRes = $oTableEditor->Save($aData);
        $oTableEditor->AllowEditByAll(false);
        if (false === $oRes) {
            trigger_error('Error: Unable to create AuthenticityToken for this document [cms_document_id] -> ['.$this->id.']', E_USER_WARNING);
        }

        return $sToken;
    }

    /**
     * returns an array with post data for save in token database table.
     *
     * @param bool                     $bCreateTokenWithUserBinding
     * @param TdbDataExtranetUser|null $oExtranetUser
     *
     * @return array
     */
    protected function getDocumentSecurityHashTokenData($bCreateTokenWithUserBinding = false, $oExtranetUser = null)
    {
        $aData = array();
        $aData['token'] = TTools::GetUUID();

        if (!is_null($oExtranetUser)) { // force user binding
            $aData['data_extranet_user_id'] = $oExtranetUser->id;
        } else {
            if ($bCreateTokenWithUserBinding) { // if user binding is not disabled, we use the current user
                /** @var $oExtranetUser TdbDataExtranetUser */
                $oExtranetUser = TdbDataExtranetUser::GetInstance();
                if ($oExtranetUser) {
                    $aData['data_extranet_user_id'] = $oExtranetUser->id;
                } else { // now user is logged in, and no user given, but user binding is forced, Houston - we have a problem
                    return $aData; // return only the token
                }
            } else {
                $aData['data_extranet_user_id'] = '';
            }
        }

        $aData['cms_document_id'] = $this->id;

        $aData['publishdate'] = date('Y-m-d H:i:s');
        $iEndTime = strtotime($aData['publishdate']) + CHAMELEON_DOCUMENT_AUTH_TOKEN_LIFE_TIME_IN_MINUTES * 60;
        $aData['enddate'] = date('Y-m-d H:i:s', $iEndTime);

        return $aData;
    }

    /**
     * formats file size in bytes to human readable format e.g. 100 kb / 3 MB
     * if $fileSize is a string the method returns it as is.
     *
     * @param int $fileSize
     *
     * @return string
     */
    public static function GetHumanReadableFileSize($fileSize)
    {
        $returnVal = 0;
        if (!empty($fileSize)) {
            if (is_numeric($fileSize)) {
                if ($fileSize <= 1024) {
                    $returnVal = '1 kb';
                } else {
                    $returnVal = round($fileSize / 1024); // kilobytes

                    if (strlen($returnVal) >= 4) {
                        $nachkomma = round(mb_substr($returnVal, -3) / 100);
                        $vorkomma = mb_substr($returnVal, 0, -3);
                        $returnVal = $vorkomma.','.$nachkomma.' MB';
                    } else {
                        $returnVal = round($returnVal).' kb';
                    }
                }
            } else { // is string and has MB or kb info
                $returnVal = $fileSize;
            }
        }

        return $returnVal;
    }

    /**
     * returns the ID of a given file type string
     * or false if file type could not be found in cms database.
     *
     * @param string $fileExtension
     *
     * @return string|bool - id of file type... false if file type wasn't found
     */
    public static function GetFileTypeIdByExtension($fileExtension)
    {
        $returnVal = false;

        $oFileType = new TCMSRecord('cms_filetype');
        $oFileType->LoadFromField('file_extension', $fileExtension);

        if (!is_null($oFileType->id)) {
            $returnVal = $oFileType->id;
        }

        return $returnVal;
    }

    /**
     * get the file type icon as plain IMG TAG.
     *
     * @return string
     */
    public function GetPlainFileTypeIcon()
    {
        $fileType = $this->GetFileType();

        return '<span class="'.$this->getFileTypeIconCssStyle().TGlobalBase::OutHTML($fileType->sqlData['file_extension']).'"></span>';
    }

    /**
     * checks.
     *
     * @param TdbCmsDocumentSecurityHash|null $oSecurityToken
     *
     * @return bool
     */
    public function allowDeliver($oSecurityToken = null)
    {
        $bAllowDeliver = false;

        if (!TdbCmsConfig::RequestIsInBotList()) {
            if (null !== $oSecurityToken) {
                if ($oSecurityToken->isValidTimeSpan()) {
                    $bAllowDeliver = $this->hasUserRights($oSecurityToken);
                }
            } else {
                $bAllowDeliver = $this->hasUserRights(null);
            }
        }

        return $bAllowDeliver;
    }

    /**
     * returns false if current user has no right for the document.
     *
     * @param TdbCmsDocumentSecurityHash|null $oSecurityToken
     *
     * @return bool
     */
    protected function hasUserRights($oSecurityToken = null)
    {
        $bHasRight = false;
        $oUser = TdbDataExtranetUser::GetInstance();

        if (null !== $oSecurityToken) {
            $bHasRight = $oSecurityToken->isValidForExtranetUser($oUser);
        } else {
            $bIsLoggedIn = $oUser->IsLoggedIn();
            if ($this->fieldTokenProtected) {
                if ($bIsLoggedIn) {
                    $oSecurityTokens = TdbCmsDocumentSecurityHashList::getListForDocumentAndUser($this->id, $oUser->id);
                    if ($oSecurityTokens->Length() > 0) {
                        $bHasRight = true;
                    }
                }
            } elseif (CHAMELEON_CHECK_VALID_USER_SESSION_ON_PROTECTED_DOWNLOADS) {
                $bHasRight = $bIsLoggedIn;
            } else {
                $bHasRight = true;
            }
        }

        return $bHasRight;
    }

    /**
     * Here you can set an redirect if download is not allowed to deliver.
     * If no redirect was done here the url handler show no access message.
     *
     * @throws AccessDeniedHttpException
     */
    public function notAllowedDeliverHook()
    {
        if (CHAMELEON_CHECK_VALID_USER_SESSION_ON_PROTECTED_DOWNLOADS) {
            throw new AccessDeniedHttpException('Access denied.');
        }
    }

    /**
     * delivery check is needed if document is in private state or is token protected.
     *
     * @return bool
     */
    public function isDeliveryCheckNeeded()
    {
        $bNeeded = false;
        if (true === $this->fieldPrivate || true === $this->fieldTokenProtected) {
            $bNeeded = true;
        }

        return $bNeeded;
    }

    /**
     * returns false if document file does not exist.
     *
     * @return bool
     */
    public function localFileExists()
    {
        return file_exists($this->GetRealPath());
    }

    /**
     * generate an e-tag for caching files.
     *
     * @return string
     */
    public function getETag()
    {
        $sETag = md5(implode($this->sqlData));

        return $sETag;
    }

    /**
     * @return Request
     */
    private function getCurrentRequest()
    {
        return ServiceLocator::get('request_stack')->getCurrentRequest();
    }

    /**
     * @return IPkgCmsFileManager
     */
    private function getFileManager()
    {
        return ServiceLocator::get('chameleon_system_cms_file_manager.file_manager');
    }

    /**
     * @return UrlNormalizationUtil
     */
    private function getUrlNormalizationUtil()
    {
        return ServiceLocator::get('chameleon_system_core.util.url_normalization');
    }
}
