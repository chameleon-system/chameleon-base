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

use ChameleonSystem\CoreBundle\UniversalUploader\Exception\UploaderConfigurationException;
use ChameleonSystem\CoreBundle\UniversalUploader\Library\DataModel\UploaderParametersDataModel;
use ChameleonSystem\CoreBundle\UniversalUploader\Library\UploaderConfigurationInterface;

class UploaderConfiguration implements UploaderConfigurationInterface
{
    /**
     * @var \TdbCmsConfig
     */
    private $cmsConfig;

    public function __construct(\TdbCmsConfig $config)
    {
        $this->cmsConfig = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function getMaxUploadSize($uploaderParameter)
    {
        $maxSizeMegaBytesFromServer = \TTools::getUploadMaxSize(); // MB
        $maxSizeKiloBytesFromServer = $maxSizeMegaBytesFromServer * 1024;

        switch ($uploaderParameter->getMode()) {
            case UploaderParametersDataModel::PARAMETER_VALUE_MODE_MEDIA:
                $maxSizeKiloBytesFromCmsConfig = $this->cmsConfig->fieldMaxImageUploadSize;
                break;
            case UploaderParametersDataModel::PARAMETER_VALUE_MODE_DOCUMENT:
                $maxSizeKiloBytesFromCmsConfig = $this->cmsConfig->fieldMaxDocumentUploadSize;
                break;
            default:
                throw new UploaderConfigurationException('Invalid mode.');
                break;
        }

        if ($maxSizeKiloBytesFromCmsConfig > $maxSizeKiloBytesFromServer) {
            return (int) $maxSizeKiloBytesFromServer;
        }

        return (int) $maxSizeKiloBytesFromCmsConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function getChunkSize()
    {
        return (int) $this->cmsConfig->fieldUploaderChunkSize;
    }

    /**
     * {@inheritdoc}
     *
     * @throws UploaderConfigurationException
     */
    public function getAllowedFileTypes($uploaderParameter)
    {
        switch ($uploaderParameter->getMode()) {
            case UploaderParametersDataModel::PARAMETER_VALUE_MODE_MEDIA:
                $fileTypes = $this->getAllowedFileTypesMedia($uploaderParameter->getRecordID());
                break;
            case UploaderParametersDataModel::PARAMETER_VALUE_MODE_DOCUMENT:
                $fileTypes = $this->getAllowedFileTypesDocument();
                break;
            default:
                throw new UploaderConfigurationException('Invalid mode.');
                break;
        }

        return $fileTypes;
    }

    /**
     * @param string|null $recordId
     *
     * @return array
     *
     * @throws UploaderConfigurationException
     */
    private function getAllowedFileTypesMedia($recordId)
    {
        $fileTypes = [];
        if (null !== $recordId) {
            $image = new \TCMSImage();
            if (!$image->Load($recordId)) {
                throw new UploaderConfigurationException('Image for recordID could not be loaded.');
            }
            $fileTypes[] = strtolower($image->GetImageType());
        } else {
            /**
             * @var \TCMSTableEditorMedia $tableEditor
             */
            $tableEditor = \TTools::GetTableEditorManager('cms_media')->oTableEditor;
            $fileTypes = $tableEditor->GetAllowedMediaTypes();
        }

        return $fileTypes;
    }

    /**
     * @return array
     */
    private function getAllowedFileTypesDocument()
    {
        return \TTools::GetCMSFileTypes();
    }

    /**
     * @return string
     */
    public function getUploadTmpDir()
    {
        return sys_get_temp_dir().DIRECTORY_SEPARATOR;
    }
}
