<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use esono\pkgCmsCache\CacheInterface;

class TCMSTableEditorMediaTree extends TCMSTableEditorTreeShared
{
    /**
     * called after inserting a new record.
     *
     * @param TIterator $oFields - the fields inserted
     */
    protected function PostInsertHook(&$oFields)
    {
        parent::PostInsertHook($oFields);

        /** @var $oNodeUpdate TdbCmsMediaTree */
        $oNodeUpdate = $this->oTable;
        // need to get root path...
        $sRootPath = '';
        if (!empty($oNodeUpdate->fieldParentId)) {
            $oParent = $oNodeUpdate->GetFieldParent();
            if ($oParent) {
                $sRootPath = $oParent->fieldPathCache;
            }
        }
        $this->UpdatePathCache($oNodeUpdate, $sRootPath);
    }

    /**
     * gets called after save if all posted data was valid.
     *
     * @param TIterator  $oFields    holds an iterator of all field classes from DB table with the posted values or default if no post data is present
     * @param TCMSRecord $oPostTable holds the record object of all posted data
     */
    protected function PostSaveHook(&$oFields, &$oPostTable)
    {
        /** @var $oNodeUpdate TdbCmsMediaTree */
        $oNodeUpdate = $this->oTable;
        // need to get root path...
        //      $oNodeUpdate = TdbCmsMediaTree::GetNewInstance($oPostTable->id);
        $sRootPath = '';
        if (!empty($oNodeUpdate->fieldParentId)) {
            $oParent = $oNodeUpdate->GetFieldParent();
            if ($oParent) {
                $sRootPath = $oParent->GetPathCache();
            }
        }
        $this->UpdatePathCache($oNodeUpdate, $sRootPath);

        parent::PostSaveHook($oFields, $oPostTable);
    }

    /**
     * updates the image seo path.
     *
     * @param TdbCmsMediaTree $oNodeToUpdate
     * @param string          $sRootPath
     */
    protected function UpdatePathCache($nodeToUpdate, $rootPath)
    {
        $fileManager = $this->getFileManager();

        // if not and the new path does not exist, then we create the new node
        // then we update update the file path in the new node
        // and repeat for every child

        $realMediaLibraryPath = realpath(PATH_OUTBOX_MEDIA_LIBRARY_SEO_LINKS);
        if (false == $realMediaLibraryPath) {
            $fileManager->mkdir(PATH_OUTBOX_MEDIA_LIBRARY_SEO_LINKS, 0777, true);
            $realMediaLibraryPath = realpath(PATH_OUTBOX_MEDIA_LIBRARY_SEO_LINKS);
        }

        if ('/' !== substr($rootPath, -1)) {
            $rootPath .= '/';
        }
        $newPath = $rootPath.$nodeToUpdate->GetNodeNameAsDirName();

        $oldServerPath = '';
        if (TdbCmsMediaTree::CMSFieldIsTranslated('path_cache')) {
            // we need to take the original path directly from sqlData - since it will hold the original value from the de field if it had not been translated
            $languagePrefix = TGlobal::GetLanguagePrefix();
            $languagePrefix = (!empty($languagePrefix)) ? '__'.$languagePrefix : $languagePrefix;
            $oldServerPath = $nodeToUpdate->sqlData['path_cache'.$languagePrefix];
        }
        if (!empty($oldServerPath)) {
            $oldServerPath = $nodeToUpdate->GetFullServerPath();

            // make sure the path is below PATH_OUTBOX_MEDIA_LIBRARY_SEO_LINKS
            $sRealPath = realpath($oldServerPath);
            $sRelativeOldPath = substr($sRealPath, strlen($realMediaLibraryPath));
            if ('/' === $sRelativeOldPath || empty($sRelativeOldPath)) {
                $oldServerPath = '';
            }
        }

        $nodeToUpdate->fieldPathCache = $newPath;
        $nodeToUpdate->sqlData['path_cache'] = $newPath;
        $newServerPath = $nodeToUpdate->GetFullServerPath();

        if (CMS_MEDIA_ENABLE_SEO_URLS) {
            if (!empty($oldServerPath) && is_dir($oldServerPath)) {
                if (!is_dir($newServerPath) && !file_exists($newServerPath)) {
                    $fileManager->move($oldServerPath, $newServerPath);
                } else {
                    $fileManager->rmdir($oldServerPath);
                }
            } elseif (!empty($newServerPath) && !is_dir($newServerPath) && !file_exists($newServerPath)) {
                // need to create the dir... every dir contains a symlink to the mediapool
                $relativePath = substr($newServerPath, strlen($realMediaLibraryPath));
                $parts = explode('/', $relativePath);
                $pathToUpdate = $realMediaLibraryPath;
                $relativeLink = PATH_MEDIA_LIBRARY;
                // remove path to document root
                $documentRoot = realpath($_SERVER['DOCUMENT_ROOT']);
                $relativeLink = substr($relativeLink, strlen($documentRoot));
                if ('/' == substr($relativeLink, 0, 1)) {
                    $relativeLink = substr($relativeLink, 1);
                }
                $relativeLink = '../../../'.$relativeLink;
                foreach ($parts as $path) {
                    if (!empty($path)) {
                        $relativeLink = '../'.$relativeLink;
                        $pathToUpdate .= '/'.$path;
                        if (!is_dir($pathToUpdate)) {
                            $fileManager->mkdir($pathToUpdate);
                            $fileManager->symlink($relativeLink, $pathToUpdate.'/i');
                        }
                    }
                }
            }
        }
        $editor = TTools::GetTableEditorManager($nodeToUpdate->table, $nodeToUpdate->id);
        // now we update the file cache
        $editor->SaveField('path_cache', $newPath);

        if (true === CMS_MEDIA_ENABLE_SEO_URLS) {
            $this->updateCache($nodeToUpdate->id);
        }

        // and update all children
        $oChildren = $nodeToUpdate->GetChildren();
        while ($oChild = $oChildren->Next()) {
            $this->UpdatePathCache($oChild, $newPath);
        }
    }

    /**
     * @param string $id
     *
     * @throws ErrorException
     * @throws TPkgCmsException_Log
     *
     * @return void
     */
    protected function updateCache($id)
    {
        // updating the page cache will affect all images in that folder. since this may affect MANY images, we need to
        // use an approximation... if we have fewer than 100 images in the folder, we clear each by hand. if we have more we clear all
        $oImageList = TdbCmsMediaList::GetList("SELECT * FROM cms_media WHERE `cms_media_tree_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($id)."'");
        if ($oImageList->Length() <= 10) { // the next block is VERY slow... only use if there are less then 10 images. otherwise we clear the complete cache
            while ($oImage = $oImageList->Next()) {
                TCacheManager::PerformeTableChange('cms_media', $oImage->id);
                $oImageEditor = TTools::GetTableEditorManager('cms_media');
                $oImageEditor->oTableEditor->ClearCacheOfObjectsUsingImage($oImage->id);
            }
        } else {
            /** @var CacheInterface $cache */
            $cache = \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.cache');
            $cache->clearAll();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function Delete($sId = null)
    {
        $fileManager = $this->getFileManager();

        if (null !== $sId) {
            $this->DeleteFolderItems($sId);

            if (CMS_MEDIA_ENABLE_SEO_URLS) {
                // remove dir
                $oNode = TdbCmsMediaTree::GetNewInstance($sId);
                $sPath = $oNode->GetFullServerPath();
                $sPath = realpath($sPath);
                if (!empty($sPath)) {
                    // make sure the path is ok...
                    $sRealMediaLibraryPath = realpath(PATH_OUTBOX_MEDIA_LIBRARY_SEO_LINKS);
                    $sRelativePath = substr($sPath, strlen($sRealMediaLibraryPath));
                    if (!empty($sRelativePath) && is_dir($sPath)) {
                        $fileManager->unlink($sPath.'/i');
                        $fileManager->rmdir($sPath);
                    }
                }
            }
        }

        parent::Delete($sId);
    }

    /**
     * remove all images in folder $sFolderId.
     *
     * @param string $sFolderId - folder id
     */
    protected function DeleteFolderItems($sFolderId)
    {
        $oMediaTableConf = new TCMSTableConf();
        /** @var $oMediaTableConf TCMSTableConf */
        $oMediaTableConf->LoadFromField('name', 'cms_media');

        // call delete for all images...
        $query = "SELECT * FROM `cms_media` WHERE `cms_media_tree_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($sFolderId)."'";
        $tImages = MySqlLegacySupport::getInstance()->query($query);
        while ($aImage = MySqlLegacySupport::getInstance()->fetch_assoc($tImages)) {
            $oMediaEditor = new TCMSTableEditorManager();
            /** @var $oMediaEditor TCMSTableEditorManager */
            $oMediaEditor->Init($oMediaTableConf->id);
            $oMediaEditor->Delete($aImage['id']);
            unset($oMediaEditor);
        }
    }

    /**
     * @return IPkgCmsFileManager
     */
    private function getFileManager()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.filemanager');
    }
}
