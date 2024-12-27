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
use Doctrine\DBAL\Connection;

class ImageEditorModule extends \MTPkgViewRendererAbstractModuleMapper
{
    public const PAGEDEF_NAME = 'imageEditor';
    public const PAGEDEF_TYPE = '@ChameleonSystemImageEditorBundle';
    private const URL_PARAM_IMAGE_ID = 'cmsMediaId';

    public function __construct(
        private readonly InputFilterUtilInterface $inputFilterUtil,
        private readonly BackendSessionInterface $backendSession,
        private readonly CmsMediaDataAccessInterface $cmsMediaDataAccess,
        private readonly Connection $dbConnection,
        private readonly FlashMessageServiceInterface $flashMessageService
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
            <link  href="'.\TGlobal::GetStaticURL('/bundles/chameleonsystemimageeditor/css/imageEditor.css').'" rel="stylesheet">
            <script src="'.\TGlobal::GetStaticURL('/bundles/chameleonsystemimageeditor/filerobot/lodash.min.js').'"></script>
            <script src="'.\TGlobal::GetStaticURL('/bundles/chameleonsystemimageeditor/filerobot/filerbot-image-editor.min.js').'"></script>;
            <script src="'.\TGlobal::GetStaticURL('/bundles/chameleonsystemimageeditor/filerobot/filerobot.js').'"></script>';

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

    protected function saveImage()
    {
        $imageData = $this->inputFilterUtil->getFilteredPostInput('editedImageData');
        $decoded = json_decode($imageData, true);

        $editedImageData = $decoded['editedImageObject']['imageBase64'];
        $editedImageDataParts = explode(',', $editedImageData);

        $imageData = base64_decode($editedImageDataParts[1], true);

        $imageId = $this->inputFilterUtil->getFilteredPostInput('imageId');

        $subPath = $this->dbConnection->fetchOne('SELECT `path` FROM `cms_media` WHERE `id` = :id', ['id' => $imageId]);
        $path = PATH_MEDIA_LIBRARY.$subPath;

        $fileSet = file_put_contents($path, $imageData);

        if (false === $fileSet) {
            $this->flashMessageService->addMessage(\TCMSTableEditorManager::MESSAGE_MANAGER_CONSUMER, 'IMAGE_EDITOR_ERROR_COULD_NOT_CREATE_FILE');

            return;
        }

        $tableManagerMedia = \TTools::GetTableEditorManager('cms_media', $imageId);

        // We save this field to force the table manager to trigger its post save hook and update caches, thumbnails and so on
        if (false === $tableManagerMedia->SaveField('path', $subPath, true)) {
            $this->flashMessageService->addMessage(\TCMSTableEditorManager::MESSAGE_MANAGER_CONSUMER, 'IMAGE_EDITOR_WARNING_COULD_NOT_SAVE_IMAGE_DATA');

            return;
        }

        $this->flashMessageService->addMessage(\TCMSTableEditorManager::MESSAGE_MANAGER_CONSUMER, 'IMAGE_EDITOR_SUCCESS_NEW_IMAGE_HAS_BEEN_SAVED');
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
