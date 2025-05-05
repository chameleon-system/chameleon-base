<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class TCMSSmartURLHandler_Document extends TCMSSmartURLHandler
{
    protected function getProtectedDownloadPath()
    {
        return URL_PROTECTED_DOCUMENT_VIRTUAL_OUTBOX;
    }

    protected function getPublicDownloadPath()
    {
        return URL_DOCUMENT_VIRTUAL_OUTBOX;
    }

    protected function getSymLinkDownloadPath()
    {
        return URL_OUTBOX.'public/';
    }

    public function GetPageDef()
    {
        $sDocumentSeoName = false;
        $sDocumentId = false;
        $oDocument = null;
        $iPageId = false;
        $bAllowDeliver = false;
        $bLoaded = false;
        $oSecurityToken = null;
        $sDownloadOutboxPath = $this->getPathURLMatchDownloadPath();
        $bLocalFileMissing = false;
        if (false != $sDownloadOutboxPath) {
            if ('symlink' == $this->getDownloadType($sDownloadOutboxPath)) {
                $sDocumentSeoName = $this->getDocumentSEONameFromURL($sDownloadOutboxPath);
            } else {
                $sTokenId = $this->getTokenFromURL($sDownloadOutboxPath);
                if (false != $sTokenId) {
                    $oSecurityToken = TdbCmsDocumentSecurityHash::GetNewInstance();
                    if ($oSecurityToken->LoadFromField('token', $sTokenId)) {
                        $sDocumentId = $oSecurityToken->fieldCmsDocumentId;
                    }
                } else {
                    $sDocumentId = $this->getDocumentIdFromURL($sDownloadOutboxPath);
                }
            }
            if (false != $sDocumentId || false != $sDocumentSeoName) {
                /** @var TdbCmsDocument $oDocument */
                $oDocument = TdbCmsDocument::GetNewInstance();
                if (false != $sDocumentSeoName) {
                    if ($oDocument->LoadFromField('seo_name', $sDocumentSeoName)) {
                        $bLoaded = true;
                        $sNewDocumentUrl = $oDocument->GetPlainDownloadLink(false, false, true);
                        $this->getRedirect()->redirect($sNewDocumentUrl, Response::HTTP_MOVED_PERMANENTLY);
                    }
                }
                if (false != $sDocumentId && false === $bLoaded) {
                    $bLoaded = $oDocument->Load($sDocumentId);
                }
                if ($bLoaded) {
                    if ($oDocument->isDeliveryCheckNeeded()) {
                        $bAllowDeliver = $oDocument->allowDeliver($oSecurityToken);
                    } else {
                        $bAllowDeliver = true;
                    }

                    if (!$oDocument->localFileExists()) {
                        if (_DEVELOPMENT_MODE) {
                            $bLocalFileMissing = true;
                        } else {
                            $this->handleNotFound();
                        }
                    }
                } else {
                    $this->handleNotFound();
                }
            } else {
                $this->handleNotFound();
            }
            if ($bAllowDeliver) {
                if ($bLocalFileMissing) {
                    $this->handleNotFound('DEV_MODE: access granted, but local file is missing');
                } else {
                    $oDocument->downloadDocument();
                }
            } else {
                if (true == $bLoaded) {
                    $oDocument->notAllowedDeliverHook();
                }
                throw new AccessDeniedHttpException();
            }
        }

        return $iPageId;
    }

    protected function handleNotFound($sMessage = '')
    {
        throw new Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
    }

    /**
     * Checks public, protected and symlink download links.
     * if download link is ok return cleaned download path without download data
     * Check public and symlinks only if CMS_USE_SEO_HANDLER_FOR_PUBLIC_DOWNLOADS is true.
     *
     * @return bool|string
     */
    protected function getPathURLMatchDownloadPath()
    {
        $bIsURLMatchDownloadPath = false;
        $sOutboxPath = false;
        if (CMS_USE_SEO_HANDLER_FOR_PUBLIC_DOWNLOADS === true) {
            $sOutboxPath = $this->getCleanPath($this->getPublicDownloadPath());
            $bIsURLMatchDownloadPath = $this->checkDownloadPathCorrect($sOutboxPath);
            if (!$bIsURLMatchDownloadPath) {
                $sOutboxPath = $this->getCleanPath($this->getSymLinkDownloadPath());
                $bIsURLMatchDownloadPath = $this->checkDownloadPathCorrect($sOutboxPath);
            }
        }
        if (false === $bIsURLMatchDownloadPath) {
            $sOutboxPath = $this->getCleanPath($this->getProtectedDownloadPath());
            $bIsURLMatchDownloadPath = $this->checkDownloadPathCorrect($sOutboxPath);
        }
        if (true != $bIsURLMatchDownloadPath) {
            $sOutboxPath = false;
        }

        return $sOutboxPath;
    }

    /**
     * Checks if given download path is part of active path.
     *
     * @param string $sOutboxPath
     *
     * @return bool
     */
    protected function checkDownloadPathCorrect($sOutboxPath)
    {
        $bPathCorrect = false;
        $oURLData = TCMSSmartURLData::GetActive();
        $sPath = $this->getCleanPath($oURLData->sRelativeURL);
        if (substr($sPath, 0, strlen($sOutboxPath)) == $sOutboxPath) {
            $bPathCorrect = true;
        }

        return $bPathCorrect;
    }

    /**
     * Strips http and /.
     *
     * @param string $sPath
     *
     * @return string
     */
    protected function getCleanPath($sPath)
    {
        if ('http://' == substr($sPath, 0, 7) || 'https://' == substr($sPath, 0, 8)) {
            $sPath = substr($sPath, strpos($sPath, '/', 8));
        }
        if ('/' == substr($sPath, 0, 1)) {
            $sPath = substr($sPath, 1);
        }

        return $sPath;
    }

    /**
     * Gets document id from url path.
     *
     * @param string $sDownloadOutboxPath
     *
     * @return bool
     */
    protected function getDocumentIdFromURL($sDownloadOutboxPath)
    {
        $sDocumentId = false;
        $aDownloadURLData = $this->getDownloadDataFromURLAsArray($sDownloadOutboxPath);
        if (is_array($aDownloadURLData) && count($aDownloadURLData) > 0) {
            $sDocumentId = $aDownloadURLData[0];
        }

        return $sDocumentId;
    }

    /**
     * gets document seo name from url path.
     *
     * @param string $sDownloadOutboxPath
     *
     * @return bool
     */
    protected function getDocumentSEONameFromURL($sDownloadOutboxPath)
    {
        $sDocumentSeoName = false;
        $aDownloadURLData = $this->getDownloadDataFromURLAsArray($sDownloadOutboxPath);
        if (is_array($aDownloadURLData) && 2 == count($aDownloadURLData)) {
            $sDocumentSeoName = $aDownloadURLData[1];
        }

        return $sDocumentSeoName;
    }

    /**
     * get document token from url path.
     *
     * @param string $sDownloadOutboxPath
     *
     * @return bool
     */
    protected function getTokenFromURL($sDownloadOutboxPath)
    {
        $sTokenId = false;
        $aDownloadURLData = $this->getDownloadDataFromURLAsArray($sDownloadOutboxPath);
        if (is_array($aDownloadURLData) && 3 == count($aDownloadURLData) && 'token' == $aDownloadURLData[1]) {
            $sTokenId = $aDownloadURLData[2];
        }

        return $sTokenId;
    }

    /**
     * Get the download data from active path url.
     * Includes token, id, seo name and tree id. Depends on download type.
     *
     * @param string $sDownloadOutboxPath
     *
     * @return array
     */
    protected function getDownloadDataFromURLAsArray($sDownloadOutboxPath)
    {
        $oURLData = TCMSSmartURLData::GetActive();
        $sPath = $this->getCleanPath($oURLData->sRelativeURL);
        $sDownloadUrlData = substr($sPath, strlen($sDownloadOutboxPath));
        if ('/' == substr($sDownloadUrlData, -1)) {
            $sDownloadUrlData = substr($sDownloadUrlData, 0, strlen($sDownloadUrlData) - 1);
        }
        $aDownloadURLData = explode('/', $sDownloadUrlData);

        return $aDownloadURLData;
    }

    /**
     * Returns the download type specific to given url path.
     *
     * @param string $sDownloadPath
     *
     * @return string
     */
    protected function getDownloadType($sDownloadPath)
    {
        if ($sDownloadPath == $this->getCleanPath($this->getPublicDownloadPath())) {
            $sType = 'public';
        } elseif ($sDownloadPath == $this->getCleanPath($this->getProtectedDownloadPath())) {
            $sType = 'protected';
        } elseif ($sDownloadPath == $this->getCleanPath($this->getSymLinkDownloadPath())) {
            $sType = 'symlink';
        }

        return $sType;
    }
}
