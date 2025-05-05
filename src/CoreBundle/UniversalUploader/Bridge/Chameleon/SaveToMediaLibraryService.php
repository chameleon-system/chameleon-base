<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\UniversalUploader\Bridge\Chameleon;

use ChameleonSystem\CoreBundle\UniversalUploader\Exception\AccessDeniedException;
use ChameleonSystem\CoreBundle\UniversalUploader\Library\DataModel\UploadedFileDataModel;
use ChameleonSystem\CoreBundle\UniversalUploader\Library\DataModel\UploaderParametersDataModel;
use ChameleonSystem\CoreBundle\UniversalUploader\Library\UploaderParameterServiceInterface;
use ChameleonSystem\SecurityBundle\Voter\CmsPermissionAttributeConstants;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;

class SaveToMediaLibraryService implements SaveToMediaLibraryServiceInterface
{
    /**
     * @var UploaderParameterServiceInterface
     */
    private $uploadParameterService;

    /**
     * @var RequestStack
     */
    private $requestStack;
    private Security $security;

    public function __construct(UploaderParameterServiceInterface $uploadParameterService, RequestStack $requestStack, Security $security)
    {
        $this->uploadParameterService = $uploadParameterService;
        $this->requestStack = $requestStack;
        $this->security = $security;
    }

    /**
     * @return UploadedFileDataModel
     */
    public function saveUploadedFile(UploadedFileDataModel $uploadedFile)
    {
        $fileUploadData = [
            'name' => $uploadedFile->getOriginalClientFileName(),
            'type' => 'application/octet-stream',
            'tmp_name' => $uploadedFile->getTmpFilePath(),
            'error' => 0,
            'size' => $uploadedFile->getFileSize(),
        ];

        $parameters = $this->uploadParameterService->getParameters();

        $tableName = 'cms_media';
        if ('document' === $parameters->getMode()) {
            $tableName = 'cms_document';
        }

        $recordId = $parameters->getRecordID();

        $tableManager = \TTools::GetTableEditorManager($tableName, $recordId);
        /** @var \TCMSTableEditorFiles $tableEditor */
        $tableEditor = $tableManager->oTableEditor;

        $postData = [];

        $treeId = $parameters->getTreeNodeID();
        if (null !== $treeId) {
            if (UploaderParametersDataModel::PARAMETER_VALUE_MODE_MEDIA === $parameters->getMode()) {
                $postData['cms_media_tree_id'] = $treeId;
            }
            if (UploaderParametersDataModel::PARAMETER_VALUE_MODE_DOCUMENT === $parameters->getMode()) {
                $postData['cms_document_tree_id'] = $treeId;
            }
        }

        if (null !== $recordId) {
            $postData['id'] = $recordId;
            if (null !== $tableEditor->oTable && isset($tableEditor->oTable->sqlData['path']) && '' !== $tableEditor->oTable->sqlData['path']) {
                $postData['refresh_token'] = dechex(crc32((string) time()));
            }
        }

        $postData = array_merge($postData, $this->getAdditionalPostData());

        try {
            // fix until #39217 is resolved
            if (false === $this->security->isGranted(CmsPermissionAttributeConstants::TABLE_EDITOR_NEW, $tableName)) {
                throw new AccessDeniedException(sprintf('Permission for user %s to add new records to %s has not been granted.', $this->security->getUser()?->getUserIdentifier(), $tableName));
            }

            $tableEditor->SetUploadData($fileUploadData, true);
            $returnData = $tableManager->Save($postData);
            if (false !== $returnData) {
                $uploadedFile->setSavedRecordId($returnData->id);
            }
        } catch (\Exception $e) {
            $context = [
                'maxWidth' => $parameters->getMaxUploadWidth(),
                'maxHeight' => $parameters->getMaxUploadHeight(),
            ];
            $uploadedFile->addErrorMessage($e->getMessage(), (int) $e->getCode(), $context);
        }

        // notice: if your image does not show up in media manager, chances are, its cmsident is below 1000... historically, these are not shown

        return $uploadedFile;
    }

    /**
     * @return array
     */
    private function getAdditionalPostData()
    {
        $postData = [];

        $request = $this->requestStack->getCurrentRequest();

        $uploadName = $request->get('uploadname', null);
        if (null !== $uploadName) {
            $postData['uploadname'] = $uploadName;
        }

        $uploadDescription = $request->get('uploaddescription', null);
        if (null !== $uploadDescription) {
            $postData['uploaddescription'] = $uploadDescription;
        }

        return $postData;
    }
}
