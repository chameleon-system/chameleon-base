<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\ImageEditorBundle\Bridge\Chameleon\BackendModule;

use ChameleonSystem\CmsBackendBundle\BackendSession\BackendSessionInterface;
use ChameleonSystem\CoreBundle\Interfaces\FlashMessageServiceInterface;
use ChameleonSystem\CoreBundle\Util\InputFilterUtilInterface;
use ChameleonSystem\ImageCrop\DataModel\CmsMediaDataModel;
use ChameleonSystem\ImageCrop\Interfaces\CmsMediaDataAccessInterface;
use ChameleonSystem\ImageEditorBundle\Interface\ImageEditorUrlServiceInterface;
use ChameleonSystem\SecurityBundle\Service\SecurityHelperAccess;
use Doctrine\DBAL\Connection;

class ImageEditorModule extends \MTPkgViewRendererAbstractModuleMapper
{
    public const PAGEDEF_NAME = 'imageEditor';
    public const PAGEDEF_TYPE = '@ChameleonSystemImageEditorBundle';
    private const URL_PARAM_IMAGE_ID = 'cmsMediaId';
    private const URL_PARAM_IMAGE_WIDTH = 'imageWidth';
    private const URL_PARAM_IMAGE_HEIGHT = 'imageHeight';

    public function __construct(
        private readonly InputFilterUtilInterface $inputFilterUtil,
        private readonly BackendSessionInterface $backendSession,
        private readonly CmsMediaDataAccessInterface $cmsMediaDataAccess,
        private readonly Connection $dbConnection,
        private readonly FlashMessageServiceInterface $flashMessageService,
        private readonly \cmsCoreRedirect $cmsCoreRedirect,
        private readonly ImageEditorUrlServiceInterface $editorUrlService,
        private readonly SecurityHelperAccess $securityHelperAccess,
        private readonly \TTools $tools
    ) {
        parent::__construct();
    }

    public function Accept(
        \IMapperVisitorRestricted $oVisitor,
        $bCachingEnabled,
        \IMapperCacheTriggerRestricted $oCacheTriggerManager
    ) {
        $image = $this->getCmsImage();

        if (null === $image) {
            return;
        }

        $oVisitor->SetMappedValue('imageUrl', $image->getImageUrl());
        $oVisitor->SetMappedValue('imageId', $image->getId());
        $oVisitor->SetMappedValue('imageWidth', $this->inputFilterUtil->getFilteredInput(self::URL_PARAM_IMAGE_WIDTH));
        $oVisitor->SetMappedValue('imageHeight', $this->inputFilterUtil->getFilteredInput(self::URL_PARAM_IMAGE_HEIGHT));

        if ($this->flashMessageService->consumerHasMessages(\TCMSTableEditorManager::MESSAGE_MANAGER_CONSUMER)) {
            $oVisitor->SetMappedValue(
                'renderedTableEditorMessages',
                $this->flashMessageService->renderMessages(
                    \TCMSTableEditorManager::MESSAGE_MANAGER_CONSUMER,
                    'standard',
                    'Core'
                )
            );
        }
    }

    public function GetHtmlFooterIncludes()
    {
        $includes = parent::GetHtmlFooterIncludes();

        $includes[] = '
            <link  href="'.\TGlobal::GetStaticURL('/bundles/chameleonsystemimageeditor/css/imageEditor.css').'?v=1" rel="stylesheet">
            <script src="'.\TGlobal::GetStaticURL('/bundles/chameleonsystemimageeditor/filerobot/lodash.min.js').'?v=1"></script>
            <script src="'.\TGlobal::GetStaticURL('/bundles/chameleonsystemimageeditor/filerobot/filerbot-image-editor.min.js').'?v=1"></script>;
            <script src="'.\TGlobal::GetStaticURL('/bundles/chameleonsystemimageeditor/filerobot/filerobot.js').'?v=1"></script>';

        return $includes;
    }

    /**
     * {@inheritdoc}
     */
    protected function DefineInterface()
    {
        parent::DefineInterface();
        $this->methodCallAllowed[] = 'saveImage';
    }

    protected function saveImage(): void
    {
        $editedImageObject = $this->getEditedImageObject();
        $editedImageData = $editedImageObject['imageBase64'];
        $editedImageDataParts = explode(',', $editedImageData);

        $imageId = $this->inputFilterUtil->getFilteredPostInput('imageId');

        $subPath = $this->dbConnection->fetchOne('SELECT `path` FROM `cms_media` WHERE `id` = :id', ['id' => $imageId]);
        $splitedPath = explode('/', $subPath);

        $imageData = base64_decode($editedImageDataParts[1], true);
        $path = PATH_MEDIA_LIBRARY.$splitedPath[0].'/'.$splitedPath[1].'/'.time().$editedImageObject['name'].'.'.$editedImageObject['extension'];
        $fileSet = file_put_contents($path, $imageData);

        if (false === $fileSet) {
            $this->flashMessageService->addMessage(\TCMSTableEditorManager::MESSAGE_MANAGER_CONSUMER, 'IMAGE_EDITOR_ERROR_COULD_NOT_CREATE_FILE');

            return;
        }

        $user = $this->securityHelperAccess->getUser();

        if (null === $user) {
            $this->flashMessageService->addMessage(\TCMSTableEditorManager::MESSAGE_MANAGER_CONSUMER, 'IMAGE_EDITOR_WARNING_COULD_NOT_SAVE_IMAGE_DATA');

            return;
        }

        $tableManagerMedia = $this->tools::GetTableEditorManager('cms_media');
        $tableManagerMedia->AllowEditByAll(true);

        $mediaRecord = $tableManagerMedia->Insert();

        if (null === $mediaRecord) {
            $this->flashMessageService->addMessage(\TCMSTableEditorManager::MESSAGE_MANAGER_CONSUMER, 'IMAGE_EDITOR_WARNING_COULD_NOT_SAVE_IMAGE_DATA');

            return;
        }

        $size = filesize($path);

        $imageData = [
            'id' => $mediaRecord->id,
            'name' => $editedImageObject['name'].'.'.$editedImageObject['extension'],
            'cms_media_tree_id' => '1',
            'width' => '',
            'height' => '',
            'description' => $editedImageObject['name'],
            'path' => $path,
            'cms_filetype_id' => '8',
            'cms_user_id' => $user->getId(),
            'custom_filename' => $editedImageObject['name'],
            'size' => $size,
            'tmp_name' => $path,
            'error' => 0,
            'type' => 'jpg',
        ];

        /**
         * @var \TCMSTableEditorFiles $tableEditor
         */
        $tableEditor = $tableManagerMedia->oTableEditor;
        $tableEditor->SetUploadData($imageData, true);

        if (false === $tableManagerMedia->Save($imageData)) {
            $this->flashMessageService->addMessage(\TCMSTableEditorManager::MESSAGE_MANAGER_CONSUMER, 'IMAGE_EDITOR_WARNING_COULD_NOT_SAVE_IMAGE_DATA');
            $tableManagerMedia->AllowEditByAll(false);

            return;
        }

        $tableManagerMedia->AllowEditByAll(false);
        $this->flashMessageService->addMessage(\TCMSTableEditorManager::MESSAGE_MANAGER_CONSUMER, 'IMAGE_EDITOR_SUCCESS_NEW_IMAGE_HAS_BEEN_SAVED');

        $this->cmsCoreRedirect->redirect($this->editorUrlService->getImageEditorUrl($mediaRecord->id));
    }

    private function getEditedImageObject()
    {
        $imageData = $this->inputFilterUtil->getFilteredPostInput('editedImageData');
        $decoded = json_decode($imageData, true);

        return $decoded['editedImageObject'];
    }

    /**
     * @return CmsMediaDataModel|null
     */
    private function getCmsImage()
    {
        $imageId = $this->inputFilterUtil->getFilteredInput(self::URL_PARAM_IMAGE_ID);

        return $this->cmsMediaDataAccess->getCmsMedia($imageId, $this->backendSession->getCurrentEditLanguageId());
    }
}
